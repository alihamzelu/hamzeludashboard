<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $range = $request->get('range', '7');

        [$from, $to] = $this->dateRange($range);

        $projectIds = Project::query()
            ->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                    ->orWhereHas('members', fn ($q) => $q->where('users.id', $user->id));
            })
            ->pluck('id');

        // ---- KPIs ----
        $tasksCompleted = Task::query()
            ->whereIn('project_id', $projectIds)
            ->where('status', 'done')
            ->when($from, fn ($q) => $q->where('updated_at', '>=', $from))
            ->count();

        $activeProjects = Project::query()
            ->whereIn('id', $projectIds)
            ->whereIn('status', ['planning', 'active'])
            ->count();

        $openTasks = Task::query()
            ->whereIn('project_id', $projectIds)
            ->where('status', '!=', 'done')
            ->get();

        $nearDeadline = $openTasks
            ->filter(fn (Task $t) => $t->due_date && $t->due_date->lte(now()->addDays(3)))
            ->count();

        // On-time: done tasks with due_date, completed on/before due
        $doneWithDue = Task::query()
            ->whereIn('project_id', $projectIds)
            ->where('status', 'done')
            ->whereNotNull('due_date')
            ->when($from, fn ($q) => $q->where('updated_at', '>=', $from))
            ->get(['due_date', 'updated_at']);

        $onTimeRate = $doneWithDue->count() > 0
            ? (int) round(
                $doneWithDue->filter(fn ($t) => $t->updated_at->startOfDay()->lte($t->due_date))->count()
                / $doneWithDue->count() * 100
            )
            : 0;

        // Avg completion time (created_at → updated_at for done tasks)
        $avgDays = Task::query()
            ->whereIn('project_id', $projectIds)
            ->where('status', 'done')
            ->when($from, fn ($q) => $q->where('updated_at', '>=', $from))
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->value('avg_days');

        $avgCompletionDays = $avgDays !== null ? round((float) $avgDays, 1) : 0;

        $stats = [
            'tasks_completed'    => $tasksCompleted,
            'avg_completion'     => $avgCompletionDays,
            'on_time_rate'       => $onTimeRate,
            'active_projects'    => $activeProjects,
            'near_deadline'      => $nearDeadline,
        ];

        // ---- Daily completions chart (last 7 days always for chart simplicity) ----
        $chartFrom = now()->subDays(6)->startOfDay();
        $chartTo   = now()->endOfDay();

        $completionsByDay = Task::query()
            ->whereIn('project_id', $projectIds)
            ->where('status', 'done')
            ->whereBetween('updated_at', [$chartFrom, $chartTo])
            ->selectRaw('DATE(updated_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $chart = [];
        foreach (CarbonPeriod::create($chartFrom, $chartTo) as $date) {
            $key = $date->format('Y-m-d');
            $chart[] = [
                'label' => $date->format('D'),
                'count' => (int) ($completionsByDay[$key] ?? 0),
            ];
        }

        $chartMax = max(1, collect($chart)->max('count'));

        // ---- Status breakdown ----
        $statusCounts = Task::query()
            ->whereIn('project_id', $projectIds)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusTotal = max(1, $statusCounts->sum());

        $statusBreakdown = [
            ['label' => 'To Do',        'key' => 'todo',        'color' => 'bg-gray-400'],
            ['label' => 'In Progress',  'key' => 'in_progress', 'color' => 'bg-blue-500'],
            ['label' => 'Review',       'key' => 'review',      'color' => 'bg-purple-500'],
            ['label' => 'Done',         'key' => 'done',        'color' => 'bg-green-500'],
        ];

        foreach ($statusBreakdown as &$row) {
            $count = (int) ($statusCounts[$row['key']] ?? 0);
            $row['count'] = $count;
            $row['pct']   = (int) round($count / $statusTotal * 100);
        }
        unset($row);

        // ---- Priority (open tasks) ----
        $priorityCounts = Task::query()
            ->whereIn('project_id', $projectIds)
            ->where('status', '!=', 'done')
            ->select('priority', DB::raw('COUNT(*) as total'))
            ->groupBy('priority')
            ->pluck('total', 'priority');

        $priorities = [
            'high'   => (int) ($priorityCounts['high'] ?? 0),
            'medium' => (int) ($priorityCounts['medium'] ?? 0),
            'low'    => (int) ($priorityCounts['low'] ?? 0),
        ];

        // ---- Top projects ----
        $topProjects = Project::query()
            ->whereIn('id', $projectIds)
            ->withCount([
                'tasks',
                'tasks as done_tasks_count' => fn ($q) => $q->where('status', 'done'),
            ])
            ->orderByDesc('done_tasks_count')
            ->limit(5)
            ->get();

        // ---- Team productivity ----
        $memberIds = User::query()
            ->where('id', '!=', $user->id)
            ->whereHas('projects', function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('projects.owner_id', $user->id)
                        ->orWhereHas('members', fn ($q) => $q->where('users.id', $user->id));
                });
            })
            ->pluck('id')
            ->push($user->id)
            ->unique();

        $teamStats = User::query()
            ->whereIn('id', $memberIds)
            ->get()
            ->map(function (User $member) use ($projectIds) {
                $base = Task::query()
                    ->whereIn('project_id', $projectIds)
                    ->where('assigned_to', $member->id);

                $completed = (clone $base)->where('status', 'done')->count();
                $inProgress = (clone $base)->whereIn('status', ['todo', 'in_progress', 'review'])->count();
                $overdue = (clone $base)
                    ->where('status', '!=', 'done')
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<', today())
                    ->count();

                $doneWithDue = Task::query()
                    ->whereIn('project_id', $projectIds)
                    ->where('assigned_to', $member->id)
                    ->where('status', 'done')
                    ->whereNotNull('due_date')
                    ->get(['due_date', 'updated_at']);

                $onTime = $doneWithDue->count() > 0
                    ? (int) round(
                        $doneWithDue->filter(fn ($t) => $t->updated_at->startOfDay()->lte($t->due_date))->count()
                        / $doneWithDue->count() * 100
                    )
                    : null;

                return [
                    'user'       => $member,
                    'completed'  => $completed,
                    'in_progress'=> $inProgress,
                    'overdue'    => $overdue,
                    'on_time'    => $onTime,
                ];
            })
            ->sortByDesc('completed')
            ->values();

        return view('analytics.index', compact(
            'range',
            'stats',
            'chart',
            'chartMax',
            'statusBreakdown',
            'priorities',
            'topProjects',
            'teamStats'
        ));
    }

    protected function dateRange(string $range): array
    {
        return match ($range) {
            '30'      => [now()->subDays(30)->startOfDay(), now()],
            'quarter' => [now()->startOfQuarter(), now()],
            'all'     => [null, now()],
            default   => [now()->subDays(7)->startOfDay(), now()],
        };
    }
}