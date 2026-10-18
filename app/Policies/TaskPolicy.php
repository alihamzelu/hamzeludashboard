<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        $project = $task->project;

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

    public function update(User $user, Task $task): bool
    {
        $project = $task->project;

        return $project->owner_id === $user->id
            || $project->members()
                ->where('users.id', $user->id)
                ->wherePivot('role', 'manager')
                ->exists();
    }

    public function delete(User $user, Task $task): bool
    {
        $project = $task->project;

        return $project->owner_id === $user->id
            || $project->members()
                ->where('users.id', $user->id)
                ->wherePivot('role', 'manager')
                ->exists();
    }
}
