<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WidgetWelcomeController extends Controller
{
    /**
     * Get the welcome message and buttons for a website.
     *
     * @param int $websiteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWelcomeMessage($websiteId)
    {
        try {
            // Find the website
            $website = Website::findOrFail($websiteId);
            
            // Get active buttons
            $buttons = $website->buttons()
                ->where('is_active', true)
                ->orderBy('display_order')
                ->get(['id', 'text', 'action_type', 'action_value', 'is_active']);
            
            return response()->json([
                'success' => true,
                'welcome_message' => $website->welcome_message,
                'buttons' => $buttons
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving welcome message: ' . $e->getMessage(), [
                'website_id' => $websiteId,
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve welcome message.'
            ], 500);
        }
    }
}
