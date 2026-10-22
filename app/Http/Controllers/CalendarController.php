<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        $now = Jalalian::now();

        $month = (int) $request->query('month', $now->getMonth());
        $year = (int) $request->query('year', $now->getYear());

        if ($month < 1 || $month > 12 || $year < 1300 || $year > 1600) {
            $month = $now->getMonth();
            $year = $now->getYear();
        }

        $startGregorian = Carbon::instance(
            Jalalian::fromFormat('Y/m/d H:i:s', sprintf(
                '%04d/%02d/01 00:00:00',
                $year,
                $month
            ))->toCarbon()
        )->startOfDay();

        $nextMonth = $month === 12
            ? ['month' => 1, 'year' => $year + 1]
            : ['month' => $month + 1, 'year' => $year];

        $prevMonth = $month === 1
            ? ['month' => 12, 'year' => $year - 1]
            : ['month' => $month - 1, 'year' => $year];

        $endGregorian = Carbon::instance(
            Jalalian::fromFormat('Y/m/d H:i:s', sprintf(
                '%04d/%02d/01 00:00:00',
                $nextMonth['year'],
                $nextMonth['month']
            ))->toCarbon()
        )->subSecond();

        $calendarItems = $this->calendarItems(
            $startGregorian->copy()->startOfMonth()->subDays(7),
            $endGregorian->copy()->endOfMonth()->addDays(7)
        );

        $calendarDays = $this->buildCalendarDays(
            $year,
            $month,
            $calendarItems
        );

        $upcomingItems = $this->calendarItems(
            now()->startOfDay(),
            now()->addDays(30)->endOfDay()
        )
            ->sortBy('starts_at')
            ->take(10)
            ->values();

        return view('calendar.index', compact(
            'month',
            'year',
            'prevMonth',
            'nextMonth',
            'calendarDays',
            'upcomingItems'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'project_name' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'string', 'max:30'],
            'color' => [
                'required',
                Rule::in([
                    'bg-blue-500',
                    'bg-emerald-500',
                    'bg-orange-500',
                    'bg-red-500',
                    'bg-purple-500',
                    'bg-slate-800',
                ]),
            ],
        ]);

        $startsAt = $this->jalaliToCarbon($validated['starts_at']);

        if (!$startsAt) {
            return back()
                ->withErrors(['starts_at' => 'تاریخ و ساعت واردشده معتبر نیست.'])
                ->withInput();
        }

        $event = new Event();
        $event->user_id = Auth::id();
        $event->title = $validated['title'];
        $event->project_name = $validated['project_name'] ?? null;
        $event->starts_at = $startsAt;
        $event->color = $validated['color'];
        $event->save();

        return redirect()
            ->route('calendar.index')
            ->with('success', 'رویداد با موفقیت ایجاد شد.');
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $this->authorizeEvent($event);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'project_name' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'string', 'max:30'],
            'color' => [
                'required',
                Rule::in([
                    'bg-blue-500',
                    'bg-emerald-500',
                    'bg-orange-500',
                    'bg-red-500',
                    'bg-purple-500',
                    'bg-slate-800',
                ]),
            ],
        ]);

        $startsAt = $this->jalaliToCarbon($validated['starts_at']);

        if (!$startsAt) {
            return back()
                ->withErrors(['starts_at' => 'تاریخ و ساعت واردشده معتبر نیست.'])
                ->withInput();
        }

        $event->title = $validated['title'];
        $event->project_name = $validated['project_name'] ?? null;
        $event->starts_at = $startsAt;
        $event->color = $validated['color'];
        $event->save();

        return redirect()
            ->route('calendar.index')
            ->with('success', 'رویداد با موفقیت ویرایش شد.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorizeEvent($event);

        $event->delete();

        return redirect()
            ->route('calendar.index')
            ->with('success', 'رویداد حذف شد.');
    }

    private function calendarItems(Carbon $from, Carbon $to)
    {
        $items = collect();

        $events = Event::query()
            ->where('user_id', Auth::id())
            ->whereBetween('starts_at', [$from, $to])
            ->get();

        foreach ($events as $event) {
            $date = Carbon::parse($event->starts_at);

            $items->push([
                'id' => $event->id,
                'type' => 'event',
                'title' => $event->title,
                'sub_title' => $event->project_name,
                'starts_at' => $date->format('Y-m-d H:i:s'),
                'timestamp' => $date->timestamp,
                'time' => $date->format('H:i'),
                'jalali_date' => Jalalian::fromCarbon($date)->format('Y/m/d'),
                'is_today' => $date->isToday(),
                'is_tomorrow' => $date->isTomorrow(),
                'color' => $event->color ?: 'bg-blue-500',
                'url' => null,
            ]);
        }

        $tasks = Task::query()
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$from, $to])
            ->whereHas('project', function ($query) {
                $query->where(function ($q) {
                    $q->where('owner_id', Auth::id())
                        ->orWhereHas('members', function ($members) {
                            $members->where('user_id', Auth::id());
                        });
                });
            })
            ->with('project')
            ->get();

        foreach ($tasks as $task) {
            $date = Carbon::parse($task->due_date);

            $isDone = $task->status === 'done';

            $items->push([
                'id' => $task->id,
                'type' => 'task',
                'title' => $task->title,
                'sub_title' => $task->project->name ?? null,
                'starts_at' => $date->format('Y-m-d H:i:s'),
                'timestamp' => $date->timestamp,
                'time' => $date->format('H:i'),
                'jalali_date' => Jalalian::fromCarbon($date)->format('Y/m/d'),
                'is_today' => $date->isToday(),
                'is_tomorrow' => $date->isTomorrow(),
                'color' => $isDone ? 'bg-slate-400' : 'bg-emerald-500',
                'url' => $task->project
                    ? route('projects.tasks.show', [
                        $task->project->slug,
                        $task->id,
                    ])
                    : null,
            ]);
        }

        return $items->sortBy('timestamp')->values();
    }

    private function buildCalendarDays(int $year, int $month, $items): array
    {
        $firstDay = Jalalian::fromFormat(
            'Y/m/d',
            sprintf('%04d/%02d/01', $year, $month)
        )->toCarbon();

        $daysInMonth = $this->jalaliMonthLength($year, $month);

        // شروع هفته در تقویم شمسی: شنبه
        $offset = ((int) $firstDay->dayOfWeek + 1) % 7;

        $totalCells = (int) (ceil(($offset + $daysInMonth) / 7) * 7);

        $today = Jalalian::now();

        $days = [];

        for ($i = 0; $i < $totalCells; $i++) {
            $dayNumber = $i - $offset + 1;
            $isCurrentMonth = $dayNumber >= 1 && $dayNumber <= $daysInMonth;

            $dayDate = null;

            if ($isCurrentMonth) {
                $dayDate = Jalalian::fromFormat(
                    'Y/m/d',
                    sprintf('%04d/%02d/%02d', $year, $month, $dayNumber)
                );
            }

            $dayItems = collect();

            if ($dayDate) {
                $dayItems = $items->filter(function ($item) use ($dayDate) {
                    return Carbon::parse($item['starts_at'])->toDateString()
                        === $dayDate->toCarbon()->toDateString();
                })->values();
            }

            $days[] = [
                'day_number' => $isCurrentMonth ? $dayNumber : null,
                'is_current_month' => $isCurrentMonth,
                'is_today' => $isCurrentMonth
                    && $dayNumber === $today->getDay()
                    && $month === $today->getMonth()
                    && $year === $today->getYear(),
                'items' => $dayItems,
            ];
        }

        return $days;
    }

    private function jalaliMonthLength(int $year, int $month): int
    {
        if ($month <= 6) {
            return 31;
        }

        if ($month <= 11) {
            return 30;
        }

        return Jalalian::fromFormat(
            'Y/m/d',
            sprintf('%04d/12/01', $year)
        )->isLeapYear() ? 30 : 29;
    }

    private function jalaliToCarbon(string $value): ?Carbon
    {
        $value = trim($value);

        $value = strtr($value, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3',
            '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7',
            '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3',
            '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7',
            '٨' => '8', '٩' => '9',
        ]);

        if (!preg_match(
            '/^(\d{4})\/(\d{1,2})\/(\d{1,2})(?:\s+(\d{1,2}):(\d{2}))?$/',
            $value,
            $matches
        )) {
            return null;
        }

        $year = (int) $matches[1];
        $month = (int) $matches[2];
        $day = (int) $matches[3];
        $hour = isset($matches[4]) ? (int) $matches[4] : 9;
        $minute = isset($matches[5]) ? (int) $matches[5] : 0;

        if (
            $year < 1300
            || $month < 1
            || $month > 12
            || $day < 1
            || $day > $this->jalaliMonthLength($year, $month)
            || $hour < 0
            || $hour > 23
            || $minute < 0
            || $minute > 59
        ) {
            return null;
        }

        try {
            $jalali = Jalalian::fromFormat(
                'Y/m/d H:i:s',
                sprintf(
                    '%04d/%02d/%02d %02d:%02d:00',
                    $year,
                    $month,
                    $day,
                    $hour,
                    $minute
                )
            );

            return $jalali->toCarbon();
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function authorizeEvent(Event $event): void
    {
        abort_unless(
            (int) $event->user_id === (int) Auth::id(),
            403
        );
    }
}