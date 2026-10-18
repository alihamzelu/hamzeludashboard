<head>
    <title>پروژه ها | مدیریت پروژه</title>
</head>
<x-dashboard-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">پروژه‌ها</h1>
            <p class="text-slate-500 text-sm mt-1">مدیریت و پیگیری همه پروژه‌ها</p>
        </div>
        <a href="{{ route('projects.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            پروژه جدید
        </a>
    </div>

    @php
      $filterClass = fn ($active) => $active
        ? 'bg-blue-600 text-white'
        : 'bg-white border border-slate-200 text-slate-600 hover:border-blue-200 hover:text-blue-700';
    @endphp

    <div class="flex flex-wrap items-center gap-2 mb-6">
        <a href="{{ route('projects.index') }}" class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $filterClass(!request('status')) }}">همه</a>
        <a href="{{ route('projects.index', ['status' => 'planning']) }}" class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $filterClass(request('status') === 'planning') }}">برنامه‌ریزی</a>
        <a href="{{ route('projects.index', ['status' => 'active']) }}" class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $filterClass(request('status') === 'active') }}">فعال</a>
        <a href="{{ route('projects.index', ['status' => 'completed']) }}" class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $filterClass(request('status') === 'completed') }}">تکمیل‌شده</a>
        <a href="{{ route('projects.index', ['status' => 'archived']) }}" class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $filterClass(request('status') === 'archived') }}">آرشیو</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse ($projects as $project)
        @php
          $statusClasses = [
            'planning' => 'bg-slate-100 text-slate-600',
            'active' => 'bg-emerald-50 text-emerald-700',
            'completed' => 'bg-blue-50 text-blue-700',
            'archived' => 'bg-orange-50 text-orange-700',
          ];
          $statusLabels = [
            'planning' => 'برنامه‌ریزی',
            'active' => 'فعال',
            'completed' => 'تکمیل‌شده',
            'archived' => 'آرشیو',
          ];
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:border-blue-200 transition duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            @if($project->user_id === auth()->id())
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700">مال من</span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">
                                    اشتراک‌گذاری شده توسط {{ $project->owner->name ?? 'شخصی' }}
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('projects.show', $project) }}">
                            <h3 class="font-semibold text-slate-900 hover:text-blue-600 transition">{{ $project->name }}</h3>
                        </a>
                        <p class="text-xs text-slate-400 mt-1">
                            {{ $project->end_date ? verta($project->end_date)->format('Y/m/d') : '—' }}
                        </p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClasses[$project->status] ?? 'bg-slate-100 text-slate-600' }}">
                        {{ $statusLabels[$project->status] ?? $project->status }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 mb-4 line-clamp-2">{{ $project->description ?? 'بدون توضیحات' }}</p>
            </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full overflow-hidden bg-blue-50 text-blue-700 flex items-center justify-center text-xs font-medium flex-shrink-0">
                    @if($project->owner?->avatar)
                        <img
                            src="{{ asset('storage/' . $project->owner->avatar) }}"
                            alt="{{ $project->owner->name }}"
                            class="w-full h-full object-cover"
                        >
                    @else
                        {{ mb_substr($project->owner->name ?? 'U', 0, 1) }}
                    @endif
                </div>

                <span class="text-xs text-slate-500 truncate max-w-[120px]">
                    {{ $project->user_id === auth()->id() ? 'شما' : ($project->owner->name ?? '') }}
                </span>
            </div>

            <a href="{{ route('projects.show', $project) }}"
            class="text-xs text-blue-600 hover:text-blue-700 font-medium transition">
                مشاهده
            </a>
        </div>
        </div>
        @empty
        <div class="col-span-full bg-white border border-slate-200 rounded-2xl p-12 text-center">
            <h3 class="text-slate-900 font-medium mb-1">پروژه‌ای پیدا نشد</h3>
            <p class="text-sm text-slate-500">با این فیلتر پروژه‌ای ندارید.</p>
        </div>
        @endforelse
    </div>
</x-dashboard-layout>