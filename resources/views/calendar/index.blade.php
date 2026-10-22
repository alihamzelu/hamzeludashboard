<x-dashboard-layout>

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

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">
                تقویم
            </h1>

            <p class="text-slate-500 text-sm mt-1">
                موعد وظایف و رویدادهای شما
            </p>
        </div>

        <button
            type="button"
            onclick="openEventModal()"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>

            افزودن رویداد
        </button>
    </div>


    {{-- Calendar --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Main Calendar --}}
        <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">

            {{-- Calendar Header --}}
            <div class="flex items-center justify-between mb-6">

                <h2 class="text-lg font-semibold text-slate-900">
                    {{ $faMonths[$month] ?? '' }}
                    {{ $year }}
                </h2>

                <div class="flex items-center gap-2">

                    {{-- Previous Month --}}
                    <a
                        href="{{ route('calendar.index', [
                            'month' => $prevMonth['month'],
                            'year' => $prevMonth['year']
                        ]) }}"
                        class="p-2 rounded-xl hover:bg-slate-100 transition"
                        title="ماه قبل"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>


                    {{-- Today --}}
                    <a
                        href="{{ route('calendar.index') }}"
                        class="px-3 py-1.5 text-sm font-medium rounded-lg hover:bg-blue-50 hover:text-blue-700 transition"
                    >
                        امروز
                    </a>


                    {{-- Next Month --}}
                    <a
                        href="{{ route('calendar.index', [
                            'month' => $nextMonth['month'],
                            'year' => $nextMonth['year']
                        ]) }}"
                        class="p-2 rounded-xl hover:bg-slate-100 transition"
                        title="ماه بعد"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>

                </div>
            </div>


            {{-- Week Days --}}
            <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-slate-400 mb-2">

                @foreach($weekDays as $label)

                    <div class="py-2">
                        {{ $label }}
                    </div>

                @endforeach

            </div>


            {{-- Days --}}
            <div class="grid grid-cols-7 gap-1 text-sm">

                @foreach($calendarDays as $day)

                    <div
                        class="
                            min-h-[72px]
                            sm:min-h-[88px]
                            rounded-xl
                            p-1.5
                            flex
                            flex-col
                            {{ $day['is_current_month'] ? 'bg-white' : 'bg-slate-50' }}
                            {{ $day['is_today'] ? 'ring-2 ring-blue-600/20' : '' }}
                        "
                    >

                        @if($day['day_number'])

                            <span
                                @class([
                                    'w-7 h-7 flex items-center justify-center text-xs rounded-full mb-1',

                                    'bg-blue-600 text-white font-semibold'
                                        => $day['is_today'],

                                    'text-slate-300'
                                        => !$day['is_current_month'] && !$day['is_today'],

                                    'text-slate-700'
                                        => $day['is_current_month'] && !$day['is_today'],
                                ])
                            >
                                {{ $day['day_number'] }}
                            </span>


                            {{-- Events --}}
                            @if(isset($day['items']) && $day['items']->isNotEmpty())

                                <div class="flex flex-wrap gap-0.5 mt-auto">

                                    @foreach($day['items']->take(3) as $item)

                                        <span
                                            class="w-1.5 h-1.5 rounded-full {{ $item['color'] }}"
                                            title="{{ $item['title'] }}"
                                        ></span>

                                    @endforeach


                                    @if($day['items']->count() > 3)

                                        <span class="text-[9px] text-slate-400 leading-none">
                                            +{{ $day['items']->count() - 3 }}
                                        </span>

                                    @endif

                                </div>

                            @endif

                        @endif

                    </div>

                @endforeach

            </div>


            {{-- Legend --}}
            <div class="flex flex-wrap items-center gap-4 mt-5 pt-4 border-t border-slate-100 text-xs text-slate-500">

                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    وظیفه
                </span>

                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    رویداد
                </span>

                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    وظیفه انجام‌شده
                </span>

            </div>

        </div>


        {{-- Upcoming --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">

            <h2 class="font-semibold text-slate-900 mb-5">
                پیش‌رو
            </h2>


            <div class="space-y-2">

                @forelse($upcomingItems as $item)

                    <div class="flex gap-3 p-3 rounded-xl hover:bg-slate-50 transition group">

                        <div class="w-1 rounded-full {{ $item['color'] }} flex-shrink-0"></div>


                        <div class="flex-1 min-w-0">

                            <div class="flex items-center gap-2">

                                @if($item['type'] === 'task' && !empty($item['url']))

                                    <a
                                        href="{{ $item['url'] }}"
                                        class="text-sm font-medium text-slate-900 truncate hover:text-blue-600 transition"
                                    >
                                        {{ $item['title'] }}
                                    </a>

                                @else

                                    <p class="text-sm font-medium text-slate-900 truncate">
                                        {{ $item['title'] }}
                                    </p>

                                @endif


                                <span
                                    @class([
                                        'text-[10px] px-1.5 py-0.5 rounded font-medium flex-shrink-0',

                                        'bg-emerald-50 text-emerald-700'
                                            => $item['type'] === 'task',

                                        'bg-blue-50 text-blue-700'
                                            => $item['type'] === 'event',
                                    ])
                                >
                                    {{ $item['type'] === 'task' ? 'وظیفه' : 'رویداد' }}
                                </span>

                            </div>


                            {{-- Date --}}
                            <p class="text-xs text-slate-500 mt-0.5">

                                @if(!empty($item['is_today']) && $item['is_today'])

                                    امروز

                                @elseif(!empty($item['is_tomorrow']) && $item['is_tomorrow'])

                                    فردا

                                @else

                                    {{ $item['jalali_date'] ?? '' }}

                                @endif


                                @if($item['type'] === 'event' && !empty($item['time']))

                                    · {{ $item['time'] }}

                                @endif

                            </p>


                            @if(!empty($item['sub_title']))

                                <p class="text-xs text-slate-400 mt-0.5 truncate">
                                    {{ $item['sub_title'] }}
                                </p>

                            @endif

                        </div>


                        {{-- Event Actions --}}
                        @if($item['type'] === 'event')

                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition flex-shrink-0">

                                {{-- Edit --}}
                                <button
                                    type="button"
                                    onclick="openEditEventModal(this)"
                                    data-id="{{ $item['id'] }}"
                                    data-title="{{ e($item['title']) }}"
                                    data-project="{{ e($item['sub_title'] ?? '') }}"
                                    data-starts="{{ $item['starts_at'] }}"
                                    data-color="{{ $item['color'] }}"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition"
                                    title="ویرایش"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>


                                {{-- Delete --}}
                                <form
                                    action="{{ route('calendar.events.destroy', $item['id']) }}"
                                    method="POST"
                                    onsubmit="return confirm('این رویداد حذف شود؟')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition"
                                        title="حذف"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v-3a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>

                                </form>

                            </div>

                        @endif

                    </div>

                @empty

                    <div class="text-center py-10">

                        <p class="text-sm font-medium text-slate-900">
                            مورد پیش‌رویی نیست
                        </p>

                        <p class="text-xs text-slate-500 mt-1">
                            وظایف دارای موعد اینجا نمایش داده می‌شوند.
                        </p>

                    </div>

                @endforelse

            </div>

        </div>

    </div>


    {{-- ========================= --}}
    {{-- Edit Event Modal --}}
    {{-- ========================= --}}

    <div
        id="editEventModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 transition-opacity duration-300 opacity-0"
    >

        <div
            id="editEventModalContainer"
            class="bg-white rounded-2xl w-full max-w-md p-6 mx-4 transform transition-all duration-300 scale-95 opacity-0"
        >

            <div class="flex items-center justify-between mb-5">

                <h2 class="text-lg font-semibold text-slate-900">
                    ویرایش رویداد
                </h2>

                <button
                    type="button"
                    onclick="closeEditEventModal()"
                    class="text-slate-400 hover:text-blue-600 text-xl"
                >
                    ✕
                </button>

            </div>


            <form
                id="editEventForm"
                method="POST"
                class="space-y-4"
            >
                @csrf
                @method('PUT')


                {{-- Title --}}
                <div>

                    <label class="text-sm text-slate-600">
                        عنوان
                    </label>

                    <input
                        type="text"
                        name="title"
                        id="editEventTitle"
                        required
                        class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none"
                    >

                </div>


                {{-- Project --}}
                <div>

                    <label class="text-sm text-slate-600">
                        پروژه / برچسب (اختیاری)
                    </label>

                    <input
                        type="text"
                        name="project_name"
                        id="editEventProject"
                        class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none"
                    >

                </div>


                {{-- Date --}}

                {{-- Date & Jalali Picker: Edit Event --}}
                <div>
                    <label class="text-sm text-slate-600 block mb-1">
                        تاریخ و ساعت
                    </label>

                    <div class="relative jalali-picker" dir="rtl">
                        <div class="flex gap-2">
                            <input
                                type="text"
                                name="starts_at"
                                id="editEventStarts"
                                required
                                autocomplete="off"
                                placeholder="1405/07/29 14:30"
                                class="jalali-datetime w-full min-w-0 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none"
                            >

                            <button
                                type="button"
                                class="picker-toggle shrink-0 px-3 rounded-xl border border-slate-200 hover:bg-slate-50"
                                aria-label="انتخاب تاریخ"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="5" width="18" height="16" rx="2"/>
                                    <path stroke-linecap="round" d="M16 3v4M8 3v4M3 11h18"/>
                                </svg>
                            </button>
                        </div>

                        <div class="jalali-calendar hidden absolute z-[70] top-full mt-2 right-0 w-80 max-w-[90vw] rounded-2xl border border-gray-200 bg-white p-4 shadow-xl">
                            <div class="flex items-center justify-between mb-4">
                                <button type="button"
                                    class="picker-prev rounded-lg p-2 hover:bg-gray-100"
                                    aria-label="ماه قبل">&#10095;</button>

                                <div class="text-center">
                                    <div class="picker-title font-bold text-gray-800"></div>
                                    <div class="text-xs text-gray-400 mt-1">تقویم شمسی</div>
                                </div>

                                <button type="button"
                                    class="picker-next rounded-lg p-2 hover:bg-gray-100"
                                    aria-label="ماه بعد">&#10094;</button>
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

                            <div class="picker-days grid grid-cols-7 gap-1"></div>

                            <div class="flex justify-between items-center border-t mt-4 pt-3">
                                <button type="button"
                                    class="picker-today text-sm text-blue-600 hover:text-blue-800">
                                    امروز
                                </button>

                                <button type="button"
                                    class="picker-close text-sm text-gray-500 hover:text-gray-700">
                                    بستن
                                </button>
                            </div>
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-400 mt-1">
                        تاریخ شمسی و ساعت را وارد کنید.
                    </p>
                </div>



                {{-- Color --}}
                <div>

                    <label class="text-sm text-slate-600 mb-2 block">
                        رنگ
                    </label>

                    <div
                        class="flex items-center gap-3"
                        id="editEventColors"
                    >

                        @foreach([
                            'bg-blue-500',
                            'bg-emerald-500',
                            'bg-orange-500',
                            'bg-red-500',
                            'bg-purple-500',
                            'bg-slate-800'
                        ] as $color)

                            <label class="cursor-pointer">

                                <input
                                    type="radio"
                                    name="color"
                                    value="{{ $color }}"
                                    class="peer sr-only edit-color-radio"
                                >

                                <span
                                    class="block w-7 h-7 rounded-full {{ $color }} ring-2 ring-offset-2 ring-transparent peer-checked:ring-blue-600 transition"
                                ></span>

                            </label>

                        @endforeach

                    </div>

                </div>


                {{-- Buttons --}}
                <div class="flex justify-end gap-3 pt-2">

                    <button
                        type="button"
                        onclick="closeEditEventModal()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-sm hover:bg-slate-50 transition"
                    >
                        انصراف
                    </button>

                    <button
                        type="submit"
                        class="px-5 py-2 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition"
                    >
                        ذخیره تغییرات
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- ========================= --}}
    {{-- Add Event Modal --}}
    {{-- ========================= --}}

    <div
        id="eventModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 transition-opacity duration-300 opacity-0"
    >

        <div
            id="eventModalContainer"
            class="bg-white rounded-2xl w-full max-w-md p-6 mx-4 transform transition-all duration-300 scale-95 opacity-0"
        >

            <div class="flex items-center justify-between mb-5">

                <h2 class="text-lg font-semibold text-slate-900">
                    افزودن رویداد
                </h2>

                <button
                    type="button"
                    onclick="closeEventModal()"
                    class="text-slate-400 hover:text-blue-600 text-xl"
                >
                    ✕
                </button>

            </div>


            {{-- Validation Errors --}}
            @if ($errors->any())

                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3">

                    <ul class="list-disc list-inside text-sm text-red-600">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <form
                action="{{ route('calendar.events.store') }}"
                method="POST"
                class="space-y-4"
            >
                @csrf


                {{-- Title --}}
                <div>

                    <label class="text-sm text-slate-600">
                        عنوان
                    </label>

                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        placeholder="عنوان رویداد"
                        class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none"
                    >

                </div>


                {{-- Project --}}
                <div>

                    <label class="text-sm text-slate-600">
                        پروژه / برچسب (اختیاری)
                    </label>

                    <input
                        type="text"
                        name="project_name"
                        value="{{ old('project_name') }}"
                        placeholder="مثلاً بازطراحی وب‌سایت"
                        class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none"
                    >

                </div>


                {{-- Date --}}
                
                {{-- Date & Jalali Picker: Add Event --}}
                <div>
                    <label class="text-sm text-slate-600 block mb-1">
                        تاریخ و ساعت
                    </label>

                    <div class="relative jalali-picker" dir="rtl">
                        <div class="flex gap-2">
                            <input
                                type="text"
                                name="starts_at"
                                value="{{ old('starts_at') }}"
                                required
                                autocomplete="off"
                                placeholder="1405/07/29 14:30"
                                class="jalali-datetime w-full min-w-0 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none"
                            >

                            <button
                                type="button"
                                class="picker-toggle shrink-0 px-3 rounded-xl border border-slate-200 hover:bg-slate-50"
                                aria-label="انتخاب تاریخ"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="5" width="18" height="16" rx="2"/>
                                    <path stroke-linecap="round" d="M16 3v4M8 3v4M3 11h18"/>
                                </svg>
                            </button>
                        </div>

                        <div class="jalali-calendar hidden absolute z-[70] top-full mt-2 right-0 w-80 max-w-[90vw] rounded-2xl border border-gray-200 bg-white p-4 shadow-xl">
                            <div class="flex items-center justify-between mb-4">
                                <button type="button"
                                    class="picker-prev rounded-lg p-2 hover:bg-gray-100"
                                    aria-label="ماه قبل">&#10095;</button>

                                <div class="text-center">
                                    <div class="picker-title font-bold text-gray-800"></div>
                                    <div class="text-xs text-gray-400 mt-1">تقویم شمسی</div>
                                </div>

                                <button type="button"
                                    class="picker-next rounded-lg p-2 hover:bg-gray-100"
                                    aria-label="ماه بعد">&#10094;</button>
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

                            <div class="picker-days grid grid-cols-7 gap-1"></div>

                            <div class="flex justify-between items-center border-t mt-4 pt-3">
                                <button type="button"
                                    class="picker-today text-sm text-blue-600 hover:text-blue-800">
                                    امروز
                                </button>

                                <button type="button"
                                    class="picker-close text-sm text-gray-500 hover:text-gray-700">
                                    بستن
                                </button>
                            </div>
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-400 mt-1">
                        تاریخ شمسی و ساعت را وارد کنید؛ مثلاً 1405/07/29 14:30
                    </p>
                </div>



                {{-- Color --}}
                <div>

                    <label class="text-sm text-slate-600 mb-2 block">
                        رنگ
                    </label>

                    <div class="flex items-center gap-3">

                        @foreach([
                            'bg-blue-500' => 'آبی',
                            'bg-emerald-500' => 'سبز',
                            'bg-orange-500' => 'نارنجی',
                            'bg-red-500' => 'قرمز',
                            'bg-purple-500' => 'بنفش',
                            'bg-slate-800' => 'تیره',
                        ] as $value => $label)

                            <label
                                class="cursor-pointer"
                                title="{{ $label }}"
                            >

                                <input
                                    type="radio"
                                    name="color"
                                    value="{{ $value }}"
                                    class="peer sr-only"
                                    {{ old('color', 'bg-blue-500') === $value ? 'checked' : '' }}
                                >

                                <span
                                    class="block w-7 h-7 rounded-full {{ $value }} ring-2 ring-offset-2 ring-transparent peer-checked:ring-blue-600 transition"
                                ></span>

                            </label>

                        @endforeach

                    </div>

                </div>


                {{-- Buttons --}}
                <div class="flex justify-end gap-3 pt-2">

                    <button
                        type="button"
                        onclick="closeEventModal()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-sm hover:bg-slate-50 transition"
                    >
                        انصراف
                    </button>

                    <button
                        type="submit"
                        class="px-5 py-2 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition"
                    >
                        ایجاد رویداد
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- ========================= --}}
    {{-- JavaScript --}}
    {{-- ========================= --}}

    <script>

        // -----------------------------------------
        // Edit Event Modal
        // -----------------------------------------

        function openEditEventModal(button) {

            const modal = document.getElementById('editEventModal');
            const box = document.getElementById('editEventModalContainer');

            const id = button.dataset.id;

            document.getElementById('editEventForm').action =
                `/calendar/events/${id}`;

            document.getElementById('editEventTitle').value =
                button.dataset.title || '';

            document.getElementById('editEventProject').value =
                button.dataset.project || '';

            document.getElementById('editEventStarts').value =
                button.dataset.starts || '';


            const color =
                button.dataset.color || 'bg-blue-500';

            document
                .querySelectorAll('.edit-color-radio')
                .forEach(radio => {

                    radio.checked =
                        radio.value === color;

                });


            modal.classList.remove('hidden');

            modal.classList.add('flex');


            setTimeout(() => {

                modal.classList.remove('opacity-0');

                box.classList.remove(
                    'scale-95',
                    'opacity-0'
                );

                box.classList.add(
                    'scale-100',
                    'opacity-100'
                );

            }, 10);
        }


        function closeEditEventModal() {

            const modal =
                document.getElementById('editEventModal');

            const box =
                document.getElementById('editEventModalContainer');


            modal.classList.add('opacity-0');

            box.classList.remove(
                'scale-100',
                'opacity-100'
            );

            box.classList.add(
                'scale-95',
                'opacity-0'
            );


            setTimeout(() => {

                modal.classList.add('hidden');

                modal.classList.remove('flex');

            }, 300);
        }


        document
            .getElementById('editEventModal')
            ?.addEventListener('click', function (e) {

                if (e.target === this) {

                    closeEditEventModal();

                }

            });


        // -----------------------------------------
        // Add Event Modal
        // -----------------------------------------

        function openEventModal() {

            const modal =
                document.getElementById('eventModal');

            const box =
                document.getElementById('eventModalContainer');


            modal.classList.remove('hidden');

            modal.classList.add('flex');


            setTimeout(() => {

                modal.classList.remove('opacity-0');

                box.classList.remove(
                    'scale-95',
                    'opacity-0'
                );

                box.classList.add(
                    'scale-100',
                    'opacity-100'
                );

            }, 10);
        }


        function closeEventModal() {

            const modal =
                document.getElementById('eventModal');

            const box =
                document.getElementById('eventModalContainer');


            modal.classList.add('opacity-0');

            box.classList.remove(
                'scale-100',
                'opacity-100'
            );

            box.classList.add(
                'scale-95',
                'opacity-0'
            );


            setTimeout(() => {

                modal.classList.add('hidden');

                modal.classList.remove('flex');

            }, 300);
        }


        document
            .getElementById('eventModal')
            ?.addEventListener('click', function (e) {

                if (e.target === this) {

                    closeEventModal();

                }

            });


        // -----------------------------------------
        // Open modal automatically when validation
        // fails
        // -----------------------------------------

        @if($errors->any())

            document.addEventListener(
                'DOMContentLoaded',
                openEventModal
            );

        @endif

    </script>






  <script>
  document.addEventListener('DOMContentLoaded', function () {
      'use strict';

      const monthNames = [
          'فروردین', 'اردیبهشت', 'خرداد',
          'تیر', 'مرداد', 'شهریور',
          'مهر', 'آبان', 'آذر',
          'دی', 'بهمن', 'اسفند'
      ];

      const pad = value => String(value).padStart(2, '0');

      function toJalali(gy, gm, gd) {
          const gdm = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];

          let gy2 = gm > 2 ? gy + 1 : gy;

          let days = 355666 + 365 * gy
              + Math.floor((gy2 + 3) / 4)
              - Math.floor((gy2 + 99) / 100)
              + Math.floor((gy2 + 399) / 400)
              + gd + gdm[gm - 1];

          let jy = -1595 + 33 * Math.floor(days / 12053);
          days %= 12053;

          jy += 4 * Math.floor(days / 1461);
          days %= 1461;

          if (days > 365) {
              jy += Math.floor((days - 1) / 365);
              days = (days - 1) % 365;
          }

          let jm, jd;

          if (days < 186) {
              jm = 1 + Math.floor(days / 31);
              jd = 1 + days % 31;
          } else {
              jm = 7 + Math.floor((days - 186) / 30);
              jd = 1 + (days - 186) % 30;
          }

          return [jy, jm, jd];
      }

      function jalaliToGregorian(jy, jm, jd) {
          const gy = jy + 621;
          const march = jalaliMarchDay(jy);

          const date = new Date(gy, 2, march);
          const offset = jm <= 6
              ? (jm - 1) * 31 + jd - 1
              : 186 + (jm - 7) * 30 + jd - 1;

          date.setDate(date.getDate() + offset);

          return new Date(date.getFullYear(), date.getMonth(), date.getDate());
      }

      function jalaliMarchDay(jy) {
          const breaks = [
              -61, 9, 38, 199, 426, 686, 756, 818, 1111,
              1181, 1210, 1635, 2060, 2097, 2192, 2262,
              2324, 2394, 2456, 3178
          ];

          let jp = breaks[0];
          let jump = 0;

          for (let i = 1; i < breaks.length; i++) {
              const jm = breaks[i];
              jump = jm - jp;

              if (jy < jm) break;

              jp = jm;
          }

          let n = jy - jp;
          let leapJ = -14;

          leapJ += Math.floor(n / 33) * 8;
          n %= 33;

          leapJ += Math.floor((n + 3) / 4);

          if (jump % 33 === 4 && jump - n === 4) {
              leapJ++;
          }

          const leapG = Math.floor((jy + 621) / 4)
              - Math.floor((Math.floor((jy + 621) / 100) + 1) * 3 / 4)
              - 150;

          return 20 + leapJ - leapG;
      }

      function jalaliMonthLength(year, month) {
          if (month <= 6) return 31;
          if (month <= 11) return 30;

          const current = jalaliMarchDay(year);
          const next = jalaliMarchDay(year + 1);

          return next < current ? 30 : 29;
      }

      function getTodayJalali() {
          const today = new Date();

          return toJalali(
              today.getFullYear(),
              today.getMonth() + 1,
              today.getDate()
          );
      }

      document.querySelectorAll('.jalali-picker').forEach(function (picker) {
          const input = picker.querySelector('.jalali-datetime');
          const calendar = picker.querySelector('.jalali-calendar');

          if (!input || !calendar) return;

          const title = calendar.querySelector('.picker-title');
          const daysContainer = calendar.querySelector('.picker-days');
          const prevButton = calendar.querySelector('.picker-prev');
          const nextButton = calendar.querySelector('.picker-next');
          const todayButton = calendar.querySelector('.picker-today');
          const closeButton = calendar.querySelector('.picker-close');
          const toggleButton = picker.querySelector('.picker-toggle');

          if (!title || !daysContainer) return;

          let viewYear;
          let viewMonth;

          function parseInput() {
              const value = input.value.trim().replace(/[۰-۹٠-٩]/g, digit => {
                  return '۰۱۲۳۴۵۶۷۸۹'.indexOf(digit) !== -1
                      ? String('۰۱۲۳۴۵۶۷۸۹'.indexOf(digit))
                      : String('٠١٢٣٤٥٦٧٨٩'.indexOf(digit));
              });

              const match = value.match(
                  /^(\d{4})\/(\d{1,2})\/(\d{1,2})(?:\s+(\d{1,2}):(\d{2}))?$/
              );

              if (!match) return null;

              const year = Number(match[1]);
              const month = Number(match[2]);
              const day = Number(match[3]);
              const hour = match[4] !== undefined ? Number(match[4]) : 9;
              const minute = match[5] !== undefined ? Number(match[5]) : 0;

              if (
                  year < 1000 ||
                  month < 1 || month > 12 ||
                  day < 1 || day > jalaliMonthLength(year, month) ||
                  hour > 23 || minute > 59
              ) {
                  return null;
              }

              return { year, month, day, hour, minute };
          }

          function writeInput(year, month, day, hour, minute) {
              input.value =
                  year + '/' + pad(month) + '/' + pad(day) +
                  ' ' + pad(hour) + ':' + pad(minute);

              input.dispatchEvent(new Event('change', { bubbles: true }));
              input.dispatchEvent(new Event('input', { bubbles: true }));
          }

          function renderCalendar() {
              title.textContent = monthNames[viewMonth - 1] + ' ' + viewYear;
              daysContainer.innerHTML = '';

              const firstDate = jalaliToGregorian(viewYear, viewMonth, 1);
              const offset = (firstDate.getDay() + 1) % 7;
              const totalDays = jalaliMonthLength(viewYear, viewMonth);
              const today = getTodayJalali();
              const selected = parseInput();

              for (let i = 0; i < offset; i++) {
                  const blank = document.createElement('span');
                  blank.className = 'h-9';
                  daysContainer.appendChild(blank);
              }

              for (let day = 1; day <= totalDays; day++) {
                  const button = document.createElement('button');

                  button.type = 'button';
                  button.textContent = day;
                  button.className =
                      'h-9 rounded-lg text-sm hover:bg-blue-50 transition';

                  if (
                      today[0] === viewYear &&
                      today[1] === viewMonth &&
                      today[2] === day
                  ) {
                      button.classList.add(
                          'bg-blue-100',
                          'text-blue-700',
                          'font-bold'
                      );
                  }

                  if (
                      selected &&
                      selected.year === viewYear &&
                      selected.month === viewMonth &&
                      selected.day === day
                  ) {
                      button.classList.add(
                          'bg-blue-600',
                          'text-white',
                          'font-bold'
                      );

                      button.classList.remove(
                          'bg-blue-100',
                          'text-blue-700'
                      );
                  }

                  button.addEventListener('click', function () {
                      const oldValue = parseInput();

                      writeInput(
                          viewYear,
                          viewMonth,
                          day,
                          oldValue ? oldValue.hour : 9,
                          oldValue ? oldValue.minute : 0
                      );

                      renderCalendar();
                      closeCalendar();
                  });

                  daysContainer.appendChild(button);
              }
          }

          function openCalendar() {
              const parsed = parseInput();

              if (parsed) {
                  viewYear = parsed.year;
                  viewMonth = parsed.month;
              } else {
                  const today = getTodayJalali();

                  viewYear = today[0];
                  viewMonth = today[1];
              }

              calendar.classList.remove('hidden');
              renderCalendar();
          }

          function closeCalendar() {
              calendar.classList.add('hidden');
          }

          function changeMonth(amount) {
              viewMonth += amount;

              if (viewMonth < 1) {
                  viewMonth = 12;
                  viewYear--;
              } else if (viewMonth > 12) {
                  viewMonth = 1;
                  viewYear++;
              }

              renderCalendar();
          }

          input.addEventListener('focus', openCalendar);
          input.addEventListener('click', openCalendar);

          if (toggleButton) {
              toggleButton.addEventListener('click', function () {
                  if (calendar.classList.contains('hidden')) {
                      openCalendar();
                  } else {
                      closeCalendar();
                  }
              });
          }

          if (prevButton) {
              prevButton.addEventListener('click', () => changeMonth(-1));
          }

          if (nextButton) {
              nextButton.addEventListener('click', () => changeMonth(1));
          }

          if (todayButton) {
              todayButton.addEventListener('click', function () {
                  const today = getTodayJalali();
                  const oldValue = parseInput();

                  viewYear = today[0];
                  viewMonth = today[1];

                  writeInput(
                      today[0],
                      today[1],
                      today[2],
                      oldValue ? oldValue.hour : 9,
                      oldValue ? oldValue.minute : 0
                  );

                  closeCalendar();
              });
          }

          if (closeButton) {
              closeButton.addEventListener('click', closeCalendar);
          }

          const modal = picker.closest('[id$="Modal"]');

          if (modal) {
              const observer = new MutationObserver(function () {
                  if (modal.classList.contains('hidden')) {
                      closeCalendar();
                  }
              });

              observer.observe(modal, {
                  attributes: true,
                  attributeFilter: ['class']
              });
          }
      });

      document.addEventListener('click', function (event) {
          document.querySelectorAll('.jalali-picker').forEach(function (picker) {
              if (!picker.contains(event.target)) {
                  const calendar = picker.querySelector('.jalali-calendar');

                  if (calendar) {
                      calendar.classList.add('hidden');
                  }
              }
          });
      });
  });
  </script>





</x-dashboard-layout>