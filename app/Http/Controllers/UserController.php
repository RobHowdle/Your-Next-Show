<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function edit(User $user)
    {
        $roles = Role::all();
        $systemMailingPrefs = config('mailing_preferences.communication_preferences');

        return view('admin.users.edit', compact('user', 'roles', 'systemMailingPrefs'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        // Get validated data
        $validated = $request->validated();

        // Update user
        $user->update($validated);

        // Handle mailing preferences
        if (isset($validated['mailing_preferences'])) {
            $user->mailing_preferences = $validated['mailing_preferences'];
            $user->save();
        }

        // Handle roles if present
        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }

    public function resendVerification(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return redirect()->back()->with('error', 'User is already verified');
        }

        $user->sendEmailVerificationNotification();
        return redirect()->back()->with('success', 'Verification email sent');
    }
}