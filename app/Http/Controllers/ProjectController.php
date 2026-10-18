<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::query()
            ->where(function ($query) {
                $query->where('owner_id', auth()->id())
                    ->orWhereHas('members', function ($query) {
                        $query->where('users.id', auth()->id());
                    });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->with('owner')
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('projects.create');
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = Project::create([
            ...$request->validated(),
            'owner_id' => auth()->id(),
        ]);

        $project->members()->syncWithoutDetaching([
            auth()->id() => ['role' => 'manager'],
        ]);

        ProjectActivityLogger::log(
            $project,
            'project.created',
            'پروژه با موفقیت ایجاد شد'
        );

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'پروژه با موفقیت ایجاد شد.');
    }

    public function show(Request $request, Project $project): View
    {
        $this->authorize('view', $project);

        $project->load(['owner', 'members']);

        $project->loadCount([
            'tasks',
            'tasks as done_tasks_count' => fn ($query) =>
                $query->where('status', 'done'),
        ]);

        $query = $project->tasks()
            ->with('assignedUser');

        $query->when($request->filled('priority'), function ($query) use ($request) {
            $query->where('priority', $request->priority);
        });

        $query->when($request->filled('status'), function ($query) use ($request) {
            $query->where('status', $request->status);
        });

        $sort = $request->get('sort', 'latest');

        match ($sort) {
            'oldest' => $query->oldest(),

            'due_soon' => $query->orderByRaw(
                'due_date IS NULL, due_date ASC'
            ),

            'due_late' => $query->orderByRaw(
                'due_date IS NULL, due_date DESC'
            ),

            default => $query->latest(),
        };

        $taskCollection = $query->get();

        $tasks = [
            'todo' => $taskCollection->where('status', 'todo')->values(),
            'in_progress' => $taskCollection->where('status', 'in_progress')->values(),
            'review' => $taskCollection->where('status', 'review')->values(),
            'done' => $taskCollection->where('status', 'done')->values(),
        ];

        $activities = $project->activities()
            ->with('user')
            ->latest()
            ->limit(50)
            ->get();

        return view('projects.show', compact(
            'project',
            'tasks',
            'activities'
        ));
    }

    public function edit(Project $project): View
    {
        $this->authorize('edit', $project);

        return view('projects.edit', compact('project'));
    }

    public function update(
        UpdateProjectRequest $request,
        Project $project
    ): RedirectResponse {
        $this->authorize('edit', $project);

        $project->update($request->validated());

        ProjectActivityLogger::log(
            $project,
            'project.updated',
            'پروژه با موفقیت به‌روزرسانی شد'
        );

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'پروژه با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $project->delete();


        return redirect()
            ->route('projects.index')
            ->with('success', 'پروژه با موفقیت حذف شد.');
    }
}