<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AIAssistant;
use Exception;

class AIAssistantController extends Controller
{
    /**
     * Display a listing of the AI assistants.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('super_admin.ai_assistants.index');
    }

    /**
     * Get all assistants via API.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssistants(Request $request)
    {
        try {
            $apiKey = $request->input('api_key');
            
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key is required'
                ], 400);
            }
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'OpenAI-Beta' => 'assistants=v1'
            ])->get('https://api.openai.com/v1/assistants');
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'assistants' => $response->json()['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $response->json()['error']['message'] ?? 'Failed to fetch assistants'
                ], $response->status());
            }
        } catch (Exception $e) {
            Log::error('Error fetching assistants: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching assistants: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new assistant via API.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAssistant(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'api_key' => 'required_without:assistant_id|string',
                'name' => 'required_without:assistant_id|string|max:255',
                'model' => 'required_without:assistant_id|string',
                'instructions' => 'required_without:assistant_id|string',
                'assistant_id' => 'nullable|string',
                'description' => 'nullable|string|max:512',
            ]);
            if ($request->filled('assistant_id')) {
               // Link existing assistant
               $assistantId = $validated['assistant_id'];
               $assistant = [
                   'id' => $assistantId,
                   'name' => $validated['name'] ?? null,
                   'model' => $validated['model'] ?? null,
                   'instructions' => $validated['instructions'] ?? null,
                   'description' => $validated['description'] ?? null,
               ];
           } else {
               // Create the assistant via OpenAI API
               $response = Http::withHeaders([
                   'Authorization' => 'Bearer ' . $validated['api_key'],
                   'Content-Type' => 'application/json',
                   'OpenAI-Beta' => 'assistants=v1'
               ])->post('https://api.openai.com/v1/assistants', [
                   'name' => $validated['name'],
                   'model' => $validated['model'],
                   'instructions' => $validated['instructions'],
                   'description' => $validated['description'] ?? null,
               ]);

               if ($response->successful()) {
                   $assistant = $response->json();
               } else {
                   return response()->json([
                       'success' => false,
                       'message' => $response->json()['error']['message'] ?? 'Failed to create assistant'
                   ], $response->status());
               }
           }

            // Store the assistant in the database for future reference
            AIAssistant::create([
                'assistant_id' => $assistant['id'],
                'name' => $assistant['name'],
                'model' => $assistant['model'],
                'instructions' => $assistant['instructions'],
                'description' => $assistant['description'] ?? null,
            ]);
        
            return response()->json([
                'success' => true,
                'assistant' => $assistant
            ], 201);
        } catch (Exception $e) {
            Log::error('Error creating assistant: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the assistant: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an assistant via API.
     *
     * @param Request $request
     * @param string $assistantId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAssistant(Request $request, $assistantId)
    {
        try {
            $apiKey = $request->input('api_key');
            
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key is required'
                ], 400);
            }
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'OpenAI-Beta' => 'assistants=v1'
            ])->delete('https://api.openai.com/v1/assistants/' . $assistantId);
            
            if ($response->successful()) {
                // Remove from database if it exists
                AIAssistant::where('assistant_id', $assistantId)->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Assistant deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $response->json()['error']['message'] ?? 'Failed to delete assistant'
                ], $response->status());
            }
        } catch (Exception $e) {
            Log::error('Error deleting assistant: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the assistant: ' . $e->getMessage()
            ], 500);
        }
    }
}
