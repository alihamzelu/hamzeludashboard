<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'username',
    'email',
    'password',
    'notify_email',
    'notify_task_assigned',
    'notify_due_reminders',
    'notify_weekly_digest',
    'avatar',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notify_email' => 'boolean',
            'notify_task_assigned' => 'boolean',
            'notify_due_reminders' => 'boolean',
            'notify_weekly_digest' => 'boolean',
        ];
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class)
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function ownedProjects()
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function teamMembers()
    {
        return User::where('users.id', '!=', $this->id)
            ->whereHas('projects', function ($query) {
                $query->where(function ($q) {
                    $q->where('projects.owner_id', $this->id)
                        ->orWhereHas('users', function ($q) {
                            $q->where('users.id', $this->id);
                        });
                });
            });
    }

    public function projectInvitations()
    {
        return $this->hasMany(ProjectInvitation::class, 'user_id');
    }
    public function getRouteKeyName()
    {
        return 'username';
    }
}
