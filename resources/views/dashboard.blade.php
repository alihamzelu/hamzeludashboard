<head>
    <title>داشبورد | مدیریت پروژه</title>
</head>
<x-dashboard-layout>
  <div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900">داشبورد</h1>
    <p class="text-slate-500 text-sm mt-1">
      خوش آمدید {{ auth()->user()->name }}. امروز اوضاع این‌طور است.
    </p>
  </div>

  {{-- Stats --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <a href="{{ route('projects.index', ['status' => 'active']) }}">
      <div class="bg-white rounded-2xl border border-slate-200 p-5 card-lift">
        <p class="text-sm text-slate-500 mb-3">پروژه‌های فعال</p>
        <p class="text-2xl font-bold text-slate-900">{{ $stats['active_projects'] }}</p>
      </div>
    </a>
    <a href="{{ route('tasks.index' , ['status' => 'todo']) }}">
      <div class="bg-white rounded-2xl border border-slate-200 p-5 card-lift">
        <p class="text-sm text-slate-500 mb-3">وظایف در انتظار</p>
        <p class="text-2xl font-bold text-slate-900">{{ $stats['pending_tasks'] }}</p>
        <p class="text-xs text-blue-600 mt-1">{{ $stats['due_today'] }} موعد امروز</p>
      </div>
    </a>
    <a href="{{ route('team.index') }}">
      <div class="bg-white rounded-2xl border border-slate-200 p-5 card-lift">
        <p class="text-sm text-slate-500 mb-3">اعضای تیم</p>
        <p class="text-2xl font-bold text-slate-900">{{ $stats['team_members'] }}</p>
      </div>
    </a>
    <a href="{{ route('projects.index', ['status' => 'completed']) }}">
      <div class="bg-blue-600 rounded-2xl p-5 text-white card-lift">
        <p class="text-sm text-white/80 mb-3">تکمیل‌شده</p>
        <p class="text-2xl font-bold">{{ $stats['completed_projects'] }}</p>
        <p class="text-xs text-white/70 mt-1">پروژه</p>
      </div>
    </a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6">
      <h2 class="font-semibold text-slate-900 mb-5">پیشرفت پروژه‌ها</h2>
      <div class="space-y-4">
        @forelse($activeProjects->take(5) as $project)
        <div>
          <div class="flex justify-between text-sm mb-1.5">
            <a href="{{ route('projects.show', $project) }}" class="font-medium text-slate-800 hover:text-blue-600 transition">
              {{ $project->name }}
            </a>
            <span class="text-slate-400">{{ $project->progress }}٪</span>
          </div>
          <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-blue-600 rounded-full transition-all duration-500" style="width: {{ $project->progress }}%"></div>
          </div>
        </div>
        @empty
        <p class="text-sm text-slate-400">هنوز پروژه فعالی وجود ندارد.</p>
        @endforelse
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6">
      <h2 class="font-semibold text-slate-900 mb-5">موعدهای پیش‌رو</h2>
      <div class="space-y-4">
        @forelse($upcomingDeadlines as $task)
        @php
          $dot = $task->due_date->isToday() ? 'bg-red-500' : ($task->due_date->isTomorrow() ? 'bg-orange-400' : 'bg-blue-600');
          $when = $task->due_date->isToday() ? 'امروز' : ($task->due_date->isTomorrow() ? 'فردا' : $task->due_date->format('Y/m/d'));
        @endphp
        <div class="flex items-start gap-3">
          <span class="w-1.5 h-1.5 rounded-full {{ $dot }} mt-2 flex-shrink-0"></span>
          <div>
            <a href="{{ route('tasks.show', $task) }}" class="text-sm font-medium hover:text-blue-600 transition">{{ $task->title }}</a>
            <p class="text-xs text-slate-400">{{ $when }} · {{ $task->project?->name }}</p>
          </div>
        </div>
        @empty
        <p class="text-sm text-slate-400">موعد پیش‌رویی وجود ندارد.</p>
        @endforelse
      </div>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-8">
      <div class="flex items-center justify-between mb-5">
          <h2 class="font-semibold text-slate-900">پروژه‌ها</h2>
          <a href="{{ route('projects.index') }}" class="text-sm text-blue-600 hover:text-blue-700 transition">مشاهده همه</a>
      </div>

      <div class="overflow-x-auto">
          <table class="w-full text-sm">
              <thead>
                  <tr class="text-right text-slate-400 border-b border-slate-100">
                      <th class="pb-3 font-medium">نام پروژه</th>
                      <th class="pb-3 font-medium">وضعیت</th>
                      <th class="pb-3 font-medium">تیم</th>
                      <th class="pb-3 font-medium">موعد</th>
                      <th class="pb-3 font-medium">پیشرفت</th>
                  </tr>
              </thead>

              <tbody class="divide-y divide-slate-50">
                  @forelse($projects->take(5) as $project)
                      @php
                          $progress = $project->tasks_count > 0
                              ? (int) round(($project->done_tasks_count / $project->tasks_count) * 100)
                              : 0;

                          $statusClasses = [
                              'planning' => 'bg-slate-100 text-slate-600',
                              'active' => 'bg-emerald-50 text-emerald-700',
                              'completed' => 'bg-blue-50 text-blue-700',
                              'archived' => 'bg-orange-50 text-orange-600',
                          ];

                          $statusLabels = [
                              'planning' => 'برنامه‌ریزی',
                              'active' => 'فعال',
                              'completed' => 'تکمیل‌شده',
                              'archived' => 'آرشیو',
                          ];
                      @endphp

                      <tr class="hover:bg-slate-50/80 transition">
                          <td class="py-3.5 font-medium">
                              <a href="{{ route('projects.show', $project) }}" class="hover:text-blue-600 transition">
                                  {{ $project->name }}
                              </a>
                          </td>

                          <td class="py-3.5">
                              <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClasses[$project->status] ?? 'bg-slate-100 text-slate-600' }}">
                                  {{ $statusLabels[$project->status] ?? $project->status }}
                              </span>
                          </td>

                          <td class="py-3.5">
                              <div class="flex -space-x-2 space-x-reverse">
                                  @foreach($project->members->take(3) as $member)
                                      <div class="w-7 h-7 rounded-full overflow-hidden bg-blue-50 text-blue-700 border-2 border-white flex items-center justify-center text-xs flex-shrink-0"
                                          title="{{ $member->name }}">
                                          @if($member->avatar)
                                              <img
                                                  src="{{ asset('storage/' . $member->avatar) }}"
                                                  alt="{{ $member->name }}"
                                                  class="w-full h-full object-cover"
                                              >
                                          @else
                                              {{ mb_substr($member->name, 0, 1) }}
                                          @endif
                                      </div>
                                  @endforeach

                                  @if($project->members->count() > 3)
                                      <div class="w-7 h-7 rounded-full bg-blue-600 border-2 border-white flex items-center justify-center text-xs text-white flex-shrink-0">
                                          +{{ $project->members->count() - 3 }}
                                      </div>
                                  @endif
                              </div>
                          </td>

                          <td class="py-3.5 text-slate-500">
                              {{ $project->end_date ? verta($project->end_date)->format('Y/m/d') : '—' }}
                          </td>

                          <td class="py-3.5">
                              <div class="flex items-center gap-2">
                                  <div class="w-20 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                      <div class="h-full bg-blue-600 rounded-full" style="width: {{ $progress }}%"></div>
                                  </div>
                                  <span class="text-xs text-slate-400">{{ $progress }}٪</span>
                              </div>
                          </td>
                      </tr>
                  @empty
                      <tr>
                          <td colspan="5" class="py-8 text-center text-sm text-slate-400">
                              هنوز پروژه‌ای وجود ندارد.
                          </td>
                      </tr>
                  @endforelse
              </tbody>
          </table>
      </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

      {{-- فعالیت‌های اخیر --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-6">
          <h2 class="font-semibold text-slate-900 mb-5">فعالیت‌های اخیر</h2>

          <div class="space-y-4">
              @forelse($recentActivities as $activity)
                  <div class="flex gap-3">
                      <div class="w-8 h-8 rounded-full overflow-hidden bg-blue-50 text-blue-700 flex items-center justify-center text-xs font-medium flex-shrink-0">
                          @if($activity->user?->avatar)
                              <img
                                  src="{{ asset('storage/' . $activity->user->avatar) }}"
                                  alt="{{ $activity->user->name }}"
                                  class="w-full h-full object-cover"
                              >
                          @else
                              {{ mb_substr($activity->user?->name ?? 'س', 0, 1) }}
                          @endif
                      </div>

                      <div class="min-w-0">
                          <p class="text-sm">
                              <span class="font-medium">{{ $activity->user?->name ?? 'سیستم' }}</span>
                              {{ $activity->description }}
                          </p>
                          <p class="text-xs text-slate-400 mt-0.5">
                              {{ $activity->created_at->locale('fa')->diffForHumans() }}
                          </p>
                      </div>
                  </div>
              @empty
                  <p class="text-sm text-slate-400">فعالیت اخیری وجود ندارد.</p>
              @endforelse
          </div>
      </div>

      {{-- اعضای تیم --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-6">
          <div class="flex items-center justify-between mb-5">
              <h2 class="font-semibold text-slate-900">اعضای تیم</h2>
              <a href="{{ route('team.index') }}" class="text-xs text-blue-600 transition">
                  مشاهده همه
              </a>
          </div>

          <div class="space-y-4">
              @forelse($teamMembers as $member)
                  <div class="flex items-center gap-3">
                      <div class="w-9 h-9 rounded-full overflow-hidden bg-blue-50 text-blue-700 flex items-center justify-center text-sm font-medium flex-shrink-0">
                          @if($member->avatar)
                              <img
                                  src="{{ asset('storage/' . $member->avatar) }}"
                                  alt="{{ $member->name }}"
                                  class="w-full h-full object-cover"
                              >
                          @else
                              {{ mb_substr($member->name, 0, 1) }}
                          @endif
                      </div>

                      <div class="min-w-0">
                          <p class="text-sm font-medium truncate">{{ $member->name }}</p>
                          <p class="text-xs text-slate-400 truncate">{{ $member->email }}</p>
                      </div>
                  </div>
              @empty
                  <p class="text-sm text-slate-400">هنوز هم‌تیمی‌ای ندارید.</p>
              @endforelse
          </div>
      </div>

      {{-- وظایف امروز --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-6">
          <h2 class="font-semibold text-slate-900 mb-5">وظایف امروز</h2>

          <div class="space-y-3">
              @forelse($todayTasks as $task)
                  <div class="flex items-center gap-3">
                      <span @class([
                          'w-4 h-4 rounded border flex-shrink-0',
                          'bg-blue-600 border-blue-600' => $task->status === 'done',
                          'border-slate-300' => $task->status !== 'done',
                      ])></span>

                      <a href="{{ route('tasks.show', $task) }}"
                        @class([
                            'text-sm transition',
                            'text-slate-400 line-through' => $task->status === 'done',
                            'text-slate-800 hover:text-blue-600' => $task->status !== 'done',
                        ])>
                          {{ $task->title }}
                      </a>
                  </div>
              @empty
                  <p class="text-sm text-slate-400">وظیفه‌ای برای امروز نیست.</p>
              @endforelse
          </div>
      </div>

  </div>

@php
    $faMonths = [
        1 => 'فروردین',
        2 => 'اردیبهشت',
        3 => 'خرداد',
        4 => 'تیر',
        5 => 'مرداد',
        6 => 'شهریور',
        7 => 'مهر',
        8 => 'آبان',
        9 => 'آذر',
        10 => 'دی',
        11 => 'بهمن',
        12 => 'اسفند',
    ];

    $weekDays = [
        'شنبه',
        'یکشنبه',
        'دوشنبه',
        'سه‌شنبه',
        'چهارشنبه',
        'پنجشنبه',
        'جمعه',
    ];
@endphp

<div class="bg-white rounded-2xl border border-slate-200 p-6">

    {{-- عنوان تقویم --}}
    <div class="flex items-center justify-between mb-5">
        <h2 class="font-semibold text-slate-900">
            {{ $faMonths[$month] ?? '' }} {{ $year }}
        </h2>

        <a
            href="{{ route('calendar.index') }}"
            class="text-sm text-blue-600 transition hover:text-blue-700"
        >
            تقویم کامل
        </a>
    </div>

    {{-- روزهای هفته --}}
    <div class="grid grid-cols-7 gap-1 text-center text-xs mb-2">
        @foreach($weekDays as $dayName)
            <div class="text-slate-400 py-2">
                {{ $dayName }}
            </div>
        @endforeach
    </div>

    {{-- روزهای ماه --}}
    <div class="grid grid-cols-7 gap-1 text-center text-sm">

        @foreach($calendarDays as $day)

            <div
                @class([
                    'py-2 relative rounded-lg transition',

                    // روزهای ماه قبل و بعد
                    'text-slate-300' =>
                        !$day['is_current_month'],

                    // امروز
                    'bg-blue-600 text-white font-medium' =>
                        $day['is_today'],

                    // هاور روزهای ماه جاری
                    'hover:bg-blue-50' =>
                        $day['is_current_month']
                        && !$day['is_today'],
                ])
            >

                {{-- شماره روز شمسی --}}
                {{ $day['day_number'] }}

                {{-- علامت داشتن تسک --}}
                @if(
                    $day['has_due']
                    && !$day['is_today']
                    && $day['is_current_month']
                )
                    <span
                        class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 bg-blue-500 rounded-full"
                    ></span>
                @endif

            </div>

        @endforeach

    </div>

</div>

</x-dashboard-layout>