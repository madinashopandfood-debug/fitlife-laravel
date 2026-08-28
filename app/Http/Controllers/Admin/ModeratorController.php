<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * Admin-only: manage moderator accounts. Route is gated by
 * middleware('role:admin') so moderators can never reach these actions.
 */
class ModeratorController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        return view('admin.moderators.index', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'in:admin,moderator'],
        ]);

        $tempPassword = Str::password(12);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($tempPassword),
            'must_change_password' => true,
            'is_active' => true,
        ]);

        ActivityLog::record(Auth::id(), 'moderator.created', null, "Created {$data['role']} account for {$data['email']}");

        return back()->with('success', "Account created for {$data['email']}. Temporary password: {$tempPassword}");
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['required', 'in:admin,moderator'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->update([
            'name' => $data['name'],
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLog::record(Auth::id(), 'moderator.updated', null, "Updated account {$user->email}");

        return back()->with('success', 'Account updated.');
    }

    public function resetPassword(User $user)
    {
        $tempPassword = Str::password(12);
        $user->update([
            'password' => Hash::make($tempPassword),
            'must_change_password' => true,
        ]);

        ActivityLog::record(Auth::id(), 'moderator.password_reset', null, "Reset password for {$user->email}");

        return back()->with('success', "New temporary password for {$user->email}: {$tempPassword}");
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        ActivityLog::record(Auth::id(), 'moderator.deleted', null, "Deleted account {$user->email}");
        $user->delete();

        return back()->with('success', 'Account deleted.');
    }
}
