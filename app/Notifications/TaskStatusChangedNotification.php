<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $statuses = [
            'planning' => 'برنامه‌ریزی‌شده',
            'active' => 'در حال انجام',
            'completed' => 'تکمیل‌شده',
            'archived' => 'بایگانی‌شده',
        ];

        $oldStatus = $statuses[$this->oldStatus] ?? $this->oldStatus;
        $newStatus = $statuses[$this->newStatus] ?? $this->newStatus;

        return [
            'type' => 'task_status_changed',
            'title' => 'تغییر وضعیت وظیفه',
            'message' => "وضعیت وظیفه «{$this->task->title}» از «{$oldStatus}» به «{$newStatus}» تغییر کرد.",
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