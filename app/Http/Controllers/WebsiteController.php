<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;

class WebsiteController extends Controller
{
    /**
     * Display a listing of the websites.
     */
    public function index()
    {
        $websites = Website::all();
        return view('super_admin.websites.index', compact('websites'));
    }

    /**
     * Show the form for creating a new website.
     */
    public function create()
    {
        return view('super_admin.websites.create');
    }

    /**
     * Store a newly created website in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'url'             => 'required|url',
            'api_key'         => 'required|string',
            'assistant_id'    => 'required|string',
            'model_name'      => 'required|string',
            'description'     => 'nullable|string',
            'widget_color'    => 'nullable|string',
            'widget_position' => 'required|string',
            'website_type'    => 'required|string',
        ]);

        Website::create($validated);

        return redirect()->route('super_admin.websites.index')
                         ->with('success', 'Website added successfully.');
    }

    /**
     * Display the specified website.
     */
    public function show(Website $website)
    {
        return view('super_admin.websites.show', compact('website'));
    }

    /**
     * Show the form for editing the specified website.
     */
    public function edit(Website $website)
    {
        return view('super_admin.websites.edit', compact('website'));
    }

    /**
     * Update the specified website in storage.
     */
    public function update(Request $request, Website $website)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'url'             => 'required|url',
            'api_key'         => 'required|string',
            'assistant_id'    => 'required|string',
            'model_name'      => 'required|string',
            'description'     => 'nullable|string',
            'widget_color'    => 'nullable|string',
            'widget_position' => 'required|string',
            'website_type'    => 'required|string',
        ]);

        $website->update($validated);

        return redirect()->route('super_admin.websites.index')
                         ->with('success', 'Website updated successfully.');
    }

    /**
     * Remove the specified website from storage.
     */
    public function destroy(Website $website)
    {
        $website->delete();
        return redirect()->route('super_admin.websites.index')
                         ->with('success', 'Website deleted successfully.');
    }
}
