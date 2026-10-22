<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;
use Morilog\Jalali\Jalalian;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | پروژه‌های کاربر
        |--------------------------------------------------------------------------
        */

        $projectIds = Project::query()
            ->where(function ($query) use ($user) {
                $query->where('owner_id', $user->id)
                    ->orWhereHas('members', function ($query) use ($user) {
                        $query->where('users.id', $user->id);
                    });
            })
            ->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | پروژه‌ها
        |--------------------------------------------------------------------------
        */

        $projects = Project::query()
            ->whereIn('id', $projectIds)
            ->with(['members'])
            ->withCount([
                'tasks',
                'tasks as done_tasks_count' => fn ($query) =>
                    $query->where('status', 'done'),
            ])
            ->latest()
            ->get();

        $activeProjects = $projects->whereIn('status', [
            'planning',
            'active',
        ]);

        $completedProjects = $projects->where(
            'status',
            'completed'
        );

        /*
        |--------------------------------------------------------------------------
        | تسک‌های در انتظار
        |--------------------------------------------------------------------------
        */

        $pendingTasks = Task::query()
            ->whereIn('project_id', $projectIds)
            ->where('status', '!=', 'done')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | تسک‌های امروز
        |--------------------------------------------------------------------------
        */

        $dueTodayCount = Task::query()
            ->whereIn('project_id', $projectIds)
            ->where('status', '!=', 'done')
            ->whereDate('due_date', today())
            ->count();

        /*
        |--------------------------------------------------------------------------
        | اعضای تیم
        |--------------------------------------------------------------------------
        */

        $teamMembers = User::query()
            ->where('id', '!=', $user->id)
            ->whereHas('projects', function ($query) use ($user) {
                $query->where(function ($query) use ($user) {
                    $query->where(
                        'projects.owner_id',
                        $user->id
                    )
                    ->orWhereHas('members', function ($query) use ($user) {
                        $query->where(
                            'users.id',
                            $user->id
                        );
                    });
                });
            })
            ->orderBy('name')
            ->limit(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | نزدیک‌ترین ددلاین‌ها
        |--------------------------------------------------------------------------
        */

        $upcomingDeadlines = Task::query()
            ->with('project')
            ->whereIn('project_id', $projectIds)
            ->where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>=', today())
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | فعالیت‌های اخیر
        |--------------------------------------------------------------------------
        */

        $recentActivities = ProjectActivity::query()
            ->with('user')
            ->whereIn('project_id', $projectIds)
            ->latest()
            ->limit(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | تسک‌های امروز
        |--------------------------------------------------------------------------
        */

        $todayTasks = Task::query()
            ->whereIn('project_id', $projectIds)
            ->where(function ($query) use ($user) {
                $query->where('assigned_to', $user->id)
                    ->orWhereNull('assigned_to');
            })
            ->where(function ($query) {
                $query->whereDate('due_date', today())
                    ->orWhere(function ($query) {
                        $query->whereNull('due_date')
                            ->where('status', '!=', 'done')
                            ->whereDate('created_at', today());
                    });
            })
            ->orderByRaw(
                "CASE status WHEN 'done' THEN 1 ELSE 0 END"
            )
            ->limit(8)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | تقویم شمسی
        |--------------------------------------------------------------------------
        */

        $todayJalali = Jalalian::now();

        $month = $todayJalali->getMonth();
        $year = $todayJalali->getYear();

        /*
        |--------------------------------------------------------------------------
        | اولین روز ماه شمسی
        |--------------------------------------------------------------------------
        */

        $currentJalali = Jalalian::fromFormat(
            'Y-m-d',
            sprintf(
                '%04d-%02d-01',
                $year,
                $month
            )
        );

        /*
        |--------------------------------------------------------------------------
        | تعداد روزهای ماه
        |--------------------------------------------------------------------------
        */

        $daysInMonth = $currentJalali->getMonthDays();

        /*
        |--------------------------------------------------------------------------
        | آخرین روز ماه شمسی
        |--------------------------------------------------------------------------
        */

        $lastJalali = Jalalian::fromFormat(
            'Y-m-d',
            sprintf(
                '%04d-%02d-%02d',
                $year,
                $month,
                $daysInMonth
            )
        );

        /*
        |--------------------------------------------------------------------------
        | تبدیل به تاریخ میلادی
        |--------------------------------------------------------------------------
        */

        $currentDate = $currentJalali
            ->toCarbon()
            ->startOfDay();

        $lastDate = $lastJalali
            ->toCarbon()
            ->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | محدوده نمایش تقویم
        |
        | شنبه = شروع هفته
        | جمعه = پایان هفته
        |--------------------------------------------------------------------------
        */

        $startOfCalendar = $currentDate
            ->copy()
            ->startOfWeek(Carbon::SATURDAY);

        $endOfCalendar = $lastDate
            ->copy()
            ->endOfWeek(Carbon::FRIDAY);

        /*
        |--------------------------------------------------------------------------
        | پیدا کردن تاریخ‌هایی که تسک دارند
        |--------------------------------------------------------------------------
        */

        $dueDates = Task::query()
            ->whereIn('project_id', $projectIds)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [
                $startOfCalendar->toDateString(),
                $endOfCalendar->toDateString(),
            ])
            ->pluck('due_date')
            ->map(function ($date) {
                return Carbon::parse($date)
                    ->format('Y-m-d');
            })
            ->unique()
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | ساخت روزهای تقویم
        |--------------------------------------------------------------------------
        */

        $calendarDays = collect();

        $day = $startOfCalendar->copy();

        while ($day->lte($endOfCalendar)) {

            $jalaliDay = Jalalian::fromCarbon($day);

            $calendarDays->push([
                'date' => $day->copy(),

                // شماره روز شمسی
                'day_number' => $jalaliDay->getDay(),

                // آیا مربوط به ماه جاری است؟
                'is_current_month' =>
                    $jalaliDay->getMonth() === $month
                    && $jalaliDay->getYear() === $year,

                // آیا امروز است؟
                'is_today' => $day->isToday(),

                // آیا در این روز تسک وجود دارد؟
                'has_due' => in_array(
                    $day->format('Y-m-d'),
                    $dueDates,
                    true
                ),
            ]);

            $day->addDay();
        }

        /*
        |--------------------------------------------------------------------------
        | آمار داشبورد
        |--------------------------------------------------------------------------
        */

        $stats = [
            'active_projects' => $activeProjects->count(),
            'pending_tasks' => $pendingTasks,
            'due_today' => $dueTodayCount,
            'team_members' => $teamMembers->count(),
            'completed_projects' => $completedProjects->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | ارسال اطلاعات به View
        |--------------------------------------------------------------------------
        */

        return view('dashboard', compact(
            'stats',
            'projects',
            'activeProjects',
            'upcomingDeadlines',
            'recentActivities',
            'teamMembers',
            'todayTasks',
            'month',
            'year',
            'currentDate',
            'calendarDays'
        ));
    }
}