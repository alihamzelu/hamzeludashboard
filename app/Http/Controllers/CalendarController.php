<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        $month = (int) $request->input('month', now()->month);
        $year  = (int) $request->input('year', now()->year);

        $currentDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $prevMonth   = $currentDate->copy()->subMonth();
        $nextMonth   = $currentDate->copy()->addMonth();

        $startOfCalendar = $currentDate->copy()->startOfWeek(Carbon::SUNDAY);
        $endOfCalendar   = $currentDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $items = $this->calendarItems($startOfCalendar, $endOfCalendar);

        $itemsByDate = $items->groupBy(
            fn(array $item) => $item['date']->format('Y-m-d')
        );

        $calendarDays = collect();
        $day = $startOfCalendar->copy();

        while ($day <= $endOfCalendar) {
            $key = $day->format('Y-m-d');

            $calendarDays->push([
                'date'             => $day->copy(),
                'day_number'       => $day->day,
                'is_current_month' => $day->month === $currentDate->month,
                'is_today'         => $day->isToday(),
                'items'            => $itemsByDate->get($key, collect()),
            ]);

            $day->addDay();
        }

        $upcomingItems = $items
            ->filter(fn(array $item) => $item['date']->gte(now()->startOfDay()))
            ->sortBy('date')
            ->take(8)
            ->values();

        return view('calendar.index', compact(
            'currentDate',
            'prevMonth',
            'nextMonth',
            'calendarDays',
            'upcomingItems'
        ));
    }

    protected function calendarItems(Carbon $from, Carbon $to): Collection
    {
        $userId = auth()->id();

        $projectIds = Project::query()
            ->where(function ($q) use ($userId) {
                $q->where('owner_id', $userId)
                    ->orWhereHas('members', fn($q) => $q->where('users.id', $userId));
            })
            ->pluck('id');

        $events = Event::query()
            ->where('user_id', $userId)
            ->whereBetween('starts_at', [$from, $to])
            ->get()
            ->map(fn(Event $event) => [
                'id'        => $event->id,
                'title'     => $event->title,
                'type'      => 'event',
                'date'      => $event->starts_at,
                'color'     => $event->color ?: 'bg-blue-500',
                'sub_title' => $event->project_name,
                'starts_at' => $event->starts_at->format('Y-m-d\TH:i'),
                'url'       => null,
            ]);

        $tasks = Task::query()
            ->with('project')
            ->whereIn('project_id', $projectIds)
            ->where(function ($q) use ($userId) {
                $q->where('assigned_to', $userId)
                    ->orWhereNull('assigned_to');
            })
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$from->toDateString(), $to->toDateString()])
            ->get()
            ->map(function (Task $task) {
                $due = $task->due_date instanceof Carbon
                    ? $task->due_date->copy()->startOfDay()
                    : Carbon::parse($task->due_date)->startOfDay();

                return [
                    'id'        => $task->id,
                    'title'     => $task->title,
                    'type'      => 'task',
                    'date'      => $due,
                    'color'     => $task->status === 'done' ? 'bg-gray-400' : 'bg-emerald-500',
                    'sub_title' => $task->project?->name,
                    'url'       => route('tasks.show', $task),
                ];
            });

        return $events->concat($tasks)->values();
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'project_name' => ['nullable', 'string', 'max:255'],
            'starts_at'    => ['required', 'date'],
            'color'        => ['nullable', 'string', 'in:bg-blue-500,bg-emerald-500,bg-orange-500,bg-red-500,bg-purple-500,bg-black'],
        ]);

        Event::create([
            'user_id'      => auth()->id(),
            'title'        => $validated['title'],
            'project_name' => $validated['project_name'] ?? null,
            'starts_at'    => $validated['starts_at'],
            'color'        => $validated['color'] ?? 'bg-blue-500',
        ]);

        return back()->with('success', 'Event created successfully.');
    }
    public function update(Request $request, Event $event)
    {
        abort_unless($event->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'project_name' => ['nullable', 'string', 'max:255'],
            'starts_at'    => ['required', 'date'],
            'color'        => ['nullable', 'string', 'in:bg-blue-500,bg-emerald-500,bg-orange-500,bg-red-500,bg-purple-500,bg-black'],
        ]);

        $event->update([
            'title'        => $validated['title'],
            'project_name' => $validated['project_name'] ?? null,
            'starts_at'    => $validated['starts_at'],
            'color'        => $validated['color'] ?? $event->color,
        ]);

        return back()->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        abort_unless($event->user_id === auth()->id(), 403);

        $event->delete();

        return back()->with('success', 'Event deleted successfully.');
    }
}
