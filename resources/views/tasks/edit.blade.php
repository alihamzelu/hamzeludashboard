<head>
    <title>ویرایش وظیفه | مدیریت پروژه</title>
</head>

<x-dashboard-layout>
    @php
        $selectedUsers = collect(old(
            'assigned_user_ids',
            $task->users->pluck('id')->all()
        ))->map(fn ($id) => (int) $id)->all();
    @endphp

    <div class="max-w-3xl mr-0 ml-auto" dir="rtl">

        <a href="{{ route('tasks.show', $task->uid) }}"
           class="text-sm text-slate-500 hover:text-blue-600 inline-flex items-center gap-1 mb-4 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
            بازگشت به وظیفه
        </a>

        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">
            ویرایش وظیفه
        </h1>

        <p class="text-sm text-slate-500 mt-1 mb-6">
            اطلاعات وظیفه را ویرایش کنید.
        </p>

        <form action="{{ route('projects.tasks.update', ['project' => $project, 'task' => $task]) }}"
              method="POST"
              class="space-y-6">

            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-slate-700 mb-1.5">
                    عنوان وظیفه
                </label>

                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title', $task->title) }}"
                    required
                    maxlength="255"
                    autofocus
                    class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600"
                    placeholder="مثلاً طراحی صفحه اصلی"
                >

                @error('title')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">
                    توضیحات
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 resize-none"
                    placeholder="توضیحات وظیفه..."
                >{{ old('description', $task->description) }}</textarea>

                @error('description')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 mb-1.5">
                        وضعیت
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-600"
                    >
                        <option value="todo" @selected(old('status', $task->status) === 'todo')>
                            انجام نشده
                        </option>

                        <option value="in_progress" @selected(old('status', $task->status) === 'in_progress')>
                            در حال انجام
                        </option>

                        <option value="review" @selected(old('status', $task->status) === 'review')>
                            بازبینی
                        </option>

                        <option value="done" @selected(old('status', $task->status) === 'done')>
                            انجام‌شده
                        </option>
                    </select>

                    @error('status')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-slate-700 mb-1.5">
                        اولویت
                    </label>

                    <select
                        id="priority"
                        name="priority"
                        required
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-600"
                    >
                        <option value="low" @selected(old('priority', $task->priority) === 'low')>
                            کم
                        </option>

                        <option value="medium" @selected(old('priority', $task->priority) === 'medium')>
                            متوسط
                        </option>

                        <option value="high" @selected(old('priority', $task->priority) === 'high')>
                            زیاد
                        </option>
                    </select>

                    @error('priority')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    مسئولان وظیفه
                </label>

                <div class="border border-slate-200 rounded-xl bg-white p-4 min-h-16">

                    @forelse($users as $user)
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:bg-slate-50 cursor-pointer transition mb-2 last:mb-0">

                            <input
                                type="checkbox"
                                name="assigned_user_ids[]"
                                value="{{ $user->id }}"
                                @checked(in_array((int) $user->id, $selectedUsers, true))
                                class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600"
                            >

                            <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-semibold">
                                {{ mb_substr($user->name, 0, 1) }}
                            </span>

                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-700">
                                    {{ $user->name }}
                                </p>

                                <p class="text-xs text-slate-400 truncate">
                                    {{ $user->email }}
                                </p>
                            </div>

                        </label>
                    @empty
                        <p class="text-sm text-slate-400">
                            عضوی برای این پروژه پیدا نشد.
                        </p>
                    @endforelse

                </div>

                @error('assigned_user_ids')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror

                @error('assigned_user_ids.*')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror

                <p class="text-xs text-slate-400 mt-2">
                    می‌توانید چند نفر از اعضای پروژه را انتخاب کنید.
                </p>
            </div>

            <div class="relative w-full" dir="rtl" id="picker-due_date">

                <label for="due_date-jalali" class="block text-sm font-medium text-slate-700 mb-1.5">
                    تاریخ سررسید
                </label>

                <div class="relative">
                    <input
                        id="due_date-jalali"
                        type="text"
                        readonly
                        autocomplete="off"
                        placeholder="انتخاب تاریخ سررسید"
                        value="{{ old('due_date_jalali', $task->due_date ? verta($task->due_date)->format('Y/m/d') : '') }}"
                        class="w-full px-4 py-2.5 pl-10 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 cursor-pointer bg-white"
                    >

                    <svg
                        class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.7"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/>
                    </svg>
                </div>

                <input
                    type="hidden"
                    id="due_date"
                    name="due_date"
                    value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                >

                <div
                    id="due_date-calendar"
                    class="hidden absolute z-50 top-full mt-2 right-0 w-80 max-w-[90vw] rounded-2xl border border-slate-200 bg-white p-4 shadow-xl"
                >
                    <div class="flex items-center justify-between mb-4">
                        <button type="button" data-prev="due_date"
                            class="rounded-lg p-2 hover:bg-slate-100 text-slate-600"
                            aria-label="ماه قبل">&#10095;</button>

                        <div class="text-center">
                            <div id="due_date-title" class="font-bold text-slate-800"></div>
                            <div class="text-xs text-slate-400 mt-1">تقویم شمسی</div>
                        </div>

                        <button type="button" data-next="due_date"
                            class="rounded-lg p-2 hover:bg-slate-100 text-slate-600"
                            aria-label="ماه بعد">&#10094;</button>
                    </div>

                    <div class="grid grid-cols-7 mb-2 text-center text-xs text-slate-400">
                        @foreach (['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'] as $dayName)
                            <div class="py-2">{{ $dayName }}</div>
                        @endforeach
                    </div>

                    <div id="due_date-days" class="grid grid-cols-7 gap-1"></div>

                    <div class="flex justify-between items-center border-t border-slate-100 mt-4 pt-3">
                        <button type="button" data-today="due_date"
                            class="text-sm text-blue-600 hover:text-blue-800">امروز</button>

                        <button type="button" data-clear="due_date"
                            class="text-sm text-slate-500 hover:text-red-600">پاک کردن</button>
                    </div>
                </div>

                @error('due_date')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">

                <a href="{{ route('tasks.index') }}"
                   class="px-5 py-2.5 text-sm font-medium text-slate-500 hover:text-slate-900 transition">
                    انصراف
                </a>

                <button type="submit"
                    class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                    ذخیره تغییرات
                </button>

            </div>

        </form>
    </div>

<script>
(() => {
    const monthNames = [
        'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
        'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
    ];

    const pad = n => String(n).padStart(2, '0');

    function toJalali(gy, gm, gd) {
        const gdm = [0, 31, ((gy % 4 === 0 && gy % 100 !== 0) || gy % 400 === 0) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        let jy = gy <= 1600 ? 0 : 979;
        gy -= gy <= 1600 ? 621 : 1600;

        const gy2 = gm > 2 ? gy + 1 : gy;

        let days = 365 * gy + Math.floor((gy2 + 3) / 4)
            - Math.floor((gy2 + 99) / 100)
            + Math.floor((gy2 + 399) / 400) - 80 + gd;

        for (let i = 0; i < gm; i++) days += gdm[i];

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

        const jd = 1 + (days < 186 ? days % 31 : (days - 186) % 30);

        return [jy, jm, jd];
    }

    function toGregorian(jy, jm, jd) {
        jy += 1595;

        let days = -355668 + 365 * jy
            + Math.floor(jy / 33) * 8
            + Math.floor(((jy % 33) + 3) / 4) + jd;

        days += jm < 7 ? (jm - 1) * 31 : (jm - 7) * 30 + 186;

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

    function monthLength(year, month) {
        if (month <= 6) return 31;
        if (month <= 11) return 30;

        const first = toGregorian(year, 1, 1);
        const next = toGregorian(year + 1, 1, 1);

        const firstDate = new Date(first[0], first[1] - 1, first[2]);
        const nextDate = new Date(next[0], next[1] - 1, next[2]);

        return Math.round((nextDate - firstDate) / 86400000) === 366 ? 30 : 29;
    }

    const now = new Date();
    const today = toJalali(now.getFullYear(), now.getMonth() + 1, now.getDate());

    const calendars = {};

    function initCalendar(field) {
        const input = document.getElementById(`${field}-jalali`);
        const hidden = document.getElementById(field);
        const calendar = document.getElementById(`${field}-calendar`);
        const title = document.getElementById(`${field}-title`);
        const daysBox = document.getElementById(`${field}-days`);

        if (!input || !hidden || !calendar || !title || !daysBox) return;

        let year = today[0];
        let month = today[1];
        let selected = null;

        if (hidden.value) {
            const match = hidden.value.match(/^(\d{4})-(\d{2})-(\d{2})$/);

            if (match) {
                selected = toJalali(
                    Number(match[1]),
                    Number(match[2]),
                    Number(match[3])
                );

                year = selected[0];
                month = selected[1];

                input.value = `${selected[0]}/${pad(selected[1])}/${pad(selected[2])}`;
            }
        }

        function render() {
            title.textContent = `${monthNames[month - 1]} ${year}`;
            daysBox.innerHTML = '';

            const first = toGregorian(year, month, 1);
            const offset = (new Date(first[0], first[1] - 1, first[2]).getDay() + 1) % 7;

            for (let i = 0; i < offset; i++) {
                daysBox.appendChild(document.createElement('div'));
            }

            for (let day = 1; day <= monthLength(year, month); day++) {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = day;

                const isSelected = selected &&
                    selected[0] === year &&
                    selected[1] === month &&
                    selected[2] === day;

                const isToday = today[0] === year &&
                    today[1] === month &&
                    today[2] === day;

                button.className = [
                    'h-9 rounded-lg text-sm transition',
                    isSelected ? 'bg-blue-600 text-white font-bold' : 'text-gray-700 hover:bg-blue-50',
                    isToday && !isSelected ? 'ring-1 ring-blue-500 text-blue-600' : ''
                ].join(' ');

                button.addEventListener('click', () => setDate(year, month, day));

                daysBox.appendChild(button);
            }
        }

        function setDate(jy, jm, jd) {
            selected = [jy, jm, jd];

            const gregorian = toGregorian(jy, jm, jd);

            input.value = `${jy}/${pad(jm)}/${pad(jd)}`;
            hidden.value = `${gregorian[0]}-${pad(gregorian[1])}-${pad(gregorian[2])}`;

            calendar.classList.add('hidden');

            input.dispatchEvent(new Event('change', { bubbles: true }));
            hidden.dispatchEvent(new Event('change', { bubbles: true }));

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

        calendars[field] = {
            input,
            hidden,
            calendar,

            today() {
                year = today[0];
                month = today[1];
                setDate(...today);
            },

            clear() {
                input.value = '';
                hidden.value = '';
                selected = null;

                calendar.classList.add('hidden');

                render();

                input.dispatchEvent(new Event('change', { bubbles: true }));
                hidden.dispatchEvent(new Event('change', { bubbles: true }));
            }
        };

        input.addEventListener('click', () => {
            Object.values(calendars).forEach(item => {
                if (item.calendar !== calendar) {
                    item.calendar.classList.add('hidden');
                }
            });

            calendar.classList.toggle('hidden');
        });

        document.querySelector(`[data-prev="${field}"]`)
            .addEventListener('click', () => moveMonth(-1));

        document.querySelector(`[data-next="${field}"]`)
            .addEventListener('click', () => moveMonth(1));

        document.querySelector(`[data-today="${field}"]`)
            .addEventListener('click', () => calendars[field].today());

        document.querySelector(`[data-clear="${field}"]`)
            .addEventListener('click', () => calendars[field].clear());

        render();
    }

    initCalendar('due_date');

    document.addEventListener('click', event => {
        Object.values(calendars).forEach(item => {
            if (!item.calendar.contains(event.target) && !item.input.contains(event.target)) {
                item.calendar.classList.add('hidden');
            }
        });
    });
})();
</script>

</x-dashboard-layout>