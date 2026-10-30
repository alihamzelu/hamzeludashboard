<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskStatusChangedNotification;
use App\Services\ProjectActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class TaskController extends Controller
{
    private function validateTaskAssignees(Project $project, array $userIds): array
    {
        $userIds = collect($userIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $eligibleIds = $project->members
            ->pluck('id')
            ->push($project->owner_id)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->reject(fn ($id) => $id === (int) auth()->id());

        abort_unless(
            $userIds->diff($eligibleIds)->isEmpty(),
            422,
            'شما نمی‌توانید خودتان یا کاربری خارج از اعضای پروژه را به‌عنوان مسئول انتخاب کنید.'
        );

        return $userIds->all();
    }

    private function syncTaskAssignees(Task $task, array $userIds): array
    {
        $oldUserIds = $task->users()
            ->pluck('users.id')
            ->map(fn ($id) => (int) $id);

        $task->users()->sync($userIds);

        // سازگاری موقت با بخش‌های قدیمی پروژه
        $task->update([
            'assigned_to' => $userIds[0] ?? null,
        ]);

        return collect($userIds)
            ->diff($oldUserIds)
            ->values()
            ->all();
    }


    private function notifyNewAssignees(Task $task, array $userIds): void
    {
        if (empty($userIds)) {
            return;
        }

        $users = User::query()
            ->whereIn('id', $userIds)
            ->where('id', '!=', auth()->id())
            ->where('notify_task_assigned', true)
            ->get();

        foreach ($users as $user) {
            $user->notify(new TaskAssignedNotification($task));
        }
    }

    public function index(Request $request): View
    {
        $projectIds = Project::query()
            ->where(function ($query) {
                $query->where('owner_id', auth()->id())
                    ->orWhereHas('members', function ($query) {
                        $query->where('users.id', auth()->id());
                    });
            })
            ->select('id');

        $query = Task::query()
            ->whereIn('project_id', $projectIds)
            ->with(['project', 'users']);

        $status = $request->get('status');

        if (in_array($status, ['todo', 'in_progress', 'review', 'done'], true)) {
            $query->where('status', $status);
        } else {
            $status = '';
        }

        if (in_array($request->get('priority'), ['low', 'medium', 'high'], true)) {
            $query->where('priority', $request->get('priority'));
        }

        $tasks = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('tasks.index', compact('tasks', 'status'));
    }

    public function create(): View
    {
        $projects = Project::query()
            ->where(function ($query) {
                $query->where('owner_id', auth()->id())
                    ->orWhereHas('members', function ($query) {
                        $query->where('users.id', auth()->id());
                    });
            })
            ->with(['members', 'owner'])
            ->orderBy('name')
            ->get();

        return view('tasks.create', compact('projects'));
    }

    public function storeMultiple(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:todo,in_progress,review,done'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date_format:Y-m-d'],
            'assigned_user_ids' => ['nullable', 'array'],
            'assigned_user_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ]);

        $project = Project::query()
            ->whereKey($validated['project_id'])
            ->where(function ($query) {
                $query->where('owner_id', auth()->id())
                    ->orWhereHas('members', function ($query) {
                        $query->where('users.id', auth()->id());
                    });
            })
            ->with(['members', 'owner'])
            ->firstOrFail();

        $this->authorize('create', $project);

        $assigneeIds = $this->validateTaskAssignees(
            $project,
            $validated['assigned_user_ids'] ?? []
        );

        [$task, $newAssigneeIds] = DB::transaction(function () use (
            $project,
            $validated,
            $assigneeIds
        ) {
            $task = $project->tasks()->create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'priority' => $validated['priority'] ?? 'medium',
                'due_date' => $validated['due_date'] ?? null,
                'assigned_to' => $assigneeIds[0] ?? null,
            ]);

            $newAssigneeIds = $this->syncTaskAssignees($task, $assigneeIds);

            $statusLabels = [
                'todo' => 'انجام نشده',
                'in_progress' => 'در حال انجام',
                'review' => 'بازبینی',
                'done' => 'انجام‌شده',
            ];

            ProjectActivityLogger::log(
                $project,
                'task.created',
                'ایجاد شد "' . $task->title . '"',
                $task,
                ['status' => $statusLabels[$task->status]]
            );

            return [$task, $newAssigneeIds];
        });

        $this->notifyNewAssignees($task, $newAssigneeIds);

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'وظیفه با موفقیت ایجاد شد.');
    }

    public function store(StoreTaskRequest $request,Project $project): RedirectResponse {
        $this->authorize('create', $project);

        $data = $request->validated();

        $project->loadMissing(['members', 'owner']);

        $assigneeIds = $this->validateTaskAssignees(
            $project,
            $data['assigned_user_ids'] ?? []
        );

        [$task, $newAssigneeIds] = DB::transaction(function () use (
            $project,
            $data,
            $assigneeIds
        ) {
            $task = $project->tasks()->create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'],
                'priority' => $data['priority'] ?? 'medium',
                'due_date' => $data['due_date'] ?? null,
                'assigned_to' => $assigneeIds[0] ?? null,
            ]);

            $newAssigneeIds = $this->syncTaskAssignees($task, $assigneeIds);

            $statusLabels = [
                'todo' => 'انجام نشده',
                'in_progress' => 'در حال انجام',
                'review' => 'بازبینی',
                'done' => 'انجام‌شده',
            ];

            ProjectActivityLogger::log(
                $project,
                'task.created',
                'ایجاد شد "' . $task->title . '"',
                $task,
                ['status' => $statusLabels[$task->status]]
            );

            return [$task, $newAssigneeIds];
        });

        $this->notifyNewAssignees($task, $newAssigneeIds);

        return back()->with('success', 'وظیفه با موفقیت ایجاد شد.');
    }

    public function edit(Project $project, Task $task): View
    {
        abort_if($task->project_id !== $project->id, 404);

        $this->authorize('update', $task);

        $project->load(['members', 'owner']);

        $users = $project->members
            ->push($project->owner)
            ->filter()
            ->unique('id')
            ->reject(fn ($user) => (int) $user->id === (int) auth()->id())
            ->sortBy('name')
            ->values();

        $task->load('users');

        return view('tasks.edit', compact('task', 'project', 'users'));
    }

    public function update(UpdateTaskRequest $request,Project $project,Task $task): RedirectResponse {
        abort_if($task->project_id !== $project->id, 404);

        $this->authorize('update', $task);

        $data = $request->validated();

        $project->loadMissing(['members', 'owner']);

        $assigneeIds = $this->validateTaskAssignees(
            $project,
            $data['assigned_user_ids'] ?? []
        );

        $oldStatus = $task->status;

        $oldAssigneeIds = $task->users()
            ->pluck('users.id')
            ->map(fn ($id) => (int) $id)
            ->all();

        [$newAssigneeIds] = DB::transaction(function () use (
            $task,
            $data,
            $assigneeIds,
            $project,
            $oldStatus,
            $oldAssigneeIds
        ) {
            $task->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'],
                'priority' => $data['priority'] ?? 'medium',
                'due_date' => $data['due_date'] ?? null,
            ]);

            $newAssigneeIds = $this->syncTaskAssignees($task, $assigneeIds);

            $statusLabels = [
                'todo' => 'انجام نشده',
                'in_progress' => 'در حال انجام',
                'review' => 'بازبینی',
                'done' => 'انجام‌شده',
            ];

            ProjectActivityLogger::log(
                $project,
                'task.updated',
                'به‌روزرسانی شد "' . $task->title . '"',
                $task,
                [
                    'old_status' => $statusLabels[$oldStatus] ?? $oldStatus,
                    'new_status' => $statusLabels[$task->status] ?? $task->status,
                ]
            );

            if ($oldAssigneeIds !== $assigneeIds) {
                ProjectActivityLogger::log(
                    $project,
                    'task.assigned',
                    'مسئولین وظیفه "' . $task->title . '" تغییر کردند.',
                    $task
                );
            }

            return [$newAssigneeIds];
        });

        $task->refresh();

        $this->notifyNewAssignees($task, $newAssigneeIds);

        return back()->with('success', 'وظیفه با موفقیت ایجاد شد.');
    }

    public function updateStatus(Request $request,Task $task): RedirectResponse|JsonResponse {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'status' => ['required', 'in:todo,in_progress,review,done'],
        ]);

        $oldStatus = $task->status;

        $statusLabels = [
            'todo' => 'انجام نشده',
            'in_progress' => 'در حال انجام',
            'review' => 'بازبینی',
            'done' => 'انجام‌شده',
        ];

        $oldStatusLabel = $statusLabels[$oldStatus] ?? $oldStatus;
        $newStatusLabel = $statusLabels[$validated['status']];

        $task->update([
            'status' => $validated['status'],
        ]);

        if ($oldStatus !== $validated['status']) {
            $task->loadMissing([
                'project.members',
                'project.owner',
            ]);

            ProjectActivityLogger::log(
                $task->project,
                'task.status_changed',
                'انتقال یافت "' . $task->title . '" از ' .
                    $oldStatusLabel . ' به ' . $newStatusLabel,
                $task,
                [
                    'old_status' => $oldStatusLabel,
                    'new_status' => $newStatusLabel,
                ]
            );

            $recipients = $task->project->members
                ->push($task->project->owner)
                ->unique('id')
                ->reject(
                    fn ($user) => (int) $user->id === (int) auth()->id()
                )
                ->values();

            Notification::send(
                $recipients,
                new TaskStatusChangedNotification(
                    $task->fresh(),
                    $oldStatusLabel,
                    $newStatusLabel
                )
            );
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $task->status,
            ]);
        }

        return back()->with('success', 'وضعیت وظیفه با موفقیت تغییر کرد.');
    }

    public function show(Task $task): View
    {
        $this->authorize('view', $task);

        $task->load([
            'project.owner',
            'project.members',
            'users',
            'messages.user',
            'messages.attachments',
        ]);

        $messages = $task->messages()
            ->with(['user', 'attachments'])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        return view('tasks.show', compact('task', 'messages'));
    }

    public function destroy(Project $project, Task $task): RedirectResponse
    {
        abort_if($task->project_id !== $project->id, 404);

        $this->authorize('delete', $task);

        $title = $task->title;

        DB::transaction(function () use ($task, $project, $title) {
            $task->delete();

            ProjectActivityLogger::log(
                $project,
                'task.deleted',
                'حذف شد "' . $title . '"'
            );
        });

        return redirect()
            ->route('projects.show', [
                'project' => $project,
                'tab' => 'tasks',
            ])
            ->with('success', 'وظیفه با موفقیت حذف شد.');
    }
}
