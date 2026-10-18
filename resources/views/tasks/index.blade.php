<head>
    <title>وظیفه ها | مدیریت پروژه</title>
</head>
<x-dashboard-layout>
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
      <h1 class="text-xl sm:text-2xl font-bold text-slate-900">وظایف</h1>
      <p class="text-slate-500 text-sm mt-1">پیگیری و مدیریت همه وظایف</p>
    </div>
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
      class="px-4 py-2 rounded-xl text-sm font-medium transition duration-200
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
              if ($task->due_date->isToday()) $dueLabel = 'امروز';
              elseif ($task->due_date->isTomorrow()) $dueLabel = 'فردا';
              elseif ($task->due_date->isYesterday()) $dueLabel = 'دیروز';
              else $dueLabel = $task->due_date->format('Y/m/d');
            }
          @endphp

          <tr class="hover:bg-slate-50/80 transition">
            <td class="px-5 py-4">
              <a href="{{ route('tasks.show', $task->uid) }}"
                class="font-medium {{ $isDone ? 'text-slate-400 line-through' : 'text-slate-900' }} hover:text-blue-600 transition">
                {{ $task->title }}
              </a>
            </td>
            <td class="px-5 py-4 {{ $isDone ? 'text-slate-400' : 'text-slate-500' }}">
              {{ $task->project?->name ?? '—' }}
            </td>
            <td class="px-5 py-4">
              <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $priorityClasses[$task->priority] ?? 'bg-slate-100 text-slate-600' }}">
                {{ $priorityLabels[$task->priority] ?? $task->priority }}
              </span>
            </td>
            <td class="px-5 py-4">
              @if($task->assignee)
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center text-xs font-medium">
                  {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                </div>
                <span class="{{ $isDone ? 'text-slate-400' : 'text-slate-700' }}">{{ $task->assignee->name }}</span>
              </div>
              @else
              <span class="text-slate-400">—</span>
              @endif
            </td>
            <td class="px-5 py-4 {{ $isDone ? 'text-slate-400' : 'text-slate-500' }}">{{ $dueLabel }}</td>
            <td class="px-5 py-4">
              <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClasses[$task->status] ?? 'bg-slate-100 text-slate-600' }}">
                {{ $statusLabels[$task->status] ?? $task->status }}
              </span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="px-5 py-16 text-center">
              <p class="text-sm font-medium text-slate-900">وظیفه‌ای پیدا نشد</p>
              <p class="text-sm text-slate-500 mt-1">وظایف پروژه‌ها اینجا نمایش داده می‌شوند.</p>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($tasks->hasPages())
  <div class="mt-6">{{ $tasks->links() }}</div>
  @endif
</x-dashboard-layout>