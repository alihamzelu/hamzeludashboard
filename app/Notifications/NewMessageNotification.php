<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Message $message,
        public Conversation $conversation
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $sender = $this->message->user;

        return [
            'type'            => 'new_message',
            'title'           => 'پیام جدید',
            'message'         => 'پیام جدیدی از طرف ' . ($sender?->name ?? 'یک کاربر') . ' دریافت کردید.',
            'body_preview'    => Str::limit($this->message->body, 80),
            'conversation_id' => $this->conversation->id,
            'message_id'      => $this->message->id,
            'sender_id'       => $sender?->id,
            'sender_name'     => $sender?->name,
            'url'             => route('messages.show', $this->conversation),
        ];
    }
}