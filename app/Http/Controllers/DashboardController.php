<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $projectIds = Project::query()
            ->where(function ($query) use ($user) {
                $query->where('owner_id', $user->id)
                    ->orWhereHas('members', function ($query) use ($user) {
                        $query->where('users.id', $user->id);
                    });
            })
            ->pluck('id');

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

        $pendingTasks = Task::query()
            ->whereIn('project_id', $projectIds)
            ->where('status', '!=', 'done')
            ->count();

        $dueTodayCount = Task::query()
            ->whereIn('project_id', $projectIds)
            ->where('status', '!=', 'done')
            ->whereDate('due_date', today())
            ->count();

        $teamMembers = User::query()
            ->where('id', '!=', $user->id)
            ->whereHas('projects', function ($query) use ($user) {
                $query->where(function ($query) use ($user) {
                    $query->where('projects.owner_id', $user->id)
                        ->orWhereHas('members', function ($query) use ($user) {
                            $query->where('users.id', $user->id);
                        });
                });
            })
            ->orderBy('name')
            ->limit(6)
            ->get();

        $upcomingDeadlines = Task::query()
            ->with('project')
            ->whereIn('project_id', $projectIds)
            ->where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>=', today())
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $recentActivities = ProjectActivity::query()
            ->with('user')
            ->whereIn('project_id', $projectIds)
            ->latest()
            ->limit(6)
            ->get();

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

        $currentDate = now()->startOfMonth();

        $startOfCalendar = $currentDate
            ->copy()
            ->startOfWeek(Carbon::SUNDAY);

        $endOfCalendar = $currentDate
            ->copy()
            ->endOfMonth()
            ->endOfWeek(Carbon::SATURDAY);

        $dueDates = Task::query()
            ->whereIn('project_id', $projectIds)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [
                $startOfCalendar->toDateString(),
                $endOfCalendar->toDateString(),
            ])
            ->pluck('due_date')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
            ->unique()
            ->values()
            ->all();

        $calendarDays = collect();

        $day = $startOfCalendar->copy();

        while ($day <= $endOfCalendar) {
            $calendarDays->push([
                'day' => $day->day,
                'is_current_month' => $day->month === $currentDate->month,
                'is_today' => $day->isToday(),
                'has_due' => in_array(
                    $day->format('Y-m-d'),
                    $dueDates,
                    true
                ),
            ]);

            $day->addDay();
        }

        $stats = [
            'active_projects' => $activeProjects->count(),
            'pending_tasks' => $pendingTasks,
            'due_today' => $dueTodayCount,
            'team_members' => $teamMembers->count(),
            'completed_projects' => $completedProjects->count(),
        ];

        return view('dashboard', compact(
            'stats',
            'projects',
            'activeProjects',
            'upcomingDeadlines',
            'recentActivities',
            'teamMembers',
            'todayTasks',
            'currentDate',
            'calendarDays'
        ));
    }
}
