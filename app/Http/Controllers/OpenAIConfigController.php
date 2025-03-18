<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OpenAIConfigController extends Controller
{
    /**
     * Show the form for editing the OpenAI configuration.
     */
    public function edit()
    {
        // Return the view for editing the configuration.
        return view('super_admin.openai_config.edit');
    }

    /**
     * Update the OpenAI configuration.
     */
    public function update(Request $request)
    {
        // Validate the request data.
        $validated = $request->validate([
            'api_key'      => 'required|string',
            'assistant_id' => 'required|string',
            'model_name'   => 'required|string',
        ]);

        // Save the configuration (you might update a settings table or .env file)
        // For now, simply redirect back with a success message.
        return redirect()->back()->with('success', 'OpenAI configuration updated successfully.');
    }
}