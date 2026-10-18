<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\User;
use App\Notifications\ProjectInvitationNotification;
use App\Services\ProjectActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProjectInvitationController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('inviteMember', $project);

        $validated = $request->validate(
            [
                'username' => ['required', 'string', 'exists:users,username'],
            ],
            [
                'username.required' => 'لطفاً نام کاربری کاربر را وارد کنید.',
                'username.string' => 'نام کاربری معتبر وارد کنید.',
                'username.exists' => 'کاربری با این نام کاربری پیدا نشد.',
            ]
        );

        $user = User::where('username', $validated['username'])->firstOrFail();

        if ($user->id === $project->owner_id) {
            return back()->withErrors([
                'username' => 'صاحب پروژه از قبل عضو این پروژه است.',
            ])->withInput();
        }

        if ($project->members()->where('users.id', $user->id)->exists()) {
            return back()->withErrors([
                'username' => 'این کاربر از قبل عضو پروژه است.',
            ])->withInput();
        }

        $existingInvitation = ProjectInvitation::where('project_id', $project->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingInvitation) {
            return back()->withErrors([
                'username' => 'برای این کاربر قبلاً یک دعوت‌نامه در انتظار وجود دارد.',
            ])->withInput();
        }

        $invitation = ProjectInvitation::create([
            'project_id' => $project->id,
            'invited_by' => auth()->id(),
            'user_id' => $user->id,
            'token' => Str::random(64),
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        $user->notify(
            new ProjectInvitationNotification($invitation)
        );

        ProjectActivityLogger::log(
            $project,
            'member.invited',
            'دعوت شد ' . $user->username . ' به پروژه'
        );

        return back()->with('success', 'دعوت‌نامه با موفقیت ارسال شد.');
    }

    public function accept(ProjectInvitation $invitation): RedirectResponse
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }

        if ($invitation->status !== 'pending') {
            return redirect()
                ->route('projects.index')
                ->withErrors([
                    'invitation' => 'This invitation is no longer available.',
                ]);
        }

        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            $invitation->update([
                'status' => 'rejected',
            ]);

            return redirect()
                ->route('projects.index')
                ->withErrors([
                    'invitation' => 'This invitation has expired.',
                ]);
        }

        $project = $invitation->project;

        if (!$project->members()->where('users.id', auth()->id())->exists()) {
            $project->members()->attach(auth()->id(), [
                'role' => 'member',
            ]);
        }

        $invitation->update([
            'status' => 'accepted',
        ]);

        auth()->user()
            ->notifications()
            ->where('data->invitation_id', $invitation->id)
            ->get()
            ->each
            ->markAsRead();

        if (!$project->members()->where('users.id', auth()->id())->exists()) {
            $project->members()->attach(auth()->id(), [
                'role' => 'member',
            ]);

            ProjectActivityLogger::log(
                $project,
                'member.joined',
                auth()->user()->name . ' به پروژه پیوست'
            );
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'You joined the project successfully.');
    }

    public function reject(ProjectInvitation $invitation): RedirectResponse
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }

        if ($invitation->status !== 'pending') {
            return redirect()
                ->route('projects.index')
                ->withErrors([
                    'invitation' => 'This invitation is no longer available.',
                ]);
        }

        $invitation->update([
            'status' => 'rejected',
        ]);

        auth()->user()
            ->notifications()
            ->where('data->invitation_id', $invitation->id)
            ->get()
            ->each
            ->markAsRead();

        return redirect()
            ->route('projects.index')
            ->with('success', 'You rejected the project invitation.');
    }

    public function index(): View
    {
        $invitations = ProjectInvitation::with([
            'project',
            'inviter',
        ])
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('invitations.index', compact('invitations'));
    }

    public function destroy(Project $project, User $user): RedirectResponse
    {
        $this->authorize('removeMember', [$project, $user]);

        if ($user->id === $project->owner_id) {
            return back()->withErrors([
                'member' => 'The project owner cannot be removed.',
            ]);
        }

        if (!$project->members()->where('users.id', $user->id)->exists()) {
            return back()->withErrors([
                'member' => 'This user is not a member of the project.',
            ]);
        }

        $project->members()->detach($user->id);

        ProjectActivityLogger::log(
            $project,
            'member.removed',
            $user->name . ' از پروژه حذف شد'
        );

        return back()->with('success', 'Member removed successfully.');
    }

    public function changeMemberRole(
        Request $request,
        Project $project,
        User $user
    ): RedirectResponse {
        $this->authorize('changeMemberRole', [$project, $user]);

        $validated = $request->validate([
            'role' => ['required', 'in:member,manager'],
        ]);

        if (!$project->members()->where('users.id', $user->id)->exists()) {
            return back()->withErrors([
                'member' => 'This user is not a member of the project.',
            ]);
        }

        $project->members()->updateExistingPivot($user->id, [
            'role' => $validated['role'],
        ]);

        $roleLabel = $validated['role'] === 'manager' ? 'مدیر' : 'عضو';

        ProjectActivityLogger::log(
            $project,
            'member.role_changed',
            $user->name . ' نقش ' . $roleLabel . ' را تغییر داد'
        );

        return back()->with('success', 'Member role updated successfully.');
    }
}