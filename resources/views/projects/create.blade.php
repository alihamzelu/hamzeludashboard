<head>
    <title>ساخت پروژه | مدیریت پروژه</title>
</head>
<x-dashboard-layout>
    <div class="max-w-2xl" dir="rtl">
        <a href="{{ route('projects.index') }}"
            class="text-sm text-slate-500 hover:text-blue-600 inline-flex items-center gap-1 mb-4 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
            بازگشت به پروژه‌ها
        </a>

        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">ایجاد پروژه</h1>
        <p class="text-sm text-slate-500 mt-1 mb-6">
            جزئیات را وارد کنید تا پروژه جدید شروع شود
        </p>

        <form action="{{ route('projects.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">
                    نام پروژه
                </label>

                <input id="name" type="text" name="name"
                    value="{{ old('name') }}" required autofocus
                    class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
                    placeholder="مثلاً بازطراحی وب‌سایت" />

                @error('name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">
                    توضیحات
                </label>

                <textarea id="description" name="description" rows="4"
                    class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition resize-none"
                    placeholder="این پروژه درباره چیست؟">{{ old('description') }}</textarea>

                @error('description')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-slate-700 mb-1.5">
                    وضعیت
                </label>

                <select id="status" name="status"
                    class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition bg-white">
                    <option value="planning" @selected(old('status', 'planning') === 'planning')>برنامه‌ریزی</option>
                    <option value="active" @selected(old('status') === 'active')>فعال</option>
                    <option value="completed" @selected(old('status') === 'completed')>تکمیل‌شده</option>
                    <option value="archived" @selected(old('status') === 'archived')>آرشیو</option>
                </select>

                @error('status')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- تاریخ شروع --}}
                <div class="relative" data-jalali-picker>
                    <label for="start_date" class="block text-sm font-medium text-slate-700 mb-1.5">
                        تاریخ شروع
                    </label>

                    <div class="relative">
                        <input id="start_date" type="text" name="start_date"
                            value="{{ old('start_date') ? \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse(old('start_date')))->format('Y/m/d') : '' }}"
                            readonly autocomplete="off"
                            placeholder="انتخاب تاریخ شروع"
                            class="jalali-input w-full px-3 py-2.5 pl-10 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition cursor-pointer bg-white" />

                        <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                        </svg>
                    </div>

                    <input type="hidden" name="start_date_gregorian"
                        class="gregorian-value"
                        value="{{ old('start_date') }}" />

                    <div class="jalali-calendar hidden absolute z-50 top-full mt-2 right-0 w-80 max-w-full rounded-2xl border border-slate-200 bg-white p-4 shadow-xl">
                        <div class="flex items-center justify-between mb-4">
                            <button type="button" class="prev-month rounded-lg p-2 hover:bg-slate-100 text-slate-600" aria-label="ماه قبل">
                                &#10095;
                            </button>

                            <div class="text-center">
                                <div class="calendar-title font-bold text-slate-800"></div>
                                <div class="text-xs text-slate-400 mt-1">تقویم شمسی</div>
                            </div>

                            <button type="button" class="next-month rounded-lg p-2 hover:bg-slate-100 text-slate-600" aria-label="ماه بعد">
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
                            <button type="button" class="today-btn text-sm text-blue-600 hover:text-blue-800">
                                امروز
                            </button>

                            <button type="button" class="clear-date text-sm text-slate-500 hover:text-red-600">
                                پاک کردن
                            </button>
                        </div>
                    </div>

                    @error('start_date')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- تاریخ پایان --}}
                <div class="relative" data-jalali-picker>
                    <label for="end_date" class="block text-sm font-medium text-slate-700 mb-1.5">
                        تاریخ پایان
                    </label>

                    <div class="relative">
                        <input id="end_date" type="text" name="end_date"
                            value="{{ old('end_date') ? \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse(old('end_date')))->format('Y/m/d') : '' }}"
                            readonly autocomplete="off"
                            placeholder="انتخاب تاریخ پایان"
                            class="jalali-input w-full px-3 py-2.5 pl-10 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition cursor-pointer bg-white" />

                        <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                        </svg>
                    </div>

                    <input type="hidden" name="end_date_gregorian"
                        class="gregorian-value"
                        value="{{ old('end_date') }}" />

                    <div class="jalali-calendar hidden absolute z-50 top-full mt-2 right-0 w-80 max-w-full rounded-2xl border border-slate-200 bg-white p-4 shadow-xl">
                        <div class="flex items-center justify-between mb-4">
                            <button type="button" class="prev-month rounded-lg p-2 hover:bg-slate-100 text-slate-600" aria-label="ماه قبل">
                                &#10095;
                            </button>

                            <div class="text-center">
                                <div class="calendar-title font-bold text-slate-800"></div>
                                <div class="text-xs text-slate-400 mt-1">تقویم شمسی</div>
                            </div>

                            <button type="button" class="next-month rounded-lg p-2 hover:bg-slate-100 text-slate-600" aria-label="ماه بعد">
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
                            <button type="button" class="today-btn text-sm text-blue-600 hover:text-blue-800">
                                امروز
                            </button>

                            <button type="button" class="clear-date text-sm text-slate-500 hover:text-red-600">
                                پاک کردن
                            </button>
                        </div>
                    </div>

                    @error('end_date')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('projects.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-slate-500 hover:text-slate-900 transition">
                    انصراف
                </a>

                <button type="submit"
                    class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                    ایجاد پروژه
                </button>
            </div>
        </form>
    </div>

    <script>
        (() => {
            const pickers = document.querySelectorAll('[data-jalali-picker]');

            if (!pickers.length) return;

            const monthNames = [
                'فروردین', 'اردیبهشت', 'خرداد', 'تیر',
                'مرداد', 'شهریور', 'مهر', 'آبان',
                'آذر', 'دی', 'بهمن', 'اسفند'
            ];

            const pad = n => String(n).padStart(2, '0');

            function toJalali(gy, gm, gd) {
                const gdm = [
                    0, 31,
                    ((gy % 4 === 0 && gy % 100 !== 0) || gy % 400 === 0) ? 29 : 28,
                    31, 30, 31, 30, 31, 31, 30, 31, 30, 31
                ];

                let jy = gy <= 1600 ? 0 : 979;
                gy -= gy <= 1600 ? 621 : 1600;

                const gy2 = gm > 2 ? gy + 1 : gy;

                let days = 365 * gy
                    + Math.floor((gy2 + 3) / 4)
                    - Math.floor((gy2 + 99) / 100)
                    + Math.floor((gy2 + 399) / 400)
                    - 80 + gd;

                for (let i = 0; i < gm; i++) {
                    days += gdm[i];
                }

                jy += 33 * Math.floor(days / 12053);
                days %= 12053;

                jy += 4 * Math.floor(days / 1461);
                days %= 1461;

                if (days > 365) {
                    jy += Math.floor((days - 1) / 365);
                    days = (days - 1) % 365;
                }

                const jm = days < 186
                    ? 1 + Math.floor(days / 31)
                    : 7 + Math.floor((days - 186) / 30);

                const jd = 1 + (days < 186
                    ? days % 31
                    : (days - 186) % 30);

                return [jy, jm, jd];
            }

            function toGregorian(jy, jm, jd) {
                jy += 1595;

                let days = -355668
                    + 365 * jy
                    + Math.floor(jy / 33) * 8
                    + Math.floor(((jy % 33) + 3) / 4)
                    + jd;

                if (jm < 7) {
                    days += (jm - 1) * 31;
                } else {
                    days += (jm - 7) * 30 + 186;
                }

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

                const monthDays = [
                    0, 31, leap ? 29 : 28, 31, 30, 31, 30,
                    31, 31, 30, 31, 30, 31
                ];

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

                const currentStart = toGregorian(y, 1, 1);
                const nextStart = toGregorian(y + 1, 1, 1);

                const currentDate = new Date(
                    currentStart[0],
                    currentStart[1] - 1,
                    currentStart[2]
                );

                const nextDate = new Date(
                    nextStart[0],
                    nextStart[1] - 1,
                    nextStart[2]
                );

                return Math.round((nextDate - currentDate) / 86400000) === 366 ? 30 : 29;
            }

            const today = new Date();
            const currentJalali = toJalali(
                today.getFullYear(),
                today.getMonth() + 1,
                today.getDate()
            );

            function initPicker(root) {
                const input = root.querySelector('.jalali-input');
                const hiddenInput = root.querySelector('.gregorian-value');
                const calendar = root.querySelector('.jalali-calendar');
                const title = root.querySelector('.calendar-title');
                const daysBox = root.querySelector('.calendar-days');

                if (!input || !hiddenInput || !calendar || !title || !daysBox) return;

                let year = currentJalali[0];
                let month = currentJalali[1];
                let selected = null;

                function render() {
                    title.textContent = `${monthNames[month - 1]} ${year}`;
                    daysBox.innerHTML = '';

                    const firstDay = toGregorian(year, month, 1);

                    const weekday = new Date(
                        firstDay[0],
                        firstDay[1] - 1,
                        firstDay[2]
                    ).getDay();

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

                        const isSelected = selected &&
                            selected[0] === year &&
                            selected[1] === month &&
                            selected[2] === day;

                        const isToday =
                            currentJalali[0] === year &&
                            currentJalali[1] === month &&
                            currentJalali[2] === day;

                        button.classList.add(
                            ...(isSelected
                                ? ['bg-blue-600', 'text-white', 'font-bold']
                                : ['text-slate-700', 'hover:bg-blue-50'])
                        );

                        if (isToday && !isSelected) {
                            button.classList.add('ring-1', 'ring-blue-500', 'text-blue-600');
                        }

                        button.addEventListener('click', () => {
                            selected = [year, month, day];

                            const g = toGregorian(year, month, day);

                            input.value = `${year}/${pad(month)}/${pad(day)}`;
                            hiddenInput.value = `${g[0]}-${pad(g[1])}-${pad(g[2])}`;

                            calendar.classList.add('hidden');

                            input.dispatchEvent(new Event('change', { bubbles: true }));
                            render();
                        });

                        daysBox.appendChild(button);
                    }
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

                function selectToday() {
                    year = currentJalali[0];
                    month = currentJalali[1];
                    selected = [...currentJalali];

                    const g = toGregorian(...selected);

                    input.value = `${year}/${pad(month)}/${pad(selected[2])}`;
                    hiddenInput.value = `${g[0]}-${pad(g[1])}-${pad(g[2])}`;

                    calendar.classList.add('hidden');

                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    render();
                }

                function clearDate() {
                    input.value = '';
                    hiddenInput.value = '';
                    selected = null;

                    calendar.classList.add('hidden');
                    render();

                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }

                root.querySelector('.prev-month').addEventListener('click', () => moveMonth(-1));
                root.querySelector('.next-month').addEventListener('click', () => moveMonth(1));
                root.querySelector('.today-btn').addEventListener('click', selectToday);
                root.querySelector('.clear-date').addEventListener('click', clearDate);

                input.addEventListener('click', () => {
                    document.querySelectorAll('.jalali-calendar').forEach(item => {
                        if (item !== calendar) item.classList.add('hidden');
                    });

                    calendar.classList.toggle('hidden');
                });

                render();
            }

            pickers.forEach(initPicker);

            document.addEventListener('click', event => {
                pickers.forEach(root => {
                    if (!root.contains(event.target)) {
                        root.querySelector('.jalali-calendar')?.classList.add('hidden');
                    }
                });
            });

            document.querySelector('form')?.addEventListener('submit', event => {
                for (const root of pickers) {
                    const input = root.querySelector('.jalali-input');
                    const hiddenInput = root.querySelector('.gregorian-value');

                    if (input.value && !hiddenInput.value) {
                        event.preventDefault();
                        input.focus();
                        alert('لطفاً تاریخ را از تقویم انتخاب کنید.');
                        return;
                    }
                }
            });
        })();
    </script>
</x-dashboard-layout>