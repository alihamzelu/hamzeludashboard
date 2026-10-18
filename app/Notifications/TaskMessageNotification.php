<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\TaskMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
        public TaskMessage $message
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $sender = $this->message->user;

        return [
            'type' => 'task_message',
            'title' => 'پیام جدید در وظیفه',
            'message' => ($sender?->name ?? 'یکی از اعضا')
                . " در وظیفه «{$this->task->title}» پیام جدیدی ارسال کرد.",
            'url' => route('tasks.show', $this->task),
            'task_id' => $this->task->id,
        ];
    }

    public function handle(): void
    {
        // کدهای موردنیاز برای پردازش اعلان
    }
}