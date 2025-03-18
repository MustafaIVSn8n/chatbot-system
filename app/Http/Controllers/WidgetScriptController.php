<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WidgetScriptController extends Controller
{
    /**
     * Generate the widget script for a specific website.
     *
     * @param string $websiteId
     * @return \Illuminate\Http\Response
     */
    public function generateScript($websiteId)
    {
        try {
            // Find the website by ID
            $website = Website::findOrFail($websiteId);
            
            // Set default values if not provided
            $widgetColor = $website->widget_color ?? '#563d7c';
            $widgetPosition = $website->widget_position ?? 'bottom-right';
            
            // Set the content type to JavaScript
            return response()
                ->view('widget.script', [
                    'website' => $website,
                    'widgetColor' => $widgetColor,
                    'widgetPosition' => $widgetPosition
                ])
                ->header('Content-Type', 'application/javascript');
        } catch (\Exception $e) {
            Log::error('Error generating widget script: ' . $e->getMessage(), [
                'website_id' => $websiteId,
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate widget script.'
            ], 500);
        }
    }
    
    /**
     * Generate the embed code for a specific website.
     *
     * @param int $websiteId
     * @return \Illuminate\Http\Response
     */
    public function getEmbedCode($websiteId)
    {
        try {
            // Find the website by ID
            $website = Website::findOrFail($websiteId);
            
            // Generate the embed code
            $embedCode = '<script src="' . route('widget.script', ['websiteId' => $website->id]) . '" defer></script>';
            
            return response()->json([
                'success' => true,
                'embedCode' => $embedCode
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating embed code: ' . $e->getMessage(), [
                'website_id' => $websiteId,
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate embed code.'
            ], 500);
        }
    }
}
