<head>
    <title> تقویم | مدیریت پروژه</title>
</head>
<x-dashboard-layout>
  @php
    $faMonths = [1=>'ژانویه',2=>'فوریه',3=>'مارس',4=>'آوریل',5=>'مه',6=>'ژوئن',7=>'ژوئیه',8=>'اوت',9=>'سپتامبر',10=>'اکتبر',11=>'نوامبر',12=>'دسامبر'];
  @endphp

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
      <h1 class="text-xl sm:text-2xl font-bold text-slate-900">تقویم</h1>
      <p class="text-slate-500 text-sm mt-1">موعد وظایف و رویدادهای شما</p>
    </div>

    <button type="button" onclick="openEventModal()"
      class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
      </svg>
      افزودن رویداد
    </button>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-slate-900">{{ $faMonths[$currentDate->month] ?? $currentDate->format('F') }} {{ $currentDate->year }}</h2>

        <div class="flex items-center gap-2">
          <a href="{{ route('calendar.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
            class="p-2 rounded-xl hover:bg-slate-100 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </a>

          <a href="{{ route('calendar.index') }}"
            class="px-3 py-1.5 text-sm font-medium rounded-lg hover:bg-blue-50 hover:text-blue-700 transition">
            امروز
          </a>

          <a href="{{ route('calendar.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
            class="p-2 rounded-xl hover:bg-slate-100 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
          </a>
        </div>
      </div>

      <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-slate-400 mb-2">
        @foreach(['یکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه'] as $label)
        <div class="py-2">{{ $label }}</div>
        @endforeach
      </div>

      <div class="grid grid-cols-7 gap-1 text-sm">
        @foreach($calendarDays as $day)
        <div class="min-h-[72px] sm:min-h-[88px] rounded-xl p-1.5 flex flex-col
            {{ $day['is_current_month'] ? 'bg-white' : 'bg-slate-50' }}
            {{ $day['is_today'] ? 'ring-2 ring-blue-600/20' : '' }}">

          <span @class([
            'w-7 h-7 flex items-center justify-center text-xs rounded-full mb-1',
            'bg-blue-600 text-white font-semibold' => $day['is_today'],
            'text-slate-300' => !$day['is_current_month'] && !$day['is_today'],
            'text-slate-700' => $day['is_current_month'] && !$day['is_today'],
          ])>
            {{ $day['day_number'] }}
          </span>

          @if($day['items']->isNotEmpty())
          <div class="flex flex-wrap gap-0.5 mt-auto">
            @foreach($day['items']->take(3) as $item)
            <span class="w-1.5 h-1.5 rounded-full {{ $item['color'] }}" title="{{ $item['title'] }}"></span>
            @endforeach
            @if($day['items']->count() > 3)
            <span class="text-[9px] text-slate-400 leading-none">+{{ $day['items']->count() - 3 }}</span>
            @endif
          </div>
          @endif
        </div>
        @endforeach
      </div>

      <div class="flex flex-wrap items-center gap-4 mt-5 pt-4 border-t border-slate-100 text-xs text-slate-500">
        <span class="inline-flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span> وظیفه
        </span>
        <span class="inline-flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-blue-500"></span> رویداد
        </span>
        <span class="inline-flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-slate-400"></span> وظیفه انجام‌شده
        </span>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
      <h2 class="font-semibold text-slate-900 mb-5">پیش‌رو</h2>

      <div class="space-y-2">
        @forelse($upcomingItems as $item)
        <div class="flex gap-3 p-3 rounded-xl hover:bg-slate-50 transition group">
          <div class="w-1 rounded-full {{ $item['color'] }} flex-shrink-0"></div>

          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
              @if($item['type'] === 'task' && !empty($item['url']))
              <a href="{{ $item['url'] }}" class="text-sm font-medium text-slate-900 truncate hover:text-blue-600 transition">
                {{ $item['title'] }}
              </a>
              @else
              <p class="text-sm font-medium text-slate-900 truncate">{{ $item['title'] }}</p>
              @endif

              <span @class([
                'text-[10px] px-1.5 py-0.5 rounded font-medium flex-shrink-0',
                'bg-emerald-50 text-emerald-700' => $item['type'] === 'task',
                'bg-blue-50 text-blue-700' => $item['type'] === 'event',
              ])>
                {{ $item['type'] === 'task' ? 'وظیفه' : 'رویداد' }}
              </span>
            </div>

            <p class="text-xs text-slate-500 mt-0.5">
              @if($item['date']->isToday()) امروز
              @elseif($item['date']->isTomorrow()) فردا
              @else {{ $item['date']->format('Y/m/d') }}
              @endif

              @if($item['type'] === 'event')
              · {{ $item['date']->format('H:i') }}
              @endif
            </p>

            @if(!empty($item['sub_title']))
            <p class="text-xs text-slate-400 mt-0.5 truncate">{{ $item['sub_title'] }}</p>
            @endif
          </div>

          @if($item['type'] === 'event')
          <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition flex-shrink-0">
            <button type="button"
              onclick="openEditEventModal(this)"
              data-id="{{ $item['id'] }}"
              data-title="{{ e($item['title']) }}"
              data-project="{{ e($item['sub_title'] ?? '') }}"
              data-starts="{{ $item['starts_at'] }}"
              data-color="{{ $item['color'] }}"
              class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition"
              title="ویرایش">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
              </svg>
            </button>

            <form action="{{ route('calendar.events.destroy', $item['id']) }}" method="POST"
              onsubmit="return confirm('این رویداد حذف شود؟')">
              @csrf
              @method('DELETE')
              <button type="submit"
                class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition"
                title="حذف">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </form>
          </div>
          @endif
        </div>
        @empty
        <div class="text-center py-10">
          <p class="text-sm font-medium text-slate-900">مورد پیش‌رویی نیست</p>
          <p class="text-xs text-slate-500 mt-1">وظایف دارای موعد اینجا نمایش داده می‌شوند.</p>
        </div>
        @endforelse
      </div>
    </div>
  </div>

  <div id="editEventModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 transition-opacity duration-300 opacity-0">
    <div id="editEventModalContainer"
      class="bg-white rounded-2xl w-full max-w-md p-6 mx-4 transform transition-all duration-300 scale-95 opacity-0">

      <div class="flex items-center justify-between mb-5">
        <h2 class="text-lg font-semibold text-slate-900">ویرایش رویداد</h2>
        <button type="button" onclick="closeEditEventModal()" class="text-slate-400 hover:text-blue-600 text-xl">✕</button>
      </div>

      <form id="editEventForm" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
          <label class="text-sm text-slate-600">عنوان</label>
          <input type="text" name="title" id="editEventTitle" required
            class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>

        <div>
          <label class="text-sm text-slate-600">پروژه / برچسب (اختیاری)</label>
          <input type="text" name="project_name" id="editEventProject"
            class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>

        <div>
          <label class="text-sm text-slate-600">تاریخ و ساعت</label>
          <input type="datetime-local" name="starts_at" id="editEventStarts" required
            class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>

        <div>
          <label class="text-sm text-slate-600 mb-2 block">رنگ</label>
          <div class="flex items-center gap-3" id="editEventColors">
            @foreach(['bg-blue-500','bg-emerald-500','bg-orange-500','bg-red-500','bg-purple-500','bg-slate-800'] as $color)
            <label class="cursor-pointer">
              <input type="radio" name="color" value="{{ $color }}" class="peer sr-only edit-color-radio">
              <span class="block w-7 h-7 rounded-full {{ $color }} ring-2 ring-offset-2 ring-transparent peer-checked:ring-blue-600 transition"></span>
            </label>
            @endforeach
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
          <button type="button" onclick="closeEditEventModal()"
            class="px-4 py-2 rounded-xl border border-slate-200 text-sm hover:bg-slate-50 transition">
            انصراف
          </button>
          <button type="submit"
            class="px-5 py-2 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
            ذخیره تغییرات
          </button>
        </div>
      </form>
    </div>
  </div>

  <div id="eventModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 transition-opacity duration-300 opacity-0">
    <div id="eventModalContainer"
      class="bg-white rounded-2xl w-full max-w-md p-6 mx-4 transform transition-all duration-300 scale-95 opacity-0">

      <div class="flex items-center justify-between mb-5">
        <h2 class="text-lg font-semibold text-slate-900">افزودن رویداد</h2>
        <button type="button" onclick="closeEventModal()" class="text-slate-400 hover:text-blue-600 text-xl">✕</button>
      </div>

      @if ($errors->any())
      <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3">
        <ul class="list-disc list-inside text-sm text-red-600">
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <form action="{{ route('calendar.events.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
          <label class="text-sm text-slate-600">عنوان</label>
          <input type="text" name="title" value="{{ old('title') }}" required placeholder="عنوان رویداد"
            class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>

        <div>
          <label class="text-sm text-slate-600">پروژه / برچسب (اختیاری)</label>
          <input type="text" name="project_name" value="{{ old('project_name') }}" placeholder="مثلاً بازطراحی وب‌سایت"
            class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>

        <div>
          <label class="text-sm text-slate-600">تاریخ و ساعت</label>
          <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" required
            class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>

        <div>
          <label class="text-sm text-slate-600 mb-2 block">رنگ</label>
          <div class="flex items-center gap-3">
            @foreach([
              'bg-blue-500' => 'آبی',
              'bg-emerald-500' => 'سبز',
              'bg-orange-500' => 'نارنجی',
              'bg-red-500' => 'قرمز',
              'bg-purple-500' => 'بنفش',
              'bg-slate-800' => 'تیره',
            ] as $value => $label)
            <label class="cursor-pointer" title="{{ $label }}">
              <input type="radio" name="color" value="{{ $value }}" class="peer sr-only"
                {{ old('color', 'bg-blue-500') === $value ? 'checked' : '' }}>
              <span class="block w-7 h-7 rounded-full {{ $value }} ring-2 ring-offset-2 ring-transparent peer-checked:ring-blue-600 transition"></span>
            </label>
            @endforeach
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
          <button type="button" onclick="closeEventModal()"
            class="px-4 py-2 rounded-xl border border-slate-200 text-sm hover:bg-slate-50 transition">
            انصراف
          </button>
          <button type="submit"
            class="px-5 py-2 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
            ایجاد رویداد
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openEditEventModal(button) {
      const modal = document.getElementById('editEventModal');
      const box = document.getElementById('editEventModalContainer');
      const id = button.dataset.id;

      document.getElementById('editEventForm').action = `/calendar/events/${id}`;
      document.getElementById('editEventTitle').value = button.dataset.title || '';
      document.getElementById('editEventProject').value = button.dataset.project || '';
      document.getElementById('editEventStarts').value = button.dataset.starts || '';

      const color = button.dataset.color || 'bg-blue-500';
      document.querySelectorAll('.edit-color-radio').forEach(radio => {
        radio.checked = radio.value === color;
      });

      modal.classList.remove('hidden');
      modal.classList.add('flex');
      setTimeout(() => {
        modal.classList.remove('opacity-0');
        box.classList.remove('scale-95', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');
      }, 10);
    }

    function closeEditEventModal() {
      const modal = document.getElementById('editEventModal');
      const box = document.getElementById('editEventModalContainer');
      modal.classList.add('opacity-0');
      box.classList.remove('scale-100', 'opacity-100');
      box.classList.add('scale-95', 'opacity-0');
      setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }, 300);
    }

    document.getElementById('editEventModal')?.addEventListener('click', function(e) {
      if (e.target === this) closeEditEventModal();
    });

    function openEventModal() {
      const modal = document.getElementById('eventModal');
      const box = document.getElementById('eventModalContainer');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      setTimeout(() => {
        modal.classList.remove('opacity-0');
        box.classList.remove('scale-95', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');
      }, 10);
    }

    function closeEventModal() {
      const modal = document.getElementById('eventModal');
      const box = document.getElementById('eventModalContainer');
      modal.classList.add('opacity-0');
      box.classList.remove('scale-100', 'opacity-100');
      box.classList.add('scale-95', 'opacity-0');
      setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }, 300);
    }

    document.getElementById('eventModal')?.addEventListener('click', function(e) {
      if (e.target === this) closeEventModal();
    });

    @if($errors -> any())
    document.addEventListener('DOMContentLoaded', openEventModal);
    @endif
  </script>
</x-dashboard-layout>