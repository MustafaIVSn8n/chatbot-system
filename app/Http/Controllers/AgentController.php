<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agents = User::whereHas('roles', function ($query) {
            $query->where('name', 'agent');
        })->get();

        return view('admin.agents.index', compact('agents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $admin = Auth::user();
        $websites = $admin->adminWebsites;
        return view('admin.agents.create', compact('websites'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $requestData)
    {
        $requestData->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $requestData->name,
            'email' => $requestData->email,
            'password' => Hash::make($requestData->password),
        ]);

        $user->assignRole('agent');

        return redirect()->route('admin.agents.index')->with('success', 'Agent created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Not used
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $agent = User::findOrFail($id);
        $admin = Auth::user();
        $websites = $admin->adminWebsites;
        return view('admin.agents.edit', compact('agent', 'websites'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $requestData, string $id)
    {
        $requestData->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $agent = User::findOrFail($id);

        $agent->name = $requestData->name;
        $agent->email = $requestData->email;
        if ($requestData->filled('password')) {
            $agent->password = Hash::make($requestData->password);
        }
        $agent->save();

        if ($requestData->has('website_ids')) {
            $agent->adminWebsites()->sync($requestData->website_ids);
        } else {
            $agent->adminWebsites()->sync([]);
        }

        return redirect()->route('admin.agents.index')
                         ->with('success', 'Agent updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $agent = User::findOrFail($id);
        $agent->delete();

        return redirect()->route('admin.agents.index')
                         ->with('success', 'Agent deleted successfully.');
    }
}
