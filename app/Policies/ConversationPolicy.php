<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    public function view(User $user, Conversation $conversation): bool
    {
        return $conversation->users()
            ->where('users.id', $user->id)
            ->exists();
    }

    public function sendMessage(User $user, Conversation $conversation): bool
    {
        return $conversation->users()
            ->where('users.id', $user->id)
            ->exists();
    }

    public function start(User $user, User $recipient): bool
    {
        if ($user->id === $recipient->id) {
            return false;
        }

        return $user->projects()
            ->where(function ($query) use ($recipient) {
                $query->where('projects.owner_id', $recipient->id)
                    ->orWhereHas('members', function ($query) use ($recipient) {
                        $query->where('users.id', $recipient->id);
                    });
            })
            ->exists()
            ||
            $recipient->projects()
                ->where(function ($query) use ($user) {
                    $query->where('projects.owner_id', $user->id)
                        ->orWhereHas('members', function ($query) use ($user) {
                            $query->where('users.id', $user->id);
                        });
                })
                ->exists();
    }
}