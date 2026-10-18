<head>
    <title>مدیریت پروژه | اعلان ها </title>
</head>
<x-dashboard-layout>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-3">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">اعلان‌ها</h1>

            @php
                $unreadCount = auth()->user()->unreadNotifications()->count();
            @endphp

            @if($unreadCount > 0)
                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-600 text-white">
                    {{ $unreadCount }} خوانده‌نشده
                </span>
            @endif
        </div>

        @if($unreadCount > 0)
            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                @method('PATCH')

                <button type="submit"
                    class="px-4 py-2 border border-slate-200 text-sm font-medium rounded-xl hover:border-blue-200 hover:text-blue-700 transition flex items-center gap-2 text-slate-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    علامت‌گذاری همه به‌عنوان خوانده‌شده
                </button>
            </form>
        @endif
    </div>

    <div class="space-y-3">
        @forelse($notifications as $notification)
            @php
                $type = $notification->data['type'] ?? '';
                $url = $notification->data['url'] ?? null;

                $title = $notification->data['title'] ?? 'اعلان جدید';

                $titleTranslations = [
                    'New message' => 'پیام جدید',
                    'Event reminder' => 'یادآوری رویداد',
                    'Task assigned' => 'وظیفه جدید',
                    'Task status changed' => 'تغییر وضعیت وظیفه',
                    'Project invitation' => 'دعوت به پروژه',
                ];

                $title = $titleTranslations[$title] ?? $title;

                $message = $notification->data['message'] ?? 'اعلان جدید';

                $messageTranslations = [
                    'You have a new message from ' => 'پیام جدیدی از طرف ',
                    'You have been assigned to task ' => 'وظیفه‌ای به شما واگذار شده است: ',
                    'Task status changed to ' => 'وضعیت وظیفه تغییر کرد به ',
                    'You have been invited to join project ' => 'به پروژه زیر دعوت شده‌اید: ',
                ];

                foreach ($messageTranslations as $english => $persian) {
                    if (str_starts_with($message, $english)) {
                        $message = $persian . substr($message, strlen($english));
                        break;
                    }
                }

                $statusTranslations = [
                    'planning' => 'در انتظار شروع',
                    'active' => 'در حال انجام',
                    'completed' => 'تکمیل‌شده',
                    'archived' => 'بایگانی‌شده',
                    'pending' => 'در انتظار پاسخ',
                    'accepted' => 'پذیرفته‌شده',
                    'rejected' => 'ردشده',
                ];
            @endphp

            <div class="bg-white border {{ $notification->read_at ? 'border-slate-200' : 'border-blue-200' }} rounded-2xl p-5 transition hover:border-blue-200">
                <div class="flex items-start gap-4">

                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ $notification->read_at ? 'bg-slate-50 text-slate-400' : 'bg-blue-600 text-white' }}">
                        @if($type === 'project_invitation')
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        @elseif(in_array($type, ['task_assigned', 'task_status_changed']))
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        @elseif($type === 'new_message')
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z" />
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0 pt-0.5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">

                                <p class="text-xs font-medium text-slate-400 mb-0.5">
                                    {{ $title }}
                                </p>

                                <p class="text-sm {{ $notification->read_at ? 'text-slate-600' : 'text-slate-900 font-semibold' }}">
                                    {{ $message }}
                                </p>

                                <p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>

                                    {{ $notification->created_at->locale('fa')->diffForHumans() }}
                                </p>

                                @if($type === 'project_invitation')
                                    @php
                                        $invitationId = $notification->data['invitation_id'] ?? null;

                                        $invitation = $invitationId
                                            ? \App\Models\ProjectInvitation::find($invitationId)
                                            : null;
                                    @endphp

                                    @if($invitation)
                                        @if($invitation->status === 'pending')
                                            <div class="mt-4 flex items-center gap-2.5">
                                                <form action="{{ route('invitations.accept', $invitation->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit"
                                                        class="px-3.5 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-xl hover:bg-blue-700 transition">
                                                        پذیرش دعوت
                                                    </button>
                                                </form>

                                                <form action="{{ route('invitations.reject', $invitation->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit"
                                                        class="px-3.5 py-1.5 bg-white border border-slate-200 text-slate-600 text-xs font-medium rounded-xl hover:bg-slate-50 transition">
                                                        رد دعوت
                                                    </button>
                                                </form>
                                            </div>

                                        @elseif($invitation->status === 'accepted')
                                            <div class="mt-4">
                                                <span class="inline-flex px-3.5 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-medium rounded-xl">
                                                    دعوت پذیرفته شده است
                                                </span>
                                            </div>

                                        @elseif($invitation->status === 'rejected')
                                            <div class="mt-4">
                                                <span class="inline-flex px-3.5 py-1.5 bg-slate-50 text-slate-500 text-xs font-medium rounded-xl">
                                                    دعوت رد شده است
                                                </span>
                                            </div>
                                        @endif
                                    @endif
                                @endif

                                @if($url && in_array($type, ['task_assigned', 'task_status_changed']))
                                    <div class="mt-3">
                                        <a href="{{ route('notifications.open', $notification->id) }}"
                                           class="text-xs font-medium text-blue-600 hover:text-blue-700 transition">
                                            مشاهده وظیفه
                                        </a>
                                    </div>
                                @elseif($url && $type === 'new_message')
                                    <div class="mt-3">
                                        <a href="{{ route('notifications.open', $notification->id) }}"
                                           class="text-xs font-medium text-blue-600 hover:text-blue-700 transition">
                                            مشاهده پیام
                                        </a>
                                    </div>
                                @elseif($url && $type === 'event_upcoming_reminder')
                                    <div class="mt-3">
                                        <a href="{{ route('notifications.open', $notification->id) }}"
                                           class="text-xs font-medium text-blue-600 hover:text-blue-700 transition">
                                            مشاهده رویداد
                                        </a>
                                    </div>
                                @endif

                            </div>

                            @if(!$notification->read_at)
                                <span class="w-2.5 h-2.5 bg-blue-600 rounded-full mt-1.5 flex-shrink-0"></span>
                            @endif
                        </div>
                    </div>

                    @if(!$notification->read_at)
                        <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="flex-shrink-0">
                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition"
                                title="علامت‌گذاری به‌عنوان خوانده‌شده">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </form>
                    @endif

                </div>
            </div>

        @empty
            <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center">
                <h3 class="text-slate-900 font-medium mb-1">همه‌چیز به‌روز است</h3>
                <p class="text-sm text-slate-500">در حال حاضر اعلانی ندارید.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
    @endif

</x-dashboard-layout>