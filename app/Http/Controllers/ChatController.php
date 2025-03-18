<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function show(Chat $chat)
    {
        \Log::info('ChatController::show called', ['chat_id' => $chat->id]);
        $admin = Auth::user();
        $websiteIds = $admin->adminWebsites->pluck('id');
        \Log::info('Admin website IDs', ['websiteIds' => $websiteIds]);

        if (! $websiteIds->contains($chat->website_id)) {
            \Log::error('Unauthorized chat access', ['chat_id' => $chat->id, 'website_id' => $chat->website_id]);
            abort(403, 'Unauthorized chat access.');
        }

        $chat->load('messages');
        \Log::info('Chat messages loaded', ['chat_id' => $chat->id, 'message_count' => $chat->messages->count()]);

        $needsHuman = $chat->human_transfer_requested; // Changed from needs_human

        \Log::info('Returning admin chat view', ['chat_id' => $chat->id]);
        return view('admin.chats.show', compact('chat', 'needsHuman'));
    }

    public function update(Request $request, Chat $chat)
    {
        $admin = Auth::user();
        $websiteIds = $admin->adminWebsites->pluck('id');
        if (! $websiteIds->contains($chat->website_id)) {
            abort(403, 'Unauthorized chat access.');
        }

        $request->validate([
            'status' => 'required|string|in:open,closed,trial_scheduled'
        ]);

        $chat->status = $request->status;
        $chat->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Chat status updated successfully.',
                'status' => $chat->status
            ]);
        }

        return redirect()->back()->with('success', 'Chat status updated successfully.');
    }

    public function sendMessage(Request $request, Chat $chat)
    {
        \Log::info('ChatController::sendMessage called', [
            'chat_id' => $chat->id,
            'request_data' => $request->all(),
            'chat_data' => $chat->toArray()
        ]);

        $admin = Auth::user();
        $websiteIds = $admin->adminWebsites->pluck('id');
        if (! $websiteIds->contains($chat->website_id)) {
            abort(403, 'Unauthorized chat access.');
        }

        $rules = ['content' => 'required|string'];
        \Log::info('Validation rules', ['rules' => $rules]);

        try {
            $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'chat_data' => $chat->toArray()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message due to a validation error.',
                'errors' => $e->errors()
            ], 422);
        }

        $message = new Message();
        $message->chat_id = $chat->id;
        $message->content = (string) $request->content;
        $message->sender_type = 'admin';
        $message->user_id = Auth::id();
        $message->is_read = true;
        try {
            $message->save();
            \Log::info('Message saved successfully', ['message_id' => $message->id]);
        } catch (\Exception $e) {
            \Log::error('Error saving message', [
                'message_id' => $message->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message due to a database error.',
            ], 500);
        }

        Message::where('chat_id', $chat->id)
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data' => [
                'id' => $message->id,
                'content' => $message->content,
                'sender_type' => $message->sender_type,
                'created_at' => $message->created_at->format('H:i'),
                'user_name' => optional($message->user)->name
            ]
        ]);
    }
    
    public function index(Request $request)
    {
        $admin = Auth::user();
        $websiteIds = $admin->adminWebsites->pluck('id');

        $query = Chat::whereIn('website_id', $websiteIds);

        if ($request->has('website_id')) {
            $query->where('website_id', $request->input('website_id'));
        }

        $chats = $query->orderBy('created_at', 'desc')->with('messages')->get();

        return view('admin.chats.index', compact('chats'));
    }
    
    public function getMessages(Request $request, Chat $chat)
    {
        $admin = Auth::user();
        $websiteIds = $admin->adminWebsites->pluck('id');
        if (! $websiteIds->contains($chat->website_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized chat access.'
            ], 403);
        }

        $query = $chat->messages();
        
        if ($request->has('after')) {
            $query->where('created_at', '>', $request->input('after'));
        }
        
        $messages = $query->orderBy('created_at', 'asc')->get();
        
        $formattedMessages = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'content' => $message->content,
                'sender_type' => $message->sender_type,
                'user_name' => optional($message->user)->name,
                'created_at' => $message->created_at->format('H:i'),
                'is_read' => $message->is_read
            ];
        });
        
        return response()->json([
            'success' => true,
            'messages' => $formattedMessages
        ]);
    }

    public function getChatList(Request $request)
    {
        \Log::info('ChatController::getChatList called', ['request_data' => $request->all()]);
        
        $admin = Auth::user();
        $websiteIds = $admin->adminWebsites->pluck('id');
        
        $query = Chat::whereIn('website_id', $websiteIds);
        
        if ($request->has('website_id')) {
            $query->where('website_id', $request->input('website_id'));
        }
        
        $chats = $query->orderBy('last_activity_at', 'desc')
                       ->with(['messages' => function($query) {
                           $query->latest()->take(1);
                       }])
                       ->get();
        
        $formattedChats = $chats->map(function($chat) {
            $lastMessage = $chat->messages->first();
            
            return [
                'id' => $chat->id,
                'customer_name' => $chat->customer_name,
                'status' => $chat->status,
                'needs_human' => $chat->human_transfer_requested, // Changed from needs_human
                'last_activity_at' => $chat->last_activity_at,
                'last_message' => $lastMessage ? \Illuminate\Support\Str::limit($lastMessage->content, 30) : null,
                'unread_count' => $chat->messages->where('is_read', false)->where('sender_type', 'customer')->count()
            ];
        });
        
        \Log::info('Returning chat list', ['count' => $formattedChats->count()]);
        
        return response()->json([
            'success' => true,
            'chats' => $formattedChats
        ]);
    }
}