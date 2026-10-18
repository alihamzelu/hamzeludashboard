<?php

namespace App\Notifications;

use App\Models\ProjectInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProjectInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ProjectInvitation $invitation
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $project = $this->invitation->project;

        return [
            'type' => 'project_invitation',
            'invitation_id' => $this->invitation->id,
            'project_id' => $project->id,
            'project_name' => $project->name,
            'title' => 'دعوت به پروژه',
            'message' => "شما به پروژه «{$project->name}» دعوت شده‌اید.",
            'url' => route('invitations.index'),
        ];
    }

    public function handle(): void
    {
        // کدهای موردنیاز برای پردازش اعلان
    }
}