<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function dashboard()
    {
        $admin = Auth::user();
        $chats = Chat::whereIn('website_id', $admin->adminWebsites->pluck('id'))
                     ->orderBy('created_at', 'desc')
                     ->get();

        return view('admin.dashboard', compact('chats'));
    }
}
