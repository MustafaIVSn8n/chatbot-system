<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Website;
use Exception;

class WidgetChatController extends Controller
{
    public function startChat(Request $request)
    {
        try {
            Log::info('Starting chat validation', ['request_data' => $request->all()]);
            $validator = Validator::make($request->all(), [
                'website_id' => 'required|integer|exists:websites,id',
                'name'       => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $websiteId = $request->input('website_id');
            $website = Website::find($websiteId);

            if (!$website) {
                Log::error('Website not found', ['website_id' => $websiteId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid website ID.'
                ], 404);
            }
            if (!$website->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat service is unavailable.'
                ], 403);
            }

            $chat = Chat::create([
                'website_id'    => $website->id,
                'customer_name' => $request->input('name') ?? 'Visitor',
                'status'        => 'open',
            ]);

            $threadId = null;
            if ($website->api_key) {
                Log::info('Creating thread with OpenAI');
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $website->api_key,
                    'Content-Type'  => 'application/json',
                    'OpenAI-Beta'   => 'assistants=v2'
                ])->post('https://api.openai.com/v1/threads');

                if ($response->successful()) {
                    $threadId = $response->json()['id'] ?? null;
                    $chat->thread_id = $threadId;
                    $chat->save();
                    Log::info('Thread created successfully', ['thread_id' => $threadId]);
                } else {
                    Log::error('Error creating thread: '.$response->body(), [
                        'status' => $response->status(),
                        'body'   => $response->body()
                    ]);
                }
            }

            Log::info('Chat created', [
                'chat_id'      => $chat->id,
                'website_id'   => $website->id,
                'customer_name'=> $chat->customer_name,
                'thread_id'    => $chat->thread_id
            ]);

            return response()->json([
                'success'        => true,
                'chat_id'        => $chat->id,
                'agent_name'     => null,
                'agent_status'   => null,
                'welcome_message'=> $website->welcome_message ?? 'Welcome to our chat!'
            ], 201);
        } catch (Exception $e) {
            Log::error('Error creating chat: '.$e->getMessage(), [
                'exception'    => $e,
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the chat.'
            ], 500);
        }
    }

    public function storeMessage(Request $request, $chatId)
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string|max:5000',
            ]);

            $chat = Chat::find($chatId);
            if (!$chat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat not found.'
                ], 404);
            }
            if ($chat->status !== 'open') {
                return response()->json([
                    'success' => false,
                    'message' => 'This chat is no longer active.'
                ], 403);
            }

            $website = Website::find($chat->website_id);
            if (!$website) {
                return response()->json([
                    'success' => false,
                    'message' => 'Website not found for this chat.'
                ], 404);
            }
            $apiKey = $website->api_key;

            $message = Message::create([
                'chat_id'     => $chat->id,
                'content'     => $validated['message'],
                'sender_type' => 'customer',
                'is_read'     => false,
            ]);
            $chat->update(['last_activity_at' => now()]);

            Log::info('Customer message saved', ['message_id' => $message->id, 'chat_id' => $chat->id]);

            $aiMessage = null;
            if (!$chat->human_transfer_requested && $website->assistant_id && $apiKey && $chat->thread_id) {
                try {
                    $addMsgResponse = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type'  => 'application/json',
                        'OpenAI-Beta'   => 'assistants=v2'
                    ])->post("https://api.openai.com/v1/threads/{$chat->thread_id}/messages", [
                        'role'    => 'user',
                        'content' => $validated['message'],
                    ]);

                    if (!$addMsgResponse->successful()) {
                        Log::error('Failed to add user msg to thread', ['response' => $addMsgResponse->body()]);
                    } else {
                        $runResponse = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $apiKey,
                            'Content-Type'  => 'application/json',
                            'OpenAI-Beta'   => 'assistants=v2'
                        ])->post("https://api.openai.com/v1/threads/{$chat->thread_id}/runs", [
                            'assistant_id' => $website->assistant_id,
                        ]);

                        if (!$runResponse->successful()) {
                            Log::error('Failed to run assistant', ['response' => $runResponse->body()]);
                        } else {
                            $runId = $runResponse->json()['id'] ?? null;
                            Log::info('Assistant run started', ['run_id' => $runId]);

                            $startTime = time();
                            $maxWait   = 15;
                            $aiResponseText = null;

                            while (true) {
                                if (time() - $startTime > $maxWait) {
                                    Log::warning('Timeout waiting for AI response');
                                    break;
                                }

                                $runStatusResponse = Http::withHeaders([
                                    'Authorization' => 'Bearer ' . $apiKey,
                                    'Content-Type'  => 'application/json',
                                    'OpenAI-Beta'   => 'assistants=v2'
                                ])->get("https://api.openai.com/v1/threads/{$chat->thread_id}/runs/{$runId}");

                                if ($runStatusResponse->successful()) {
                                    $runStatus = $runStatusResponse->json()['status'] ?? 'unknown';
                                    Log::info('Run status checked', ['status' => $runStatus]);

                                    if ($runStatus === 'completed') {
                                        $messagesResponse = Http::withHeaders([
                                            'Authorization' => 'Bearer ' . $apiKey,
                                            'Content-Type'  => 'application/json',
                                            'OpenAI-Beta'   => 'assistants=v2'
                                        ])->get("https://api.openai.com/v1/threads/{$chat->thread_id}/messages", [
                                            'limit' => 1,
                                            'order' => 'desc',
                                        ]);

                                        if ($messagesResponse->successful()) {
                                            $msgData = $messagesResponse->json()['data'][0] ?? null;
                                            if ($msgData && ($msgData['role'] === 'assistant') && !empty($msgData['content'])) {
                                                $aiResponseText = $msgData['content'][0]['text']['value'] ?? null;
                                            } else {
                                                Log::warning('No assistant message found after run', ['response' => $messagesResponse->json()]);
                                            }
                                        } else {
                                            Log::error('Error retrieving messages after run', ['resp' => $messagesResponse->body()]);
                                        }
                                        break;
                                    } elseif (in_array($runStatus, ['failed','cancelled','expired'])) {
                                        Log::error('Run failed or cancelled', ['status' => $runStatus]);
                                        break;
                                    }
                                } else {
                                    Log::error('Error checking run status', ['resp' => $runStatusResponse->body()]);
                                    break;
                                }

                                sleep(1);
                            }

                            if ($aiResponseText) {
                                $aiMessage = Message::create([
                                    'chat_id'     => $chat->id,
                                    'content'     => $aiResponseText,
                                    'sender_type' => 'ai',
                                    'is_read'     => true,
                                ]);
                                Log::info('AI response saved', [
                                    'ai_message_id' => $aiMessage->id,
                                    'content'       => $aiResponseText
                                ]);
                            }
                        }
                    }
                } catch (Exception $ex) {
                    Log::error('Exception running AI logic: ' . $ex->getMessage());
                }
            } else if ($chat->human_transfer_requested) {
                Log::info('Chat transferred to human, skipping AI response', ['chat_id' => $chat->id]);
            } else {
                Log::info('No assistant configured for this website or no thread_id');
            }

            $responseData = [
                'success' => true,
                'user_message' => $message,
            ];
            if ($aiMessage) {
                $responseData['ai_message'] = $aiMessage;
            }

            return response()->json($responseData, 201);

        } catch (Exception $e) {
            Log::error('Error storing message: '.$e->getMessage(), [
                'exception'   => $e,
                'chat_id'     => $chatId,
                'request_data'=> $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while storing the message / AI response.'
            ], 500);
        }
    }

    public function getMessages(Request $request, $chatId)
    {
        try {
            $chat = Chat::find($chatId);
            if (!$chat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat not found.'
                ], 404);
            }

            $messages = Message::where('chat_id', $chat->id)
                               ->orderBy('created_at','asc')
                               ->get();

            return response()->json([
                'success'  => true,
                'messages' => $messages
            ], 200);

        } catch (Exception $e) {
            Log::error('Error getting messages: ' . $e->getMessage(), [
                'exception' => $e,
                'chat_id'   => $chatId
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error getting messages'
            ], 500);
        }
    }

    public function transferToHuman(Request $request, $chatId)
    {
        try {
            $chat = Chat::findOrFail($chatId);
            if ($chat->human_transfer_requested) {
                return response()->json([
                    'success' => true,
                    'message' => 'Chat is already transferred to human support.',
                ], 200);
            }

            $chat->human_transfer_requested = true;
            $chat->save();

            $systemMessage = Message::create([
                'chat_id'     => $chat->id,
                'content'     => 'Chat transferred to human support.',
                'sender_type' => 'ai',
                'is_read'     => true,
            ]);

            Log::info('Chat transferred to human', ['chat_id' => $chat->id]);

            return response()->json([
                'success' => true,
                'message' => 'Chat transferred to human support.',
                'system_message' => $systemMessage,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error transferring chat: ' . $e->getMessage(), ['chat_id' => $chatId]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to transfer chat.',
            ], 500);
        }
    }
}