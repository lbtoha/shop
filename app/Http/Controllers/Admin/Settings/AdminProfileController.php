<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    public function index(Request $request)
    {
        adminUserHasPermission(permission: 'read');

        $user = auth('admin')->user();

        return view('admin.pages.profile-edit', compact('user'));
    }

    public function update(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        /**
         * @var \App\Models\Admin $user
         */
        $user = auth('admin')->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,'.$user->id,
            'phone' => 'required|string|max:255|unique:admins,phone,'.$user->id,
            'avatar' => 'nullable|string',
        ]);

        $user->update($validated);

        return response()->json(['success' => __('Profile updated successfully')]);
    }

    public function changePassword(Request $request)
    {
        adminUserHasPermission(permission: 'edit');
        $validated = $request->validate([
            'old_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        /**
         * @var \App\Models\Admin $user
         */
        $user = auth('admin')->user();

        if (! Hash::check($validated['old_password'], $user->password)) {
            return redirect()->back()->with('error', __('Old password does not match'));
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['success' => __('Password updated successfully'), 'reload' => true]);
    }
}
