<x-dashboard-layout>
    <div class="max-w-3xl">
        <div class="mb-8">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">دعوت‌ها</h1>
            <p class="text-slate-500 text-sm mt-1">دعوت‌های پروژه که هنوز پاسخ ندادی</p>
        </div>

        @forelse($invitations as $invitation)
            <div class="bg-white border border-slate-200 rounded-2xl p-5 mb-4 hover:border-blue-200 transition">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="font-semibold text-slate-900">
                            {{ $invitation->project->name }}
                        </h2>
                        <p class="text-sm text-slate-500 mt-1">
                            دعوت‌شده توسط {{ $invitation->inviter->name }}
                        </p>
                        @if($invitation->expires_at)
                            <p class="text-xs text-slate-400 mt-2">
                                انقضا {{ $invitation->expires_at->diffForHumans() }}
                            </p>
                        @endif
                    </div>

                    <div class="flex gap-2 flex-shrink-0">
                        <form method="POST" action="{{ route('invitations.accept', $invitation) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
                                پذیرش
                            </button>
                        </form>
                        <form method="POST" action="{{ route('invitations.reject', $invitation) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">
                                رد کردن
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center">
                <p class="text-sm font-medium text-slate-900">دعوت در انتظاری وجود ندارد</p>
                <p class="text-sm text-slate-500 mt-1">وقتی کسی دعوتت کند، اینجا دیده می‌شود.</p>
            </div>
        @endforelse
    </div>
</x-dashboard-layout>