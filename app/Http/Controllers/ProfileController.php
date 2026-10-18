<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'notify_email'          => ['sometimes', 'boolean'],
            'notify_task_assigned'  => ['sometimes', 'boolean'],
            'notify_due_reminders'  => ['sometimes', 'boolean'],
            'notify_weekly_digest'  => ['sometimes', 'boolean'],
        ]);

        $request->user()->update([
            'notify_email'         => $request->boolean('notify_email'),
            'notify_task_assigned' => $request->boolean('notify_task_assigned'),
            'notify_due_reminders' => $request->boolean('notify_due_reminders'),
            'notify_weekly_digest' => $request->boolean('notify_weekly_digest'),
        ]);

        return back()->with('status', 'notifications-updated');
    }
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }


    
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $oldAvatar = $user->avatar;

            $newAvatar = $request->file('avatar')->store('avatars', 'public');

            if ($oldAvatar && Storage::disk('public')->exists($oldAvatar)) {
                Storage::disk('public')->delete($oldAvatar);
            }

            $user->avatar = $newAvatar;
        }

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
