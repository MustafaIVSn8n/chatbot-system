<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Website;
use Illuminate\Support\Facades\Auth;

class AdminWebsitesController extends Controller
{
    public function index()
    {
        // Current Admin user
        $admin = Auth::user();

        // Fetch the websites assigned to this admin (assuming 'adminWebsites' relationship)
        $websites = $admin->adminWebsites; // or $admin->websites if named differently

        // For each website, you can compute basic analytics, e.g. total chats
        // Let's do a simple example: We'll attach a 'total_chats' attribute to each website
        foreach ($websites as $website) {
            $website->total_chats = Chat::where('website_id', $website->id)->count();
            // You can add more stats, like $website->trial_scheduled, etc.
        }

        return view('admin.websites.index', compact('websites'));
    }
}


