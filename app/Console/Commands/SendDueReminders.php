<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Task;
use App\Models\User;
use App\Notifications\EventUpcomingReminderNotification;
use App\Notifications\TaskDueReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDueReminders extends Command
{
    protected $signature = 'reminders:due';

    protected $description = 'Send task and event due reminders (0–3 days ahead)';

    public function handle(): int
    {
        $today = now()->startOfDay();

        foreach ([0, 1, 2, 3] as $daysLeft) {
            $targetDate = $today->copy()->addDays($daysLeft);

            $this->sendTaskReminders($targetDate, $daysLeft);
            $this->sendEventReminders($targetDate, $daysLeft);
        }

        $this->info('Due reminders sent.');

        return self::SUCCESS;
    }

    protected function sendTaskReminders(Carbon $targetDate, int $daysLeft): void
    {
        $tasks = Task::query()
            ->with(['assignedUser', 'project'])
            ->whereNotNull('assigned_to')
            ->where('status', '!=', 'done')
            ->whereDate('due_date', $targetDate)
            ->get();

        foreach ($tasks as $task) {
            if (!$task->assignedUser) {
                continue;
            }

            if (!$task->assignedUser->notify_due_reminders) {
                continue;
            }

            if ($this->alreadyNotified($task->assignedUser, 'task_due_reminder', $task->id, $daysLeft)) {
                continue;
            }

            $task->assignedUser->notify(
                new TaskDueReminderNotification($task, $daysLeft)
            );
        }
    }

    protected function sendEventReminders(Carbon $targetDate, int $daysLeft): void
    {
        $events = Event::query()
            ->with('user')
            ->whereDate('starts_at', $targetDate)
            ->get();

        foreach ($events as $event) {
            if (!$event->user) {
                continue;
            }

            if (!$event->user->notify_due_reminders) {
                continue;
            }

            if ($this->alreadyNotified($event->user, 'event_upcoming_reminder', $event->id, $daysLeft)) {
                continue;
            }

            $event->user->notify(
                new EventUpcomingReminderNotification($event, $daysLeft)
            );
        }
    }

    protected function alreadyNotified(User $user, string $type, int $subjectId, int $daysLeft): bool
    {
        $key = $type === 'task_due_reminder' ? 'task_id' : 'event_id';

        return $user->notifications()
            ->where('data->type', $type)
            ->where("data->{$key}", $subjectId)
            ->where('data->days_left', $daysLeft)
            ->whereDate('created_at', today())
            ->exists();
    }
}