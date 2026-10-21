<head>
    <title>مدیریت پروژه | تیم</title>
</head>

<x-dashboard-layout>
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-xl font-bold text-slate-900 sm:text-2xl">
                تیم
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                افرادی که در پروژه‌ها با آن‌ها همکاری می‌کنید
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 13l4 4L19 7" />
            </svg>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-700">
            {{ session('info') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="mb-2 font-semibold">لطفاً موارد زیر را بررسی کنید:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('team.index') }}"
        method="GET"
        class="mb-8 flex items-center gap-3"
    >
        <div class="relative flex-1">
            <input
                id="team-search"
                name="search"
                type="text"
                value="{{ $search ?? request('search', '') }}"
                placeholder="جستجو با نام یا ایمیل..."
                class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm transition placeholder:text-slate-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
            </svg>
        </div>

        <button
            type="submit"
            class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-blue-700"
        >
            جستجو
        </button>

        @if(request()->filled('search'))
            <a
                href="{{ route('team.index') }}"
                class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 transition hover:bg-slate-50"
            >
                پاک‌کردن
            </a>
        @endif
    </form>

    <div class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M17 20h5v-2a3 3 0 00-5.2-2M17 20H7m10 0v-2c0-.7-.2-1.4-.6-2M7 20H2v-2a3 3 0 015.2-2M7 20v-2c0-.7.2-1.4.6-2m0 0a5 5 0 019.8 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <p class="text-sm text-slate-500">اعضای تیم</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">
                {{ $teams->count() }}
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-sm text-slate-500">دعوت‌های در انتظار</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">
                {{ $pendingInvites }}
            </p>
        </div>

        <div class="col-span-2 rounded-2xl border border-slate-200 bg-white p-4 sm:col-span-1">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M3 7h7l2 2h9v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                </svg>
            </div>
            <p class="text-sm text-slate-500">پروژه‌های مشترک</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">
                {{ $teams->sum('shared_projects_count') }}
            </p>
        </div>
    </div>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-900">
            هم‌تیمی‌های شما
        </h2>

        <span class="text-sm text-slate-500">
            {{ $teams->count() }} نفر
        </span>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3" id="team-list">
        @forelse($teams as $member)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 transition duration-200 hover:border-blue-200 hover:shadow-sm">

                <div class="mb-5 flex min-w-0 items-center gap-3">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center overflow-hidden rounded-full bg-blue-50 text-lg font-semibold text-blue-700">
                        @if($member->avatar)
                            <img
                                src="{{ asset('storage/' . $member->avatar) }}"
                                alt="{{ $member->name }}"
                                class="h-full w-full object-cover"
                                loading="lazy"
                            >
                        @else
                            {{ mb_substr($member->name ?? 'U', 0, 1) }}
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <h3 class="truncate font-semibold text-slate-900">
                            {{ $member->name }}
                        </h3>

                        <p class="truncate text-sm text-slate-500" dir="ltr">
                            {{ $member->email }}
                        </p>
                    </div>

                    @can('editMember', [$ownedProjects->first(), $member])
                        @if($ownedProjects->isNotEmpty())
                            <a
                                href="{{ route('teams.edit', $member->username) }}"
                                class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600"
                                title="ویرایش کاربر"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                          d="M16.862 3.487a2.1 2.1 0 013.65 2.1L8.2 17.9l-4.2.8.8-4.2L16.862 3.487zM15 5l4 4" />
                                </svg>
                            </a>
                        @endif
                    @endcan
                </div>

                <div class="mb-5 flex items-center justify-between gap-3">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                        همکار
                    </span>

                    <span class="text-xs text-slate-500">
                        {{ $member->shared_projects_count }}
                        پروژه مشترک
                    </span>
                </div>

                @if(isset($ownedProjects) && $ownedProjects->isNotEmpty())
                    <form
                        action="{{ route('teams.addToProject', $member) }}"
                        method="POST"
                        class="space-y-2"
                    >
                        @csrf

                        <label
                            for="project-{{ $member->id }}"
                            class="block text-xs font-medium text-slate-600"
                        >
                            اضافه کردن به پروژه
                        </label>

                        <select
                            id="project-{{ $member->id }}"
                            name="project_id"
                            required
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="">انتخاب پروژه</option>

                            @foreach($ownedProjects as $project)
                                <option value="{{ $project->id }}">
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-slate-100 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-blue-600 hover:text-white"
                        >
                            اضافه به پروژه
                        </button>
                    </form>
                @else
                    <div class="rounded-xl bg-slate-50 px-3 py-3 text-center text-xs leading-6 text-slate-500">
                        برای اضافه‌کردن اعضا، ابتدا یک پروژه ایجاد کنید یا مالک یک پروژه باشید.
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-slate-200 bg-white px-6 py-16 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                              d="M17 20h5v-2a3 3 0 00-5.2-2M17 20H7m10 0v-2c0-.7.2-1.4.6-2M7 20H2v-2a3 3 0 015.2-2M7 20v-2c0-.7.2-1.4.6-2m0 0a5 5 0 019.8 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>

                @if(request()->filled('search'))
                    <p class="text-sm font-semibold text-slate-900">
                        نتیجه‌ای پیدا نشد
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        نام یا ایمیل دیگری را جستجو کنید.
                    </p>

                    <a
                        href="{{ route('team.index') }}"
                        class="mt-4 inline-flex rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-200"
                    >
                        نمایش همه اعضا
                    </a>
                @else
                    <p class="text-sm font-semibold text-slate-900">
                        هنوز هم‌تیمی‌ای ندارید
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        از بخش اعضای هر پروژه، افراد را دعوت کنید.
                    </p>

                    <a
                        href="{{ route('projects.index') }}"
                        class="mt-4 inline-flex rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700"
                    >
                        مشاهده پروژه‌ها
                    </a>
                @endif
            </div>
        @endforelse
    </div>
</x-dashboard-layout>