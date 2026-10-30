
<head>
    <title>وظیفه ها | مدیریت پروژه</title>
</head>

<x-dashboard-layout>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">وظایف</h1>
            <p class="text-slate-500 text-sm mt-1">پیگیری و مدیریت همه وظایف</p>
        </div>

        <a href="{{ route('tasks.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            وظیفه جدید
        </a>
    </div>

    @php
        $filters = [
            '' => 'همه',
            'todo' => 'انجام نشده',
            'in_progress' => 'در حال انجام',
            'review' => 'بازبینی',
            'done' => 'انجام‌شده',
        ];

        $current = $status ?? '';
    @endphp

    <div class="flex flex-wrap items-center gap-2 mb-6">
        @foreach($filters as $value => $label)
            <a href="{{ route('tasks.index', array_filter(['status' => $value ?: null])) }}"
                class="px-4 py-2 rounded-xl text-sm font-medium
                {{ $current === $value
                    ? 'bg-blue-600 text-white'
                    : 'bg-white border border-slate-200 text-slate-600 hover:border-blue-200 hover:text-blue-700' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-right text-slate-400 border-b border-slate-100">
                        <th class="px-5 py-3.5 font-medium">وظیفه</th>
                        <th class="px-5 py-3.5 font-medium">پروژه</th>
                        <th class="px-5 py-3.5 font-medium">اولویت</th>
                        <th class="px-5 py-3.5 font-medium">مسئول</th>
                        <th class="px-5 py-3.5 font-medium">موعد</th>
                        <th class="px-5 py-3.5 font-medium">وضعیت</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-50">
                    @forelse($tasks as $task)

                        @php
                            $isDone = $task->status === 'done';

                            $priorityClasses = [
                                'high' => 'bg-red-50 text-red-700',
                                'medium' => 'bg-orange-50 text-orange-700',
                                'low' => 'bg-emerald-50 text-emerald-700',
                            ];

                            $priorityLabels = [
                                'high' => 'بالا',
                                'medium' => 'متوسط',
                                'low' => 'پایین',
                            ];

                            $statusClasses = [
                                'todo' => 'bg-slate-100 text-slate-600',
                                'in_progress' => 'bg-blue-50 text-blue-700',
                                'review' => 'bg-violet-50 text-violet-700',
                                'done' => 'bg-emerald-50 text-emerald-700',
                            ];

                            $statusLabels = [
                                'todo' => 'انجام نشده',
                                'in_progress' => 'در حال انجام',
                                'review' => 'بازبینی',
                                'done' => 'انجام‌شده',
                            ];

                            $dueLabel = '—';

                            if ($task->due_date) {
                                if ($task->due_date->isToday()) {
                                    $dueLabel = 'امروز';
                                } elseif ($task->due_date->isTomorrow()) {
                                    $dueLabel = 'فردا';
                                } elseif ($task->due_date->isYesterday()) {
                                    $dueLabel = 'دیروز';
                                } else {
                                    $dueLabel = verta($task->due_date)->format('Y/m/d');
                                }
                            }
                        @endphp

                        <tr class="hover:bg-slate-50">
                            {{-- عنوان وظیفه و ویرایش --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2 min-w-52">
                                    <a href="{{ route('tasks.show', $task->uid) }}"
                                        class="font-medium {{ $isDone ? 'text-slate-400 line-through' : 'text-slate-900' }} hover:text-blue-600">
                                        {{ $task->title }}
                                    </a>

                                    <a href="{{ route('projects.tasks.edit', [
                                        'project' => $task->project,
                                        'task' => $task
                                    ]) }}"
                                        title="ویرایش وظیفه"
                                        aria-label="ویرایش وظیفه"
                                        class="shrink-0 text-slate-400 hover:text-blue-600 p-1">

                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685 8.932-8.931z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>

                            {{-- پروژه --}}
                            <td class="px-5 py-4 {{ $isDone ? 'text-slate-400' : 'text-slate-500' }}">
                                {{ $task->project?->name ?? '—' }}
                            </td>

                            {{-- اولویت --}}
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $priorityClasses[$task->priority] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $priorityLabels[$task->priority] ?? $task->priority }}
                                </span>
                            </td>

                            {{-- مسئولین --}}
                            <td class="px-5 py-4">
                                @forelse($task->users as $user)
                                    <div class="flex items-center gap-2 mb-2 last:mb-0">

                                        @if($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar))
                                            <img
                                                src="{{ asset('storage/' . $user->avatar) }}"
                                                alt="{{ $user->name }}"
                                                class="w-7 h-7 rounded-full object-cover shrink-0">
                                        @else
                                            <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center text-xs font-semibold shrink-0">
                                                {{ mb_substr($user->name, 0, 1) }}
                                            </div>
                                        @endif

                                        <span class="{{ $isDone ? 'text-slate-400' : 'text-slate-700' }}">
                                            {{ $user->name }}
                                        </span>
                                    </div>
                                @empty
                                    <span class="text-slate-400">بدون مسئول</span>
                                @endforelse
                            </td>

                            {{-- موعد --}}
                            <td class="px-5 py-4 {{ $isDone ? 'text-slate-400' : 'text-slate-500' }}">
                                {{ $dueLabel }}
                            </td>

                            {{-- وضعیت --}}
                            <td class="px-5 py-4">
                                <form action="{{ route('tasks.update-status', $task) }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <select
                                        name="status"
                                        onchange="this.form.submit()"
                                        class="rounded-md border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700 outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                                    >
                                        <option value="todo" @selected($task->status === 'todo')>انجام نشده</option>
                                        <option value="in_progress" @selected($task->status === 'in_progress')>در حال انجام</option>
                                        <option value="review" @selected($task->status === 'review')>در انتظار بررسی</option>
                                        <option value="done" @selected($task->status === 'done')>انجام شده</option>
                                    </select>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <p class="text-sm font-medium text-slate-900">وظیفه‌ای پیدا نشد</p>
                                <p class="text-sm text-slate-500 mt-1">
                                    وظایف پروژه‌ها اینجا نمایش داده می‌شوند.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($tasks->hasPages())
        <div class="mt-6">
            {{ $tasks->links() }}
        </div>
    @endif

</x-dashboard-layout>
