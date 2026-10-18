<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskDueReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
        public int $daysLeft
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $label = match ($this->daysLeft) {
            0 => 'امروز',
            1 => 'فردا',
            default => "{$this->daysLeft} روز دیگر",
        };

        return [
            'type' => 'task_due_reminder',
            'title' => 'یادآوری مهلت وظیفه',
            'message' => "مهلت انجام وظیفه «{$this->task->title}» {$label} است.",
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id,
            'days_left' => $this->daysLeft,
            'url' => route('tasks.show', $this->task),
        ];
    }

    public function handle(): void
    {
        // کدهای موردنیاز برای پردازش اعلان
    }
}