<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $search = trim((string) $request->query('search', ''));

        $sharedProjects = function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('projects.owner_id', $user->id)
                    ->orWhereHas('members', function ($members) use ($user) {
                        $members->where('users.id', $user->id);
                    });
            });
        };

        $teams = User::query()
            ->where('users.id', '!=', $user->id)
            ->whereHas('projects', $sharedProjects)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%");
                });
            })
            ->withCount([
                'projects as shared_projects_count' => $sharedProjects,
            ])
            ->orderBy('users.name')
            ->get();

        $pendingInvites = ProjectInvitation::query()
            ->where('invited_by', $user->id)
            ->where('status', 'pending')
            ->count();

        $ownedProjects = $user->ownedProjects()
            ->orderBy('name')
            ->get();

        return view('teams.index', compact(
            'teams',
            'pendingInvites',
            'ownedProjects',
            'search'
        ));
    }

    public function addToProject(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        abort_if($user->id === $currentUser->id, 422, 'نمی‌توانید خودتان را اضافه کنید.');

        $validated = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
        ]);

        // فقط پروژه‌هایی که متعلق به کاربر فعلی هستند مجازند.
        $project = $currentUser->ownedProjects()
            ->findOrFail($validated['project_id']);

        // فقط افرادی که هم‌اکنون عضو تیم کاربر هستند.
        $isTeamMember = $user->projects()
            ->where(function ($query) use ($currentUser) {
                $query->where('projects.owner_id', $currentUser->id)
                    ->orWhereHas('members', function ($members) use ($currentUser) {
                        $members->where('users.id', $currentUser->id);
                    });
            })
            ->exists();

        abort_unless($isTeamMember, 403, 'این کاربر عضو تیم شما نیست.');

        $alreadyMember = ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyMember) {
            return back()->with('info', 'این کاربر از قبل عضو پروژه است.');
        }

        ProjectMember::firstOrCreate(
            [
                'project_id' => $project->id,
                'user_id' => $user->id,
            ],
            [
                'role' => 'member',
            ]
        );

        return back()->with('success', 'عضو با موفقیت به پروژه اضافه شد.');
    }

    public function edit(Request $request, User $user): View
    {
        $currentUser = $request->user();

        abort_if($user->id === $currentUser->id, 403);

        $project = $currentUser->ownedProjects()
            ->whereHas('members', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->first();

        abort_unless($project, 403);

        $this->authorize('editMember', [$project, $user]);

        return view('teams.edit', compact('user', 'project'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        abort_if($user->id === $currentUser->id, 403);

        $project = $currentUser->ownedProjects()
            ->whereHas('members', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->first();

        abort_unless($project, 403);

        $this->authorize('editMember', [$project, $user]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
        ]);

        $user->update($validated);

        return redirect()
            ->route('teams.edit', $user->username)
            ->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        abort_if($user->id === $currentUser->id, 403);

        $project = $currentUser->ownedProjects()
            ->whereHas('members', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->first();

        abort_unless($project, 403);

        $this->authorize('editMember', [$project, $user]);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => bcrypt($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
