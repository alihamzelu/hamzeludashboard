<head>
    <title>مدیریت پروژه | تحلیل ها</title>
</head>
<x-dashboard-layout>
  <div class="mb-6 sm:mb-8">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">تحلیل‌ها</h1>
    <p class="text-gray-500 text-sm mt-1">نمای کلی پروژه‌ها، وظایف و عملکرد تیم</p>
  </div>

  <div class="flex flex-wrap items-center gap-2 mb-6">
    @foreach([
      '7' => '۷ روز اخیر',
      '30' => '۳۰ روز اخیر',
      'quarter' => 'این فصل',
      'all' => 'همه زمان‌ها',
    ] as $key => $label)
      <a href="{{ route('analytics.index', ['range' => $key]) }}"
        class="px-4 py-2 rounded-xl text-sm font-medium transition
          {{ $range === $key
            ? 'bg-black text-white'
            : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
        {{ $label }}
      </a>
    @endforeach
  </div>

  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5 mb-6 sm:mb-8">
    <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5">
      <p class="text-xs sm:text-sm text-gray-500">وظایف تکمیل‌شده</p>
      <p class="text-xl sm:text-2xl font-bold mt-1">{{ $stats['tasks_completed'] }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5">
      <p class="text-xs sm:text-sm text-gray-500">میانگین زمان تکمیل</p>
      <p class="text-xl sm:text-2xl font-bold mt-1">{{ $stats['avg_completion'] }} روز</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5">
      <p class="text-xs sm:text-sm text-gray-500">تحویل به‌موقع</p>
      <p class="text-xl sm:text-2xl font-bold mt-1">{{ $stats['on_time_rate'] }}٪</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5">
      <p class="text-xs sm:text-sm text-gray-500">پروژه‌های فعال</p>
      <p class="text-xl sm:text-2xl font-bold mt-1">{{ $stats['active_projects'] }}</p>
      <p class="text-xs text-gray-500 mt-1">{{ $stats['near_deadline'] }} نزدیک موعد</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6">
      <div class="mb-6">
        <h2 class="font-semibold text-gray-900">وظایف تکمیل‌شده</h2>
        <p class="text-xs text-gray-500 mt-0.5">تکمیل روزانه (۷ روز اخیر)</p>
      </div>
      <div class="flex items-end gap-3 h-48">
        @foreach($chart as $day)
          @php $h = (int) round(($day['count'] / $chartMax) * 100); @endphp
          <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
            <span class="text-[10px] text-gray-400">{{ $day['count'] }}</span>
            <div class="w-full max-w-[40px] bg-black/90 rounded-t-lg min-h-[4px]"
              style="height: {{ max($h, $day['count'] > 0 ? 8 : 4) }}%"></div>
            <span class="text-[11px] text-gray-400">{{ $day['label'] }}</span>
          </div>
        @endforeach
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6">
      <h2 class="font-semibold text-gray-900 mb-1">وضعیت وظایف</h2>
      <p class="text-xs text-gray-500 mb-6">توزیع در همه پروژه‌ها</p>
      <div class="space-y-4">
        @php
          $statusFa = [
            'To Do' => 'انجام نشده',
            'In Progress' => 'در حال انجام',
            'Review' => 'بازبینی',
            'Done' => 'انجام‌شده',
            'Todo' => 'انجام نشده',
          ];
        @endphp
        @foreach($statusBreakdown as $row)
          <div>
            <div class="flex justify-between text-sm mb-1.5">
              <span class="text-gray-700">{{ $statusFa[$row['label']] ?? $row['label'] }}</span>
              <span class="text-gray-500">{{ $row['count'] }} · {{ $row['pct'] }}٪</span>
            </div>
            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
              <div class="h-full {{ $row['color'] }} rounded-full" style="width: {{ $row['pct'] }}%"></div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
      <h2 class="font-semibold text-gray-900 mb-1">بر اساس اولویت</h2>
      <p class="text-xs text-gray-500 mb-6">وظایف باز، گروه‌بندی‌شده با اولویت</p>
      <div class="grid grid-cols-3 gap-4">
        <div class="rounded-2xl bg-red-50 border border-red-100 p-4 text-center">
          <p class="text-2xl font-bold text-red-700">{{ $priorities['high'] }}</p>
          <p class="text-xs text-red-600 mt-1 font-medium">بالا</p>
        </div>
        <div class="rounded-2xl bg-orange-50 border border-orange-100 p-4 text-center">
          <p class="text-2xl font-bold text-orange-700">{{ $priorities['medium'] }}</p>
          <p class="text-xs text-orange-600 mt-1 font-medium">متوسط</p>
        </div>
        <div class="rounded-2xl bg-green-50 border border-green-100 p-4 text-center">
          <p class="text-2xl font-bold text-green-700">{{ $priorities['low'] }}</p>
          <p class="text-xs text-green-600 mt-1 font-medium">پایین</p>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6">
      <h2 class="font-semibold text-gray-900 mb-1">برترین پروژه‌ها</h2>
      <p class="text-xs text-gray-500 mb-6">بیشترین وظایف تکمیل‌شده</p>
      <div class="space-y-4">
        @forelse($topProjects as $project)
          <div>
            <div class="flex justify-between text-sm mb-1">
              <a href="{{ route('projects.show', $project) }}" class="font-medium text-gray-900 truncate hover:underline">
                {{ $project->name }}
              </a>
              <span class="text-gray-500 flex-shrink-0">{{ $project->done_tasks_count }} انجام‌شده</span>
            </div>
            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
              <div class="h-full bg-black rounded-full" style="width: {{ $project->progress }}%"></div>
            </div>
          </div>
        @empty
          <p class="text-sm text-gray-400">هنوز پروژه‌ای نیست.</p>
        @endforelse
      </div>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-gray-200 p-6">
    <div class="mb-5">
      <h2 class="font-semibold text-gray-900">بهره‌وری تیم</h2>
      <p class="text-xs text-gray-500 mt-0.5">وظایف بر اساس مسئول</p>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-right text-gray-500 border-b border-gray-100">
            <th class="pb-3 font-medium">عضو</th>
            <th class="pb-3 font-medium">تکمیل‌شده</th>
            <th class="pb-3 font-medium">باز</th>
            <th class="pb-3 font-medium">عقب‌افتاده</th>
            <th class="pb-3 font-medium">نرخ به‌موقع</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @forelse($teamStats as $row)
            <tr>
              <td class="py-3.5">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-semibold">
                    {{ strtoupper(substr($row['user']->name, 0, 1)) }}
                  </div>
                  <span class="font-medium text-gray-900">{{ $row['user']->name }}</span>
                </div>
              </td>
              <td class="py-3.5 text-gray-700">{{ $row['completed'] }}</td>
              <td class="py-3.5 text-gray-700">{{ $row['in_progress'] }}</td>
              <td class="py-3.5 {{ $row['overdue'] > 0 ? 'text-red-600' : 'text-gray-500' }}">
                {{ $row['overdue'] }}
              </td>
              <td class="py-3.5 font-medium text-gray-900">
                {{ $row['on_time'] !== null ? $row['on_time'].'٪' : '—' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-8 text-center text-gray-400">هنوز داده تیمی نیست.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</x-dashboard-layout>