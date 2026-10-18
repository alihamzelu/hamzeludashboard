<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_assigned',
            'title' => 'وظیفه جدید به شما واگذار شد',
            'message' => "وظیفه جدید «{$this->task->title}» به شما واگذار شده است.",
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id,
            'url' => route('projects.show', $this->task->project),
        ];
    }

    public function handle(): void
    {
        // کدهای موردنیاز برای پردازش اعلان
    }
}