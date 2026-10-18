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
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;
 
class TaskController extends Controller
{
    public function store(StoreTaskRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('create', $project);

        $data = $request->validated();

        $task = $project->tasks()->create([
            ...$data,
            'assigned_to' => $data['assigned_to'] ?? auth()->id(),
        ]);

        if (
            !empty($data['assigned_to']) &&
            (int) $data['assigned_to'] !== (int) auth()->id()
        ) {
            $assignee = User::find($data['assigned_to']);

            if ($assignee && $assignee->notify_task_assigned) {
                $assignee->notify(new TaskAssignedNotification($task));
            }
        }

        $status = $task->status === 'todo' ? 'انجام نشده' :
                  ($task->status === 'in_progress' ? 'در حال انجام' :
                   ($task->status === 'review' ? 'بازبینی' : 'انجام‌شده'));

        ProjectActivityLogger::log(
            $project,
            'task.created',
            'ایجاد شد "' . $task->title . '"',
            $task,
            ['status' => $status]
        );

        return back()->with('success', 'تکلیف با موفقیت ایجاد شد.');
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task): RedirectResponse
    {
        abort_if($task->project_id !== $project->id, 404);

        $this->authorize('update', $task);

        $data = $request->validated();
        $oldAssignee = $task->assigned_to;
        $oldStatus = $task->status;

        $task->update($data);

        // فارسی کردن استتوس قدیمی
        $oldStatusLabel = $oldStatus === 'todo' ? 'انجام نشده' :
                         ($oldStatus === 'in_progress' ? 'در حال انجام' :
                          ($oldStatus === 'review' ? 'بازبینی' : 'انجام‌شده'));

        $status = $task->status === 'todo' ? 'انجام نشده' :
                  ($task->status === 'in_progress' ? 'در حال انجام' :
                   ($task->status === 'review' ? 'بازبینی' : 'انجام‌شده'));

        ProjectActivityLogger::log(
            $project,
            'task.updated',
            'به‌روزرسانی شد "' . $task->title . '"',
            $task,
            ['old_status' => $oldStatusLabel, 'new_status' => $status]
        );

        // منطق تغییر مأمور (بدون تغییر)
        if (
            array_key_exists('assigned_to', $data) &&
            (int) $data['assigned_to'] !== (int) $oldAssignee
        ) {
            $assignee = User::find($data['assigned_to']);

            if (
                $assignee &&
                (int) $assignee->id !== (int) auth()->id() &&
                $assignee->notify_task_assigned
            ) {
                $assignee->notify(
                    new TaskAssignedNotification($task->fresh())
                );
            }

            ProjectActivityLogger::log(
                $project,
                'task.assigned',
                'مأمور شد "' . $task->title . '" به ' .
                    ($assignee?->name ?? 'someone'),
                $task
            );
        }

        return redirect()
            ->route('projects.show', [
                'project' => $project,
                'tab' => 'tasks',
            ])
            ->with('success', 'تکلیف با موفقیت به‌روزرسانی شد.');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,review,done',
        ]);

        $oldStatus = $task->status;
        $oldStatusLabel = $oldStatus === 'todo' ? 'انجام نشده' :
                         ($oldStatus === 'in_progress' ? 'در حال انجام' :
                          ($oldStatus === 'review' ? 'بازبینی' : 'انجام‌شده'));

        $task->update([
            'status' => $validated['status'],
        ]);

        $status = $validated['status'] === 'todo' ? 'انجام نشده' :
                  ($validated['status'] === 'in_progress' ? 'در حال انجام' :
                   ($validated['status'] === 'review' ? 'بازبینی' : 'انجام‌شده'));

        if ($oldStatus !== $validated['status']) {
            $task->loadMissing([
                'project.members',
                'project.owner',
            ]);

            ProjectActivityLogger::log(
                $task->project,
                'task.status_changed',
                'انتقال یافت "' . $task->title . '" از ' . $oldStatusLabel . ' به ' . $status,
                $task,
                [
                    'old_status' => $oldStatusLabel,
                    'new_status' => $status,
                ]
            );

            $recipients = $task->project->members
                ->push($task->project->owner)
                ->unique('id')
                ->reject(fn ($user) => (int) $user->id === (int) auth()->id())
                ->values();

            Notification::send(
                $recipients,
                new TaskStatusChangedNotification(
                    $task->fresh(),
                    $oldStatusLabel,
                    $status
                )
            );
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $task->status,
            ]);
        }

        return back()->with(
            'success',
            'وضعیت تکلیف با موفقیت به‌روزرسانی شد.'
        );
    }

    public function destroy(Project $project, Task $task): RedirectResponse
    {
        abort_if($task->project_id !== $project->id, 404);

        $this->authorize('delete', $task);

        $title = $task->title;

        $task->delete();

        ProjectActivityLogger::log(
            $project,
            'task.deleted',
            'حذف شد "' . $title . '"'
        );

        return back()->with(
            'success',
            'تکلیف با موفقیت حذف شد.'
        );
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
            ->pluck('id');

        $query = Task::query()
            ->whereIn('project_id', $projectIds)
            ->with([
                'project',
                'assignedUser',
            ]);

        $status = $request->get('status');

        if (
            $status &&
            in_array(
                $status,
                ['todo', 'in_progress', 'review', 'done'],
                true
            )
        ) {
            $query->where('status', $status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tasks = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('tasks.index', compact('tasks', 'status'));
    }

    
    public function show(Task $task): \Illuminate\Contracts\View\View
    {
        $this->authorize('view', $task);

        $task->load([
            'project.owner',
            'project.members',
            'assignedUser',
        ]);

        $messages = $task->messages()
            ->with([
                'user',
                'attachments',
            ])
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('tasks.show', compact('task', 'messages'));
    }

}