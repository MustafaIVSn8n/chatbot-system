<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use App\Events\AdminUserCreated;
use App\Events\AdminUserUpdated;
use App\Events\AdminUserDeleted;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        return view('super_admin.dashboard');
    }

    public function listAdmins()
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();
        return view('super_admin.users.index', compact('admins'));
    }

    public function createAdminForm()
    {
        $websites = Website::all();
        return view('super_admin.users.create', compact('websites'));
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $password = $request->password; // Store the plain text password

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
        ]);

        $user->assignRole('admin');

        $websiteIds = $request->input('website_ids', []);
        $websites = Website::whereIn('id', $websiteIds)->get();

        if ($request->has('website_ids')) {
            $user->adminWebsites()->sync($request->website_ids);
        }

        Event::dispatch(new AdminUserCreated($user, true, $password, $websites)); // Pass the password and websites to the event

        return redirect()->route('super_admin.users.list')->with('success', 'Admin user created successfully.');
    }

    public function assignWebsites()
    {
        return "Assign Websites";
    }

    public function editAdminForm($id)
    {
        $admin = User::find($id);
        $websites = Website::all();

        return view('super_admin.users.edit', compact('admin', 'websites'));
    }

    public function updateAdmin(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $admin = User::find($id);

        $admin->name = $request->name;
        $admin->email = $request->email;
        $passwordChanged = false;
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
            $passwordChanged = true;
        }
        $admin->save();

        if ($request->has('website_ids')) {
            $admin->adminWebsites()->sync($request->website_ids);
        } else {
            $admin->adminWebsites()->sync([]);
        }

        Event::dispatch(new AdminUserUpdated($admin, $passwordChanged));

        return redirect()->route('super_admin.users.list')->with('success', 'Admin updated successfully.');
    }

    public function destroyAdmin($id)
    {
        $admin = User::find($id);

        if (!$admin) {
            return redirect()->route('super_admin.users.list')->with('error', 'Admin not found.');
        }

        Event::dispatch(new AdminUserDeleted($admin));

        $admin->delete();

        return redirect()->route('super_admin.users.list')->with('success', 'Admin deleted successfully.');
    }
}
