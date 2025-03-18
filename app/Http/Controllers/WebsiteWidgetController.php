<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Models\WidgetButton;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WebsiteWidgetController extends Controller
{
    /**
     * Show the form for editing the widget settings.
     *
     * @param int $websiteId
     * @return \Illuminate\View\View
     */
    public function edit($websiteId)
    {
        $admin = Auth::user();
        $website = $admin->adminWebsites()->findOrFail($websiteId);
        $buttons = $website->buttons()->orderBy('display_order')->get();
        
        return view('admin.websites.widget', compact('website', 'buttons'));
    }
    
    /**
     * Update the welcome message.
     *
     * @param Request $request
     * @param int $websiteId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateWelcomeMessage(Request $request, $websiteId)
    {
        $admin = Auth::user();
        $website = $admin->adminWebsites()->findOrFail($websiteId);
        
        $validator = Validator::make($request->all(), [
            'welcome_message' => 'required|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $website->update([
            'welcome_message' => $request->welcome_message,
        ]);
        
        return back()->with('success', 'Welcome message updated successfully.');
    }
    
    /**
     * Store a new button.
     *
     * @param Request $request
     * @param int $websiteId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeButton(Request $request, $websiteId)
    {
        $admin = Auth::user();
        $website = $admin->adminWebsites()->findOrFail($websiteId);
        
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:50',
            'action_type' => 'required|in:message,link',
            'action_value' => 'required|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // Get the highest display order
        $maxOrder = $website->buttons()->max('display_order') ?? 0;
        
        WidgetButton::create([
            'website_id' => $website->id,
            'text' => $request->text,
            'action_type' => $request->action_type,
            'action_value' => $request->action_value,
            'display_order' => $maxOrder + 1,
            'is_active' => true,
        ]);
        
        return back()->with('success', 'Button added successfully.');
    }
    
    /**
     * Update a button.
     *
     * @param Request $request
     * @param int $websiteId
     * @param int $buttonId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateButton(Request $request, $websiteId, $buttonId)
    {
        $admin = Auth::user();
        $website = $admin->adminWebsites()->findOrFail($websiteId);
        $button = $website->buttons()->findOrFail($buttonId);
        
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:50',
            'action_type' => 'required|in:message,link',
            'action_value' => 'required|string|max:1000',
            'is_active' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $button->update([
            'text' => $request->text,
            'action_type' => $request->action_type,
            'action_value' => $request->action_value,
            'is_active' => $request->has('is_active'),
        ]);
        
        return back()->with('success', 'Button updated successfully.');
    }
    
    /**
     * Delete a button.
     *
     * @param int $websiteId
     * @param int $buttonId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyButton($websiteId, $buttonId)
    {
        $admin = Auth::user();
        $website = $admin->adminWebsites()->findOrFail($websiteId);
        $button = $website->buttons()->findOrFail($buttonId);
        
        $button->delete();
        
        // Reorder remaining buttons
        $remainingButtons = $website->buttons()->orderBy('display_order')->get();
        foreach ($remainingButtons as $index => $btn) {
            $btn->update(['display_order' => $index + 1]);
        }
        
        return back()->with('success', 'Button deleted successfully.');
    }
    
    /**
     * Update button order.
     *
     * @param Request $request
     * @param int $websiteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorderButtons(Request $request, $websiteId)
    {
        $admin = Auth::user();
        $website = $admin->adminWebsites()->findOrFail($websiteId);
        
        $validator = Validator::make($request->all(), [
            'buttons' => 'required|array',
            'buttons.*' => 'integer|exists:widget_buttons,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid button order.'], 422);
        }
        
        $buttonIds = $request->buttons;
        
        foreach ($buttonIds as $index => $buttonId) {
            $button = WidgetButton::find($buttonId);
            if ($button && $button->website_id == $website->id) {
                $button->update(['display_order' => $index + 1]);
            }
        }
        
        return response()->json(['success' => true]);
    }
}
