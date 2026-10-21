<head>
    <title>وظیفه {{ \Illuminate\Support\Str::limit($project->name, 30) }} | مدیریت پروژه</title>
</head>
<x-dashboard-layout>
  <a href="{{ route('projects.index') }}"
    class="text-sm text-gray-500 hover:text-slate-900 inline-flex items-center gap-1 mb-4">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
    </svg>
    بازگشت به پروژه‌ها
  </a>

  @php
  $statusClasses = [
  'planning' => 'bg-gray-100 text-gray-700',
  'active' => 'bg-green-50 text-green-700',
  'completed' => 'bg-blue-50 text-blue-700',
  'archived' => 'bg-orange-50 text-orange-700',
  ];
  $statusLabels = [
  'planning' => 'برنامه‌ریزی',
  'active' => 'فعال',
  'completed' => 'تکمیل‌شده',
  'archived' => 'آرشیو',
  ];
  $tab = request('tab', 'overview');
  @endphp

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
    <div>
      <div class="flex items-center gap-3 mb-2">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">{{ $project->name }}</h1>
        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClasses[$project->status] ?? 'bg-gray-100 text-gray-700' }}">
          {{ $statusLabels[$project->status] ?? $project->status }}
        </span>
      </div>
      <p class="text-sm text-gray-500">
        @if($project->end_date)
        موعد {{ $project->end_date ? verta($project->end_date)->format('Y/m/d') : '—' }}
        @else
        بدون موعد
        @endif
      </p>
    </div>

  </div>

  {{-- Tabs --}}
  <div class="border-b border-slate-200 mb-6">
    <nav class="flex gap-1 overflow-x-auto">
      @foreach([
      'overview' => 'نمای کلی',
      'members' => 'اعضا',
      'tasks' => 'وظایف',
      'activity' => 'فعالیت‌ها',
      'settings' => 'تنظیمات',
      ] as $key => $label)
      <a href="{{ route('projects.show', ['project' => $project, 'tab' => $key]) }}"
        class="px-4 py-2.5 text-sm font-medium whitespace-nowrap border-b-2 transition
            {{ $tab === $key
              ? 'border-blue-600 text-slate-900'
              : 'border-transparent text-gray-500 hover:text-slate-900 hover:border-gray-300' }}">
        {{ $label }}
      </a>
      @endforeach
    </nav>
  </div>

  {{-- ===================== OVERVIEW ===================== --}}
  @if($tab === 'overview')
  @php
  $todoCount = count($tasks['todo'] ?? []);
  $inProgressCount = count($tasks['in_progress'] ?? []);
  $reviewCount = count($tasks['review'] ?? []);
  $doneCount = count($tasks['done'] ?? []);
  $totalTasks = $todoCount + $inProgressCount + $reviewCount + $doneCount;
  $progress = $totalTasks > 0 ? (int) round(($doneCount / $totalTasks) * 100) : (int) ($project->progress ?? 0);

  $recentTasks = collect($tasks)
  ->flatten()
  ->sortByDesc(fn ($t) => $t->updated_at)
  ->take(4);

  $taskStatusClasses = [
  'todo' => 'bg-gray-100 text-gray-600',
  'in_progress' => 'bg-blue-50 text-blue-700',
  'review' => 'bg-purple-50 text-purple-700',
  'done' => 'bg-green-50 text-green-700',
  ];
  $taskStatusLabels = [
  'todo' => 'انجام نشده',
  'in_progress' => 'در حال انجام',
  'review' => 'بازبینی',
  'done' => 'انجام‌شده',
  ];
  @endphp

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 space-y-6">
      <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-900 mb-3">درباره پروژه</h2>
        <p class="text-sm text-gray-600 leading-relaxed">
          {{ $project->description ?: 'توضیحی ثبت نشده است.' }}
        </p>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-3">
          <h2 class="font-semibold text-slate-900">پیشرفت</h2>
          <span class="text-sm font-medium text-gray-700">{{ $progress }}٪</span>
        </div>
        <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden mb-4">
          <div class="h-full bg-blue-600 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
          <div class="rounded-xl bg-gray-50 p-3">
            <p class="text-lg font-bold text-slate-900">{{ $todoCount }}</p>
            <p class="text-xs text-gray-500">انجام نشده</p>
          </div>
          <div class="rounded-xl bg-blue-50 p-3">
            <p class="text-lg font-bold text-blue-700">{{ $inProgressCount }}</p>
            <p class="text-xs text-blue-600">در حال انجام</p>
          </div>
          <div class="rounded-xl bg-purple-50 p-3">
            <p class="text-lg font-bold text-purple-700">{{ $reviewCount }}</p>
            <p class="text-xs text-purple-600">بازبینی</p>
          </div>
          <div class="rounded-xl bg-green-50 p-3">
            <p class="text-lg font-bold text-green-700">{{ $doneCount }}</p>
            <p class="text-xs text-green-600">انجام‌شده</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-slate-900">وظایف اخیر</h2>
          <a href="{{ route('projects.show', ['project' => $project, 'tab' => 'tasks']) }}"
            class="text-sm text-blue-600 font-medium hover:underline">مشاهده بورد ←</a>
        </div>
        <div class="space-y-3">
          @forelse($recentTasks as $task)
          <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
            <span @class([ 'w-4 h-4 rounded border flex-shrink-0' , 'bg-blue-600 border-blue-600'=> $task->status === 'done',
              'border-gray-300' => $task->status !== 'done',
              ])></span>
            <span @class([ 'text-sm flex-1 truncate' , 'text-gray-400 line-through'=> $task->status === 'done',
              'text-slate-900' => $task->status !== 'done',
              ])>{{ $task->title }}</span>
            <span class="text-xs px-2 py-0.5 rounded-full {{ $taskStatusClasses[$task->status] ?? 'bg-gray-100 text-gray-600' }}">
              {{ $taskStatusLabels[$task->status] ?? $task->status }}
            </span>
          </div>
          @empty
          <p class="text-sm text-gray-400">هنوز وظیفه‌ای نیست.</p>
          @endforelse
        </div>
      </div>
    </div>

    <div class="space-y-6">
      <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-900 mb-4">جزئیات</h2>
        <dl class="space-y-3 text-sm">
          <div class="flex justify-between gap-3">
            <dt class="text-gray-500">مالک</dt>
            <dd class="font-medium text-slate-900">{{ $project->owner->name ?? '—' }}</dd>
          </div>
          <div class="flex justify-between gap-3">
            <dt class="text-gray-500">وضعیت</dt>
            <dd>
              <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$project->status] ?? 'bg-gray-100 text-gray-700' }}">
                {{ $statusLabels[$project->status] ?? $project->status }}
              </span>
            </dd>
          </div>
          <div class="flex justify-between gap-3">
            <dt class="text-gray-500">شروع</dt>
            <dd class="font-medium text-slate-900">
              {{ $project->start_date ? verta($project->start_date)->format('Y/m/d') : '—' }}
            </dd>
          </div>
          <div class="flex justify-between gap-3">
            <dt class="text-gray-500">موعد</dt>
            <dd class="font-medium text-slate-900">
              {{ $project->end_date ? verta($project->end_date)->format('Y/m/d') : '—' }}
            </dd>
          </div>
          <div class="flex justify-between gap-3">
            <dt class="text-gray-500">وظایف</dt>
            <dd class="font-medium text-slate-900">{{ $totalTasks }} مورد</dd>
          </div>
        </dl>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-slate-900">تیم</h2>
          <a href="{{ route('projects.show', ['project' => $project, 'tab' => 'members']) }}"
            class="text-xs font-medium bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full hover:bg-gray-200 transition">
            {{ $project->members->count() }}
          </a>
        </div>
        <div class="space-y-3">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full overflow-hidden bg-blue-600 text-white flex items-center justify-center text-xs font-semibold flex-shrink-0">
              @if($project->owner?->avatar)
              <img
                src="{{ asset('storage/' . $project->owner->avatar) }}"
                alt="{{ $project->owner->name }}"
                class="w-full h-full object-cover">
              @else
              {{ mb_substr($project->owner->name ?? 'U', 0, 1) }}
              @endif
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-medium text-slate-900 truncate">{{ $project->owner->name ?? '—' }}</p>
              <p class="text-xs text-gray-500">مالک</p>
            </div>
          </div>
          @foreach($project->members->where('id', '!=', $project->owner_id)->take(5) as $member)
          @php
          $roleLabels = ['member' => 'عضو', 'manager' => 'مدیر'];
          $role = $member->pivot->role ?? 'member';
          @endphp
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full overflow-hidden bg-gray-200 text-gray-700 flex items-center justify-center text-xs font-semibold flex-shrink-0">
              @if($member->avatar)
              <img
                src="{{ asset('storage/' . $member->avatar) }}"
                alt="{{ $member->name }}"
                class="w-full h-full object-cover">
              @else
              {{ strtoupper(substr($member->name, 0, 1)) }}
              @endif
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-medium text-slate-900 truncate">{{ $member->name }}</p>
              <p class="text-xs text-gray-500">{{ $roleLabels[$role] ?? $role }}</p>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-slate-900">فعالیت‌ها</h2>
          <a href="{{ route('projects.show', ['project' => $project, 'tab' => 'activity']) }}"
            class="text-sm text-blue-600 font-medium hover:underline">مشاهده همه ←</a>
        </div>
        <div class="space-y-4">
          @forelse(($activities ?? collect())->take(3) as $activity)
          <div class="flex gap-3">
            <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center text-xs font-medium flex-shrink-0">
              @if($activity->user?->avatar)
              <img
                src="{{ asset('storage/' . $activity->user->avatar) }}"
                alt="{{ $activity->user->name }}"
                class="w-full h-full object-cover">
              @else
              {{ mb_substr($activity->user?->name ?? 'S', 0, 1) }}
              @endif
            </div>
            <div>
              <p class="text-sm text-gray-800">
                <span class="font-medium">{{ $activity->user?->name ?? 'سیستم' }}</span>
                {{ $activity->description }}
              </p>
              <p class="text-xs text-gray-400 mt-0.5">{{ $activity->created_at->locale('fa')->diffForHumans() }}</p>
            </div>
          </div>
          @empty
          <p class="text-sm text-gray-400">هنوز فعالیتی نیست.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
  @endif



  {{-- ===================== TASKS ===================== --}}
  @if($tab === 'tasks')
  @php
  $priorityLabels = [
  'high' => 'بالا',
  'medium' => 'متوسط',
  'low' => 'پایین',
  ];
  @endphp
  <div class="flex flex-col lg:flex-row gap-6 items-start">
    <div class="flex-1 w-full">
      <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-5">بورد وظایف</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
          @foreach([
          'todo' => 'انجام نشده',
          'in_progress' => 'در حال انجام',
          'review' => 'بازبینی',
          'done' => 'انجام‌شده'
          ] as $key => $label)
          <div class="bg-gray-50 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-semibold text-slate-900 text-sm">{{ $label }}</h3>
              <span class="text-xs text-gray-400">{{ count($tasks[$key] ?? []) }}</span>
            </div>

            <div class="task-column space-y-3 min-h-[100px]" data-status="{{ $key }}">
              @foreach($tasks[$key] ?? [] as $task)
              <div class="task-card bg-white border border-slate-200 rounded-xl p-4 cursor-grab active:cursor-grabbing"
                draggable="true" data-task-id="{{ $task->uid }}">
                <h4 class="font-medium text-sm">{{ $task->title }}</h4>
                @if($task->description)
                <p class="text-xs text-gray-500 mt-2 line-clamp-2">{{ $task->description }}</p>
                @endif
                <div class="flex items-center justify-between mt-4">
                  <span class="text-xs text-gray-500">{{ $priorityLabels[$task->priority] ?? $task->priority }}</span>
                  <div class="flex items-center gap-2">
                    @if($task->due_date)
                    <span class="text-xs text-gray-400">{{ $task->due_date->format('Y/m/d') }}</span>
                    @endif
                    @can('update', $task)
                    <button type="button" onclick="openEditModal(this)"
                      data-id="{{ $task->uid }}"
                      data-title="{{ e($task->title) }}"
                      data-description="{{ e($task->description) }}"
                      data-priority="{{ $task->priority }}"
                      data-status="{{ $task->status }}"
                      data-due="{{ $task->due_date?->format('Y-m-d') }}"
                      data-assigned="{{ $task->assigned_to }}"
                      data-can-delete="{{ auth()->user()->can('delete', $task) ? 'true' : 'false' }}"
                      class="text-xs text-gray-400 hover:text-blue-600 transition">
                      ویرایش
                    </button>
                    @endcan
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="w-full lg:w-80 flex-shrink-0">
      @can('create', $project)
      <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-900 mb-4">افزودن وظیفه جدید</h2>
        <form action="{{ route('projects.tasks.store', $project) }}" method="POST">
          @csrf
          <input type="hidden" name="status" value="todo">
          <div class="flex flex-col gap-3">
            <input type="text" name="title" value="{{ old('title') }}" placeholder="عنوان وظیفه" required
              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600" />
            <textarea name="description" rows="3" placeholder="توضیحات (اختیاری)"
              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-600">{{ old('description') }}</textarea>
            <select name="priority" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white">
              <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>پایین</option>
              <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>متوسط</option>
              <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>بالا</option>
            </select>
            {{-- تاریخ سررسید شمسی --}}
            <div class="relative" data-jalali-picker>


              <div class="relative">
                <input
                  id="due_date"
                  type="text"
                  name="due_date"
                  readonly
                  autocomplete="off"
                  placeholder="انتخاب تاریخ سررسید"
                  value="{{ old('due_date') }}"
                  class="jalali-input w-full px-4 py-2.5 pl-10 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition cursor-pointer bg-white" />

                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                  fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                </svg>
              </div>

              <input
                type="hidden"
                name="due_date_gregorian"
                class="gregorian-value"
                value="{{ old('due_date') }}" />

              <div class="jalali-calendar hidden absolute z-50 top-full mt-2 right-0 w-80 max-w-full rounded-2xl border border-slate-200 bg-white p-4 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                  <button type="button"
                    class="prev-month rounded-lg p-2 hover:bg-slate-100 text-slate-600"
                    aria-label="ماه قبل">
                    &#10095;
                  </button>

                  <div class="text-center">
                    <div class="calendar-title font-bold text-slate-800"></div>
                    <div class="text-xs text-slate-400 mt-1">تقویم شمسی</div>
                  </div>

                  <button type="button"
                    class="next-month rounded-lg p-2 hover:bg-slate-100 text-slate-600"
                    aria-label="ماه بعد">
                    &#10094;
                  </button>
                </div>

                <div class="grid grid-cols-7 mb-2 text-center text-xs text-slate-400">
                  <div class="py-2">ش</div>
                  <div class="py-2">ی</div>
                  <div class="py-2">د</div>
                  <div class="py-2">س</div>
                  <div class="py-2">چ</div>
                  <div class="py-2">پ</div>
                  <div class="py-2">ج</div>
                </div>

                <div class="calendar-days grid grid-cols-7 gap-1"></div>

                <div class="flex justify-between items-center border-t border-slate-100 mt-4 pt-3">
                  <button type="button"
                    class="today-btn text-sm text-blue-600 hover:text-blue-800">
                    امروز
                  </button>

                  <button type="button"
                    class="clear-date text-sm text-slate-500 hover:text-red-600">
                    پاک کردن
                  </button>
                </div>
              </div>

              @error('due_date')
              <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
              @enderror
            </div>
            <select name="assigned_to" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white">
              <option value="{{ auth()->id() }}" @selected(old('assigned_to', auth()->id()) == auth()->id())>
                من ({{ auth()->user()->name }})
              </option>
              @foreach($project->members as $member)
              @if($member->id !== auth()->id())
              <option value="{{ $member->id }}" @selected(old('assigned_to')==$member->id)>
                {{ $member->name }}
              </option>
              @endif
              @endforeach
            </select>
            <button type="submit"
              class="w-full bg-blue-600 text-white text-sm font-medium rounded-xl px-5 py-2.5 hover:bg-blue-700 transition">
              افزودن وظیفه
            </button>
          </div>
        </form>
      </div>
      @endcan
    </div>
  </div>
  @endif

  {{-- ===================== MEMBERS ===================== --}}
  @if($tab === 'members')
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Members List --}}
    <div class="lg:col-span-2">
      <div class="bg-white rounded-2xl border border-slate-200 p-6">

        <div class="flex items-center justify-between mb-5">
          <h2 class="text-lg font-semibold text-slate-900">
            اعضای پروژه
          </h2>

          <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">
            {{ $project->members->unique('id')->count() + ($project->members->contains('id', $project->owner_id) ? 0 : 1) }} کاربر
          </span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-right text-sm whitespace-nowrap">
            <thead>
              <tr class="text-gray-500 border-b border-gray-100">
                <th class="pb-3 font-medium px-2">نام</th>
                <th class="pb-3 font-medium px-2">نام کاربری</th>
                <th class="pb-3 font-medium px-2">ایمیل</th>
                <th class="pb-3 font-medium px-2">نقش</th>
                <th class="pb-3 font-medium px-2">عضویت</th>
                <th class="pb-3 font-medium px-2 text-left">عملیات</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-50">

              {{-- Project Owner --}}
              <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-2">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full overflow-hidden bg-blue-600 text-white flex items-center justify-center text-xs font-semibold flex-shrink-0">
                      @if($project->owner?->avatar)
                      <img
                        src="{{ asset('storage/' . $project->owner->avatar) }}"
                        alt="{{ $project->owner->name }}"
                        class="w-full h-full object-cover">
                      @else
                      {{ mb_substr($project->owner->name ?? 'U', 0, 1) }}
                      @endif
                    </div>

                    <span class="font-medium text-slate-900">
                      {{ $project->owner->name ?? 'نامشخص' }}
                    </span>
                  </div>
                </td>

                <td class="py-3 px-2 text-gray-600">
                  {{ $project->owner->username ?? '—' }}
                </td>

                <td class="py-3 px-2 text-gray-600">
                  {{ $project->owner->email ?? '—' }}
                </td>

                <td class="py-3 px-2">
                  <span class="bg-gray-100 text-gray-700 px-2.5 py-1 rounded-lg text-xs font-medium">
                    مالک
                  </span>
                </td>

                <td class="py-3 px-2 text-gray-400">
                  {{ $project->created_at ? verta($project->created_at)->format('Y/m/d') : '—' }}
                </td>

                <td class="py-3 px-2 text-left text-xs text-gray-400">
                  مالک پروژه
                </td>
              </tr>

              {{-- Project Members --}}
              @foreach($project->members->where('id', '!=', $project->owner_id) as $member)
              @php
              $role = $member->pivot->role ?? 'member';

              $roleLabels = [
              'member' => 'عضو',
              'manager' => 'مدیر',
              ];
              @endphp

              <tr class="hover:bg-gray-50 transition">

                {{-- Name and Avatar --}}
                <td class="py-3 px-2">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full overflow-hidden bg-gray-50 border border-slate-200 text-gray-600 flex items-center justify-center font-medium text-xs flex-shrink-0">
                      @if($member->avatar)
                      <img
                        src="{{ asset('storage/' . $member->avatar) }}"
                        alt="{{ $member->name }}"
                        class="w-full h-full object-cover">
                      @else
                      {{ mb_substr($member->name ?? 'U', 0, 1) }}
                      @endif
                    </div>

                    <span class="text-slate-900">
                      {{ $member->name }}
                    </span>
                  </div>
                </td>

                {{-- Username --}}
                <td class="py-3 px-2 text-gray-600">
                  {{ $member->username ?? '—' }}
                </td>

                {{-- Email --}}
                <td class="py-3 px-2 text-gray-600">
                  {{ $member->email ?? '—' }}
                </td>

                {{-- Role --}}
                <td class="py-3 px-2">
                  @can('changeMemberRole', [$project, $member])
                  <form
                    action="{{ route('projects.members.change-role', [$project, $member]) }}"
                    method="POST"
                    class="inline-block">
                    @csrf
                    @method('PATCH')

                    <select
                      name="role"
                      onchange="this.form.submit()"
                      class="bg-white border border-slate-200 text-gray-600 px-2 py-1.5 rounded-lg text-xs font-medium focus:outline-none focus:ring-1 focus:ring-blue-600 cursor-pointer">
                      <option value="member" @selected($role === 'member')>عضو</option>
                      <option value="manager" @selected($role === 'manager')>مدیر</option>
                    </select>
                  </form>
                  @else
                  <span class="text-xs font-medium text-gray-500">
                    {{ $roleLabels[$role] ?? $role }}
                  </span>
                  @endcan
                </td>

                {{-- Joined At --}}
                <td class="py-3 px-2 text-gray-400">
                  {{ $member->pivot->created_at ? verta($member->pivot->created_at)->format('Y/m/d') : '—' }}
                </td>

                {{-- Actions --}}
                <td class="py-3 px-2 text-left">

                    @can('editMember', [$project, $member])
                        <a
                            href="{{ route('teams.edit', $member->username) }}"
                            class="text-sm text-blue-500 hover:text-blue-700 transition font-medium ml-3">
                            ویرایش
                        </a>
                    @else
                        <span class="text-xs text-gray-400">—</span>
                    @endcan

                    @can('removeMember', [$project, $member])
                        <form
                            action="{{ route('projects.members.destroy', [$project, $member]) }}"
                            method="POST"
                            onsubmit="return confirm('این عضو حذف شود؟')"
                            class="inline">
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="text-sm text-red-500 hover:text-red-700 transition font-medium">
                                حذف
                            </button>
                        </form>
                    @else
                        <span class="text-xs text-gray-400">—</span>
                    @endcan

                </td>
              </tr>
              @endforeach

            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Right Sidebar --}}
    <div class="space-y-6">

      {{-- Create User --}}
      @can('inviteMember', $project)
      <div class="bg-white rounded-2xl border border-slate-200 p-6">

        <div class="flex items-start justify-between gap-4 mb-4">
          <div>
            <h2 class="font-semibold text-slate-900">
              ایجاد کاربر جدید
            </h2>

            <p class="text-xs text-gray-400 mt-1 leading-5">
              برای کارمند جدید یک حساب کاربری بسازید.
            </p>
          </div>

          <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg"
              class="w-5 h-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="1.8">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M18 9v3m0 0v3m0-3h3m-3 0h-3M15 21a6 6 0 0 0-12 0m9-12a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" />
            </svg>
          </div>
        </div>

        <button
          type="button"
          onclick="toggleCreateUserForm()"
          id="createUserToggle"
          class="w-full px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-medium hover:bg-slate-800 transition">
          + ایجاد کاربر جدید
        </button>

        {{-- Create User Form --}}
        <div id="createUserForm" class="hidden mt-5 pt-5 border-t border-slate-100">
          <form
            action="{{ route('projects.members.create-user', $project) }}"
            method="POST"
            class="space-y-4">
            @csrf

            <div>
              <label class="block text-xs font-medium text-slate-700 mb-1.5">
                نام و نام خانوادگی
              </label>

              <input
                type="text"
                name="name"
                placeholder="مثلاً علی احمدی"
                value="{{ old('name') }}"
                required
                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:outline-none transition">
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-700 mb-1.5">
                نام کاربری
              </label>

              <input
                type="text"
                name="username"
                placeholder="مثلاً ali123"
                value="{{ old('username') }}"
                required
                autocomplete="off"
                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:outline-none transition">

              <p class="text-[11px] text-gray-400 mt-1.5">
                کارمند با این نام کاربری وارد سیستم می‌شود.
              </p>
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-700 mb-1.5">
                ایمیل
                <span class="text-gray-400 font-normal">(اختیاری)</span>
              </label>

              <input
                type="email"
                name="email"
                placeholder="example@company.com"
                value="{{ old('email') }}"
                autocomplete="off"
                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:outline-none transition">
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-700 mb-1.5">
                رمز عبور
              </label>

              <input
                type="password"
                name="password"
                placeholder="رمز عبور کاربر"
                required
                autocomplete="new-password"
                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:outline-none transition">
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-700 mb-1.5">
                تکرار رمز عبور
              </label>

              <input
                type="password"
                name="password_confirmation"
                placeholder="تکرار رمز عبور"
                required
                autocomplete="new-password"
                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:outline-none transition">
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-700 mb-1.5">
                نقش در پروژه
              </label>

              <select
                name="role"
                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:outline-none transition">
                <option value="member">عضو</option>
                <option value="manager">مدیر</option>
              </select>
            </div>

            <div class="flex gap-2 pt-1">
              <button
                type="submit"
                class="flex-1 px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
                ایجاد و اضافه کردن
              </button>

              <button
                type="button"
                onclick="toggleCreateUserForm()"
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-gray-600 text-sm hover:bg-gray-50 transition">
                انصراف
              </button>
            </div>
          </form>
        </div>
      </div>
      @endcan

      {{-- Invite Existing Member --}}
      @can('inviteMember', $project)
      <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-900 mb-1">
          دعوت عضو موجود
        </h2>

        <p class="text-xs text-gray-400 mb-4">
          اگر کاربر از قبل حساب دارد، می‌توانید با نام کاربری دعوتش کنید.
        </p>

        <form
          action="{{ route('projects.invitations.store', $project) }}"
          method="POST"
          class="flex flex-col gap-3">
          @csrf

          <input
            type="text"
            name="username"
            placeholder="نام کاربری را وارد کنید"
            value="{{ old('username') }}"
            required
            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">

          <button
            type="submit"
            class="w-full px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
            دعوت
          </button>
        </form>

        @error('username')
        <p class="mt-2 text-sm text-red-500">
          {{ $message }}
        </p>
        @enderror
      </div>
      @endcan

    </div>
  </div>

  {{-- Create User Toggle Script --}}
  <script>
    function toggleCreateUserForm() {
      const form = document.getElementById('createUserForm');
      const button = document.getElementById('createUserToggle');

      if (!form || !button) {
        return;
      }

      const isHidden = form.classList.contains('hidden');

      form.classList.toggle('hidden');

      button.textContent = isHidden ?
        '− بستن فرم ایجاد کاربر' :
        '+ ایجاد کاربر جدید';
    }
  </script>
  @endif


  {{-- ===================== ACTIVITY ===================== --}}
  @if($tab === 'activity')
  <div class="bg-white rounded-2xl border border-slate-200 p-6">
    <h2 class="text-lg font-semibold text-slate-900 mb-1">
      فعالیت‌ها
    </h2>

    <p class="text-sm text-gray-500 mb-6">
      اقدامات اخیر روی این پروژه
    </p>

    <div class="space-y-0">
      @forelse($activities ?? [] as $activity)
      <div class="flex gap-4 py-4 border-b border-gray-50 last:border-0">

        {{-- User Avatar --}}
        <div class="w-9 h-9 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center text-xs font-semibold text-gray-600 flex-shrink-0">
          @if($activity->user?->avatar)
          <img
            src="{{ asset('storage/' . $activity->user->avatar) }}"
            alt="{{ $activity->user->name }}"
            class="w-full h-full object-cover"
            loading="lazy">
          @else
          {{ mb_substr($activity->user?->name ?? 'S', 0, 1) }}
          @endif
        </div>

        {{-- Activity Details --}}
        <div class="flex-1 min-w-0">
          <p class="text-sm text-slate-900">
            <span class="font-medium">
              {{ $activity->user?->name ?? 'سیستم' }}
            </span>

            <span class="text-gray-600">
              {{ $activity->description }}
            </span>
          </p>

          <p class="text-xs text-gray-400 mt-1">
            {{ $activity->created_at->locale('fa')->diffForHumans() }}
          </p>
        </div>

      </div>
      @empty
      <div class="flex flex-col items-center justify-center py-16 text-center">
        <p class="text-sm font-medium text-slate-900">
          هنوز فعالیتی نیست
        </p>

        <p class="text-sm text-gray-500 mt-1">
          اقدامات این پروژه اینجا نمایش داده می‌شوند.
        </p>
      </div>
      @endforelse
    </div>
  </div>
  @endif

  {{-- ===================== SETTINGS ===================== --}}
  
@if($tab === 'settings')
  @php
    $statusOptionLabels = [
      'planning' => 'برنامه‌ریزی',
      'active'   => 'فعال',
      'completed'=> 'تکمیل‌شده',
      'archived' => 'آرشیو',
    ];
  @endphp

  @if (auth()->user()->can('edit', $project))
    <div class="max-w-2xl space-y-6">
      <!-- بخش عمومی -->
      <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-1">عمومی</h2>
        <p class="text-sm text-gray-500 mb-6">جزئیات پروژه را به‌روز کنید</p>

        <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-4">
          @csrf
          @method('PUT')

          <div>
            <label class="text-sm text-gray-600">نام</label>
            <input type="text" name="name" value="{{ old('name', $project->name) }}" required
              class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
          </div>

          <div>
            <label class="text-sm text-gray-600">توضیحات</label>
            <textarea name="description" rows="3"
              class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm resize-none focus:ring-2 focus:ring-blue-600 focus:outline-none">{{ old('description', $project->description) }}</textarea>
          </div>

          <div>
            <label class="text-sm text-gray-600">وضعیت</label>
            <select name="status" class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white">
              @foreach(['planning', 'active', 'completed', 'archived'] as $s)
                <option value="{{ $s }}" @selected(old('status', $project->status) === $s)>
                  {{ $statusOptionLabels[$s] }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="grid grid-cols-2 gap-3">
            {{-- تاریخ شروع --}}
            <div class="relative" dir="rtl" data-jalali-picker>
              <label class="block text-sm text-gray-600 mb-1">تاریخ شروع</label>

              <input
                type="text"
                name="start_date_jalali"
                class="jalali-input w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 cursor-pointer"
                placeholder="انتخاب تاریخ شروع"
                readonly
                autocomplete="off"
                value="{{ old('start_date_jalali', $project->start_date ? verta($project->start_date)->format('Y/m/d') : '') }}">

              <input
                type="hidden"
                name="start_date"
                class="gregorian-value"
                value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}">

              <div class="jalali-calendar hidden absolute z-50 top-full mt-2 right-0 w-80 max-w-[90vw] rounded-2xl border border-gray-200 bg-white p-4 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                  <button type="button" class="prev-month rounded-lg p-2 hover:bg-gray-100 text-gray-600">&#10095;</button>
                  <div class="text-center">
                    <div class="calendar-title font-bold text-gray-800"></div>
                    <div class="text-xs text-gray-400 mt-1">تقویم شمسی</div>
                  </div>
                  <button type="button" class="next-month rounded-lg p-2 hover:bg-gray-100 text-gray-600">&#10094;</button>
                </div>

                <div class="grid grid-cols-7 mb-2 text-center text-xs text-gray-400">
                  <div class="py-2">ش</div>
                  <div class="py-2">ی</div>
                  <div class="py-2">د</div>
                  <div class="py-2">س</div>
                  <div class="py-2">چ</div>
                  <div class="py-2">پ</div>
                  <div class="py-2">ج</div>
                </div>

                <div class="calendar-days grid grid-cols-7 gap-1"></div>

                <div class="flex justify-between items-center border-t mt-4 pt-3">
                  <button type="button" class="today-btn text-sm text-blue-600 hover:text-blue-800">امروز</button>
                  <button type="button" class="clear-date text-sm text-gray-500 hover:text-red-600">پاک کردن</button>
                </div>
              </div>

              @error('start_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- تاریخ پایان --}}
            <div class="relative" dir="rtl" data-jalali-picker>
              <label class="block text-sm text-gray-600 mb-1">تاریخ پایان</label>

              <input
                type="text"
                name="end_date_jalali"
                class="jalali-input w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 cursor-pointer"
                placeholder="انتخاب تاریخ پایان"
                readonly
                autocomplete="off"
                value="{{ old('end_date_jalali', $project->end_date ? verta($project->end_date)->format('Y/m/d') : '') }}">

              <input
                type="hidden"
                name="end_date"
                class="gregorian-value"
                value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}">

              <div class="jalali-calendar hidden absolute z-50 top-full mt-2 right-0 w-80 max-w-[90vw] rounded-2xl border border-gray-200 bg-white p-4 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                  <button type="button" class="prev-month rounded-lg p-2 hover:bg-gray-100 text-gray-600">&#10095;</button>
                  <div class="text-center">
                    <div class="calendar-title font-bold text-gray-800"></div>
                    <div class="text-xs text-gray-400 mt-1">تقویم شمسی</div>
                  </div>
                  <button type="button" class="next-month rounded-lg p-2 hover:bg-gray-100 text-gray-600">&#10094;</button>
                </div>

                <div class="grid grid-cols-7 mb-2 text-center text-xs text-gray-400">
                  <div class="py-2">ش</div>
                  <div class="py-2">ی</div>
                  <div class="py-2">د</div>
                  <div class="py-2">س</div>
                  <div class="py-2">چ</div>
                  <div class="py-2">پ</div>
                  <div class="py-2">ج</div>
                </div>

                <div class="calendar-days grid grid-cols-7 gap-1"></div>

                <div class="flex justify-between items-center border-t mt-4 pt-3">
                  <button type="button" class="today-btn text-sm text-blue-600 hover:text-blue-800">امروز</button>
                  <button type="button" class="clear-date text-sm text-gray-500 hover:text-red-600">پاک کردن</button>
                </div>
              </div>

              @error('end_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
          </div>

          <button type="submit"
            class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
            ذخیره تغییرات
          </button>
        </form>
      </div>

      <!-- منطقه خطر -->
      @can('delete', $project)
      <div class="bg-white rounded-2xl border border-red-200 p-6">
        <h2 class="text-lg font-semibold text-red-600 mb-1">
          منطقه خطر
        </h2>

        <p class="text-sm text-gray-500 mb-4">
          این پروژه و همه وظایف آن برای همیشه حذف می‌شوند.
        </p>

        <form method="POST"
          action="{{ route('projects.destroy', $project) }}"
          onsubmit="return confirm('این پروژه برای همیشه حذف شود؟')">
          @csrf
          @method('DELETE')

          <button type="submit"
            class="px-5 py-2.5 border border-red-200 text-red-600 text-sm font-medium rounded-xl hover:bg-red-50 transition">
            حذف پروژه
          </button>
        </form>
      </div>
      @endcan
    </div>
  @else
    <!-- حالت قفل برای کاربرانی که دسترسی ندارند -->
    <div class="max-w-2xl">
      <div class="bg-white rounded-2xl border border-amber-200 p-8 text-center">
        <div class="mx-auto w-16 h-16 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center text-4xl mb-6">🔒</div>
        <h3 class="text-xl font-semibold text-slate-900 mb-2">دسترسی محدود</h3>
        <p class="text-gray-500 max-w-sm mx-auto leading-relaxed">
          متأسفانه شما مجوز ویرایش یا حذف این پروژه را ندارید.
        </p>
        <div class="mt-8 text-xs text-gray-400">لطفاً با ادمین پروژه یا صاحب آن تماس بگیرید</div>
      </div>
    </div>
  @endif
@endif

  {{-- ===================== EDIT TASK MODAL ===================== --}}
  <div id="editTaskModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-blue-600/50 transition-opacity duration-300 opacity-0">
    <div id="modalContainer"
      class="bg-white rounded-2xl w-full max-w-lg p-6 mx-4 max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0">

      <div id="editModalMainContent">
        <div class="flex items-center justify-between mb-5">
          <h2 class="text-lg font-semibold text-slate-900">ویرایش وظیفه</h2>
          <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-blue-600 text-xl">✕</button>
        </div>

        @if ($errors->any() && old('_task_id'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4">
          <ul class="list-disc list-inside text-sm text-red-600">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <form
          action="{{ $errors->any() && old('_task_id') ? route('projects.tasks.update', [$project, old('_task_id')]) : '' }}"
          id="editTaskForm"
          method="POST">
          @csrf
          @method('PUT')
          <input type="hidden" name="_task_id" id="editTaskId" value="{{ old('_task_id') }}">

          <div class="space-y-4">
            <div>
              <label class="text-sm text-gray-600">عنوان</label>
              <input id="editTitle" name="title" type="text" value="{{ old('title') }}"
                class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
              <label class="text-sm text-gray-600">توضیحات</label>
              <textarea id="editDescription" name="description" rows="3"
                class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm resize-none focus:ring-2 focus:ring-blue-600 focus:outline-none">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="text-sm text-gray-600">اولویت</label>
                <select id="editPriority" name="priority" class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white">
                  <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>پایین</option>
                  <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>متوسط</option>
                  <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>بالا</option>
                </select>
              </div>
              <div>
                <label class="text-sm text-gray-600">وضعیت</label>
                <select id="editStatus" name="status" class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white">
                  <option value="todo" {{ old('status') === 'todo' ? 'selected' : '' }}>انجام نشده</option>
                  <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>در حال انجام</option>
                  <option value="review" {{ old('status') === 'review' ? 'selected' : '' }}>بازبینی</option>
                  <option value="done" {{ old('status') === 'done' ? 'selected' : '' }}>انجام‌شده</option>
                </select>
              </div>
            </div>

            <div class="relative" dir="rtl" data-jalali-picker>
              <label for="editJalaliDate" class="block text-sm text-gray-600 mb-1">تاریخ سررسید</label>

              <div class="relative">
                <input
                  id="editJalaliDate"
                  type="text"
                  name="due_date"
                  readonly
                  autocomplete="off"
                  placeholder="انتخاب تاریخ سررسید"
                  value="{{ old('due_date') }}"
                  class="jalali-input w-full px-4 py-2.5 pl-10 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition cursor-pointer bg-white" />

                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                  fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                </svg>
              </div>

              <input type="hidden" id="editStartDateGregorian" name="due_date_gregorian" class="gregorian-value" value="{{ old('due_date') }}" />

              <div class="jalali-calendar hidden absolute z-50 top-full mt-2 right-0 w-80 max-w-full rounded-2xl border border-slate-200 bg-white p-4 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                  <button type="button" class="prev-month rounded-lg p-2 hover:bg-slate-100 text-slate-600">&#10095;</button>
                  <div class="text-center">
                    <div class="calendar-title font-bold text-slate-800"></div>
                    <div class="text-xs text-slate-400 mt-1">تقویم شمسی</div>
                  </div>
                  <button type="button" class="next-month rounded-lg p-2 hover:bg-slate-100 text-slate-600">&#10094;</button>
                </div>

                <div class="grid grid-cols-7 mb-2 text-center text-xs text-slate-400">
                  <div class="py-2">ش</div>
                  <div class="py-2">ی</div>
                  <div class="py-2">د</div>
                  <div class="py-2">س</div>
                  <div class="py-2">چ</div>
                  <div class="py-2">پ</div>
                  <div class="py-2">ج</div>
                </div>

                <div class="calendar-days grid grid-cols-7 gap-1"></div>

                <div class="flex justify-between items-center border-t border-slate-100 mt-4 pt-3">
                  <button type="button" class="today-btn text-sm text-blue-600 hover:text-blue-800">امروز</button>
                  <button type="button" class="clear-date text-sm text-slate-500 hover:text-red-600">پاک کردن</button>
                </div>
              </div>

              @error('due_date')
              <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label class="text-sm text-gray-600">مسئول</label>
              <select id="editAssigned" name="assigned_to"
                class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white">
                @foreach($project->members as $member)
                <option value="{{ $member->id }}" @selected(old('assigned_to')==$member->id)>
                  {{ $member->name }}
                </option>
                @endforeach
              </select>
            </div>

            <div class="flex items-center justify-between pt-4">
              {{-- دکمه حذف با جاوااسکریپت کنترل می‌شه --}}
              <button type="button" id="deleteTaskBtn" onclick="showDeleteConfirmation()"
                class="text-sm text-red-600 hover:text-red-700 font-medium transition hidden">
                حذف
              </button>
              <div id="deletePlaceholder"></div>

              <div class="flex gap-3">
                <button type="button" onclick="closeEditModal()"
                  class="px-4 py-2 rounded-xl border border-slate-200 text-sm hover:bg-gray-50 transition">
                  انصراف
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 text-white text-sm hover:bg-blue-700 transition">
                  ذخیره تغییرات
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>

      <div id="deleteConfirmContent" class="hidden text-center py-4">
        <div class="w-12 h-12 rounded-2xl bg-red-50 border border-red-100 text-red-600 flex items-center justify-center mx-auto mb-3 text-lg font-bold">!</div>
        <h3 class="text-base font-semibold text-slate-900 mb-1">این وظیفه حذف شود؟</h3>
        <p class="text-sm text-gray-500 mb-6">این عمل قابل بازگشت نیست.</p>
        <div class="flex justify-center gap-3">
          <button type="button" onclick="hideDeleteConfirmation()"
            class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
            انصراف
          </button>
          <button type="button" onclick="executeDelete()"
            class="px-5 py-2.5 rounded-xl bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition">
            حذف وظیفه
          </button>
        </div>
      </div>

      <form id="deleteTaskForm" method="POST" class="hidden"
        action="{{ $errors->any() && old('_task_id') ? route('projects.tasks.destroy', [$project, old('_task_id')]) : '' }}">
        @csrf
        @method('DELETE')
      </form>
    </div>
  </div>

  <script>
    (() => {
      const monthNames = [
        'فروردین', 'اردیبهشت', 'خرداد', 'تیر',
        'مرداد', 'شهریور', 'مهر', 'آبان',
        'آذر', 'دی', 'بهمن', 'اسفند'
      ];

      const pad = n => String(n).padStart(2, '0');

      function toJalali(gy, gm, gd) {
        const gdm = [0, 31, ((gy % 4 === 0 && gy % 100 !== 0) || gy % 400 === 0) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        let jy = gy <= 1600 ? 0 : 979;
        gy -= gy <= 1600 ? 621 : 1600;
        const gy2 = gm > 2 ? gy + 1 : gy;
        let days = 365 * gy + Math.floor((gy2 + 3) / 4) - Math.floor((gy2 + 99) / 100) + Math.floor((gy2 + 399) / 400) - 80 + gd;
        for (let i = 0; i < gm; i++) days += gdm[i];
        jy += 33 * Math.floor(days / 12053);
        days %= 12053;
        jy += 4 * Math.floor(days / 1461);
        days %= 1461;
        if (days > 365) {
          jy += Math.floor((days - 1) / 365);
          days = (days - 1) % 365;
        }
        const jm = days < 186 ? 1 + Math.floor(days / 31) : 7 + Math.floor((days - 186) / 30);
        const jd = 1 + (days < 186 ? days % 31 : (days - 186) % 30);
        return [jy, jm, jd];
      }

      function toGregorian(jy, jm, jd) {
        jy += 1595;
        let days = -355668 + 365 * jy + Math.floor(jy / 33) * 8 + Math.floor(((jy % 33) + 3) / 4) + jd;
        if (jm < 7) days += (jm - 1) * 31;
        else days += (jm - 7) * 30 + 186;
        let gy = 400 * Math.floor(days / 146097);
        days %= 146097;
        if (days > 36524) {
          gy += 100 * Math.floor(--days / 36524);
          days %= 36524;
          if (days >= 365) days++;
        }
        gy += 4 * Math.floor(days / 1461);
        days %= 1461;
        if (days > 365) {
          gy += Math.floor((days - 1) / 365);
          days = (days - 1) % 365;
        }
        let gd = days + 1;
        const leap = gy % 4 === 0 && (gy % 100 !== 0 || gy % 400 === 0);
        const monthDays = [0, 31, leap ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        let gm = 1;
        while (gm <= 12 && gd > monthDays[gm]) {
          gd -= monthDays[gm];
          gm++;
        }
        return [gy, gm, gd];
      }

      function monthLength(y, m) {
        if (m <= 6) return 31;
        if (m <= 11) return 30;
        const next = toGregorian(y + 1, 1, 1);
        const last = toJalali(next[0], next[1], next[2] - 1);
        return last[2] === 30 ? 30 : 29;
      }

      const today = new Date();
      const currentJalali = toJalali(today.getFullYear(), today.getMonth() + 1, today.getDate());

      function parseJalali(str) {
        const p = (str || '').split('/').map(Number);
        return p.length === 3 && p.every(n => n > 0) ? p : null;
      }

      function initPicker(root) {
        if (root._jalali) return root._jalali;

        const input = root.querySelector('.jalali-input');
        const hiddenInput = root.querySelector('.gregorian-value');
        const calendar = root.querySelector('.jalali-calendar');
        const title = root.querySelector('.calendar-title');
        const daysBox = root.querySelector('.calendar-days');
        const prevBtn = root.querySelector('.prev-month');
        const nextBtn = root.querySelector('.next-month');
        const todayBtn = root.querySelector('.today-btn');
        const clearBtn = root.querySelector('.clear-date');

        if (!input || !hiddenInput || !calendar || !title || !daysBox) return null;

        let year = currentJalali[0];
        let month = currentJalali[1];
        let selected = null;

        function notifyChange() {
          input.dispatchEvent(new Event('change', {
            bubbles: true
          }));
        }

        function render() {
          title.textContent = `${monthNames[month - 1]} ${year}`;
          daysBox.innerHTML = '';

          const firstDay = toGregorian(year, month, 1);
          const weekday = new Date(firstDay[0], firstDay[1] - 1, firstDay[2]).getDay();
          const startOffset = (weekday + 1) % 7;
          const count = monthLength(year, month);

          for (let i = 0; i < startOffset; i++) {
            daysBox.appendChild(document.createElement('div'));
          }

          for (let day = 1; day <= count; day++) {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = day;
            button.className = 'h-9 rounded-lg text-sm transition';

            const isSelected = selected && selected[0] === year && selected[1] === month && selected[2] === day;
            const isToday = currentJalali[0] === year && currentJalali[1] === month && currentJalali[2] === day;

            if (isSelected) {
              button.classList.add('bg-blue-600', 'text-white', 'font-bold');
            } else {
              button.classList.add('text-slate-700', 'hover:bg-blue-50');
            }
            if (isToday && !isSelected) {
              button.classList.add('ring-1', 'ring-blue-500', 'text-blue-600');
            }

            button.addEventListener('click', () => setDate(year, month, day));
            daysBox.appendChild(button);
          }
        }

        function setDate(y, m, d) {
          selected = [y, m, d];
          year = y;
          month = m;

          const g = toGregorian(y, m, d);
          input.value = `${y}/${pad(m)}/${pad(d)}`;
          hiddenInput.value = `${g[0]}-${pad(g[1])}-${pad(g[2])}`;

          calendar.classList.add('hidden');
          notifyChange();
          render();
        }

        function clearDate() {
          input.value = '';
          hiddenInput.value = '';
          selected = null;
          year = currentJalali[0];
          month = currentJalali[1];

          calendar.classList.add('hidden');
          notifyChange();
          render();
        }

        function moveMonth(delta) {
          month += delta;
          if (month > 12) {
            month = 1;
            year++;
          } else if (month < 1) {
            month = 12;
            year--;
          }
          render();
        }

        // مقدار فعلی input را می‌خواند و تقویم را روی همان تاریخ می‌گذارد
        function sync() {
          const p = parseJalali(input.value);
          if (p) {
            selected = p;
            year = p[0];
            month = p[1];
          } else {
            selected = null;
            year = currentJalali[0];
            month = currentJalali[1];
          }
          render();
        }

        prevBtn?.addEventListener('click', () => moveMonth(-1));
        nextBtn?.addEventListener('click', () => moveMonth(1));
        todayBtn?.addEventListener('click', () => setDate(...currentJalali));
        clearBtn?.addEventListener('click', clearDate);

        input.addEventListener('click', (e) => {
          e.stopPropagation();
          document.querySelectorAll('.jalali-calendar').forEach(item => {
            if (item !== calendar) item.classList.add('hidden');
          });
          calendar.classList.toggle('hidden');
        });

        root._jalali = {
          sync,
          setDate,
          clearDate
        };
        sync();
        return root._jalali;
      }

      // راه‌اندازی همه تقویم‌ها (فرم ایجاد و مودال ویرایش)
      const pickers = document.querySelectorAll('[data-jalali-picker]');
      pickers.forEach(initPicker);

      document.addEventListener('click', (event) => {
        pickers.forEach(root => {
          if (!root.contains(event.target)) {
            root.querySelector('.jalali-calendar')?.classList.add('hidden');
          }
        });
      });

      // اعتبارسنجی: اگر تاریخ نمایش داده شده ولی مقدار میلادی خالی است، ارسال نشود
      document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', (event) => {
          const formPickers = form.querySelectorAll('[data-jalali-picker]');
          for (const root of formPickers) {
            const input = root.querySelector('.jalali-input');
            const hiddenInput = root.querySelector('.gregorian-value');
            if (input && hiddenInput && input.value && !hiddenInput.value) {
              event.preventDefault();
              input.focus();
              alert('لطفاً تاریخ را از تقویم انتخاب کنید.');
              return;
            }
          }
        });
      });

      // ===== مودال ویرایش =====
      const modal = document.getElementById('editTaskModal');
      const container = document.getElementById('modalContainer');
      const editRoot = document.getElementById('editPickerRoot');

      window.openEditModal = function(button) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        setTimeout(() => {
          modal.classList.remove('opacity-0');
          container.classList.remove('scale-95', 'opacity-0');
          container.classList.add('scale-100', 'opacity-100');
        }, 10);

        hideDeleteConfirmation();

        document.getElementById('editTitle').value = button.dataset.title || '';
        document.getElementById('editDescription').value = button.dataset.description || '';
        document.getElementById('editPriority').value = button.dataset.priority || 'medium';
        document.getElementById('editStatus').value = button.dataset.status || 'todo';
        document.getElementById('editAssigned').value = button.dataset.assigned || '';

        const taskId = button.dataset.id;
        document.getElementById('editTaskId').value = taskId;

        // مقدار تاریخ را در فیلدها می‌گذاریم و سپس تقویم را همگام می‌کنیم
        document.getElementById('editJalaliDate').value = button.dataset.date || '';
        document.getElementById('editStartDateGregorian').value = button.dataset.gregorian || '';

        if (editRoot) {
          const picker = initPicker(editRoot);
          picker?.sync();
        }

        const baseUrl = `/projects/${'{{ $project->slug ?? $project->id }}'}/tasks/${taskId}`;
        document.getElementById('editTaskForm').action = baseUrl;
        document.getElementById('deleteTaskForm').action = baseUrl;

        const deleteBtn = document.getElementById('deleteTaskBtn');
        const placeholder = document.getElementById('deletePlaceholder');
        if (button.dataset.canDelete === 'true') {
          deleteBtn.classList.remove('hidden');
          placeholder.classList.add('hidden');
        } else {
          deleteBtn.classList.add('hidden');
          placeholder.classList.remove('hidden');
        }
      };

      window.closeEditModal = function() {
        modal.classList.add('opacity-0');
        container.classList.remove('scale-100', 'opacity-100');
        container.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
          modal.classList.remove('flex');
          modal.classList.add('hidden');
        }, 300);
      };

      window.showDeleteConfirmation = function() {
        document.getElementById('editModalMainContent').classList.add('hidden');
        document.getElementById('deleteConfirmContent').classList.remove('hidden');
      };

      window.hideDeleteConfirmation = function() {
        document.getElementById('deleteConfirmContent').classList.add('hidden');
        document.getElementById('editModalMainContent').classList.remove('hidden');
      };

      window.executeDelete = function() {
        document.getElementById('deleteTaskForm').submit();
      };

      @if($errors -> any() && old('_task_id'))
      if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
          modal.classList.remove('opacity-0');
          container.classList.remove('scale-95', 'opacity-0');
          container.classList.add('scale-100', 'opacity-100');
        }, 10);
      }
      @endif

      // ===== درگ و دراپ وظایف =====
      const taskCards = document.querySelectorAll('.task-card');
      const taskColumns = document.querySelectorAll('.task-column');
      let draggedTask = null;

      taskCards.forEach(card => {
        card.addEventListener('dragstart', () => {
          draggedTask = card;
          setTimeout(() => card.classList.add('opacity-50'), 0);
        });
        card.addEventListener('dragend', () => {
          card.classList.remove('opacity-50');
          draggedTask = null;
        });
      });

      taskColumns.forEach(column => {
        column.addEventListener('dragover', (e) => {
          e.preventDefault();
          column.classList.add('bg-gray-100');
        });
        column.addEventListener('dragleave', () => column.classList.remove('bg-gray-100'));
        column.addEventListener('drop', async (e) => {
          e.preventDefault();
          column.classList.remove('bg-gray-100');
          if (!draggedTask) return;
          const taskId = draggedTask.dataset.taskId;
          const newStatus = column.dataset.status;
          column.appendChild(draggedTask);
          const updated = await updateTaskStatus(taskId, newStatus);
          if (updated) location.reload();
        });
      });

      async function updateTaskStatus(taskId, status) {
        try {
          const response = await fetch(`/tasks/${taskId}/status`, {
            method: 'PATCH',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
              status
            })
          });
          if (!response.ok) throw new Error('Update failed');
          return true;
        } catch (error) {
          console.error(error);
          return false;
        }
      }
      })();





    
  </script>


</x-dashboard-layout>