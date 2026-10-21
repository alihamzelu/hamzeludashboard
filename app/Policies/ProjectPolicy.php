<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    
    public function view(User $user, Project $project): bool
    {
        return $project->owner_id === $user->id
            || $project->members()
                ->where('users.id', $user->id)
                ->exists();
    }

    public function create(User $user, Project $project): bool
    {
        return $project->owner_id === $user->id
            || $project->members()
                ->where('users.id', $user->id)
                ->wherePivot('role', 'manager')
                ->exists();
    }

    public function edit(User $user, Project $project): bool
    {
        return $project->owner_id === $user->id
            || $project->members()
                ->where('users.id', $user->id)
                ->wherePivot('role', 'manager')
                ->exists();
    }

    public function delete(User $user, Project $project): bool
    {
        // فقط Owner می‌تواند حذف کند (مدیران دیگر حذف نمی‌کنند)
        return $project->owner_id === $user->id;
    }

    public function inviteMember(User $user, Project $project): bool
    {
        return $project->owner_id === $user->id
            || $project->members()
                ->where('users.id', $user->id)
                ->wherePivot('role', 'manager')
                ->exists();
    }

    public function removeMember(User $user, Project $project, User $target): bool
    {
        // هیچ کس نمی‌تواند Owner را حذف کند
        if ($target->id === $project->owner_id) {
            return false;
        }

        // Owner می‌تواند هر عضوی را حذف کند
        if ($project->owner_id === $user->id) {
            return true;
        }

        // مدیر نمی‌تواند خودش را حذف کند
        if ($user->id === $target->id) {
            return false;
        }

        // فقط مدیران می‌توانند عضو حذف کنند
        $isManager = $project->members()
            ->where('users.id', $user->id)
            ->wherePivot('role', 'manager')
            ->exists();

        if (! $isManager) {
            return false;
        }

        // مدیر نمی‌تواند مدیر دیگری را حذف کند
        $targetMembership = $project->members()
            ->where('users.id', $target->id)
            ->first();

        if (! $targetMembership) {
            return false;
        }

        return $targetMembership->pivot->role !== 'manager';
    }
    public function editMember(User $user, Project $project, User $target): bool
    {
        if ($target->id === $project->owner_id) {
            return false;
        }

        return $project->owner_id === $user->id;
    }


    public function changeMemberRole(User $user, Project $project, User $target): bool
    {
        if ($project->owner_id === $user->id) {
            return true;
        }

        if ($target->id === $project->owner_id) {
            return false;
        }

        if ($user->id === $target->id) {
            return false;
        }

        $isManager = $project->members()
            ->where('users.id', $user->id)
            ->wherePivot('role', 'manager')
            ->exists();

        if (! $isManager) {
            return false;
        }

        $targetRole = $project->members()
            ->where('users.id', $target->id)
            ->first()?->pivot?->role;

        return $targetRole !== 'manager';
    }
}