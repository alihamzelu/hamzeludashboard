<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class EventUpcomingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Event $event,
        public int $daysLeft
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $label = match ($this->daysLeft) {
            0       => 'امروز',
            1       => 'فردا',
            default => "{$this->daysLeft} روز دیگر",
        };

        $time = $this->event->starts_at->format('H:i');

        return [
            'type'      => 'event_upcoming_reminder',
            'title'     => 'یادآوری رویداد',
            'message'   => "رویداد «{$this->event->title}» {$label} ساعت {$time} برگزار می‌شود.",
            'event_id'  => $this->event->id,
            'days_left' => $this->daysLeft,
            'url'       => route('calendar.index'),
        ];
    }
}