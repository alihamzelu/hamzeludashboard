<head>
    <title>وظیفه {{ \Illuminate\Support\Str::limit($task->title, 30) }} | مدیریت پروژه</title>
</head>
<x-dashboard-layout>
    @php
        $priorityClasses = [
            'high' => 'bg-red-50 text-red-700 border-red-100',
            'medium' => 'bg-orange-50 text-orange-700 border-orange-100',
            'low' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
        ];
        $priorityLabels = ['high' => 'بالا', 'medium' => 'متوسط', 'low' => 'پایین'];
        $statusClasses = [
            'todo' => 'bg-slate-100 text-slate-600 border-slate-200',
            'in_progress' => 'bg-blue-50 text-blue-700 border-blue-100',
            'review' => 'bg-violet-50 text-violet-700 border-violet-100',
            'done' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
        ];
        $statusLabels = [
            'todo' => 'انجام نشده',
            'in_progress' => 'در حال انجام',
            'review' => 'بازبینی',
            'done' => 'انجام‌شده',
        ];
    @endphp

    <div dir="rtl" class="space-y-4">
        <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-blue-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            بازگشت به وظایف
        </a>

        <section class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-4">
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 break-words">{{ $task->title }}</h1>
                    <a href="{{ route('projects.show', ['project' => $task->project, 'tab' => 'tasks']) }}" class="text-sm text-slate-500 hover:text-blue-600 mt-1 inline-block">
                        {{ $task->project?->name ?? 'بدون پروژه' }}
                    </a>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full border text-xs {{ $priorityClasses[$task->priority] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                        {{ $priorityLabels[$task->priority] ?? $task->priority }}
                    </span>
                    <span class="px-3 py-1 rounded-full border text-xs {{ $statusClasses[$task->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                        {{ $statusLabels[$task->status] ?? $task->status }}
                    </span>
                </div>
            </div>
            <p class="text-sm text-slate-600 leading-7 whitespace-pre-line">{{ $task->description ?: 'توضیحی ثبت نشده است.' }}</p>
        </section>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">مسئول</p>
                <div class="flex items-center gap-2 mt-2">
                    @if($task->assignedUser)
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm">
                            {{ mb_substr($task->assignedUser->name, 0, 1) }}
                        </div>
                        <span class="text-sm font-medium truncate">{{ $task->assignedUser->name }}</span>
                    @else
                        <span class="text-sm text-slate-400">بدون مسئول</span>
                    @endif
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">تاریخ سررسید</p>
                <p class="text-base font-semibold mt-2">{{ $task->due_date ? $task->due_date->format('Y/m/d') : '—' }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">اولویت</p>
                <p class="text-base font-semibold mt-2">{{ $priorityLabels[$task->priority] ?? '—' }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">وضعیت</p>
                <p class="text-base font-semibold mt-2">{{ $statusLabels[$task->status] ?? '—' }}</p>
            </div>
        </div>

        <section class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center text-sm font-semibold">
                    {{ mb_substr($task->project?->owner?->name ?? 'گ', 0, 1) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">گفتگوی وظیفه</p>
                    <p class="text-xs text-slate-500">{{ $task->project?->name ?? 'بدون پروژه' }}</p>
                </div>
            </div>

            <div id="taskMessagesBox" class="h-[55vh] min-h-[320px] max-h-[650px] overflow-y-auto p-3 sm:p-4 space-y-3 bg-slate-50/70" dir="rtl">
                @forelse($messages as $message)
                    @php
                        $mine = (int) $message->user_id === (int) auth()->id();
                        $attachments = $message->attachments ?? collect();
                    @endphp

                    <div class="flex {{ $mine ? 'justify-start' : 'justify-end' }} items-end gap-2">
                        @unless($mine)
                            <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs flex-shrink-0">
                                {{ mb_substr($message->user?->name ?? '?', 0, 1) }}
                            </div>
                        @endunless

                        <div class="min-w-0 max-w-[88%] sm:max-w-[75%]">
                            @unless($mine)
                                <p class="text-[10px] text-slate-500 mb-1 px-1">{{ $message->user?->name ?? 'کاربر حذف‌شده' }}</p>
                            @endunless

                            @if($message->body)
                                <div @class([
                                    'w-fit max-w-full px-3 py-2 text-sm leading-6 whitespace-pre-wrap break-words rounded-2xl',
                                    'bg-blue-600 text-white rounded-br-sm' => $mine,
                                    'bg-white border border-slate-200 text-slate-800 rounded-bl-sm' => !$mine,
                                ])>{{ $message->body }}</div>
                            @endif

                            @if($attachments->isNotEmpty())
                                <div class="mt-2 space-y-2">
                                    @foreach($attachments as $attachment)
                                        @php
                                            $mime = $attachment->mime_type ?? 'application/octet-stream';
                                            $url = \Illuminate\Support\Facades\Storage::disk('public')->url($attachment->path);
                                            $isImage = str_starts_with($mime, 'image/');
                                            $isVideo = str_starts_with($mime, 'video/');
                                            $size = (int) ($attachment->size ?? 0);
                                            $sizeLabel = $size >= 1048576
                                                ? number_format($size / 1048576, 1) . ' MB'
                                                : ($size >= 1024 ? number_format($size / 1024, 1) . ' KB' : $size . ' B');
                                        @endphp

                                        @if($isImage)
                                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer">
                                                    <img src="{{ $url }}" alt="{{ $attachment->original_name }}" loading="lazy" class="block max-h-64 max-w-full object-contain">
                                                </a>
                                                <div class="px-3 py-2 flex items-center justify-between gap-2">
                                                    <span class="text-xs text-slate-600 truncate">{{ $attachment->original_name }}</span>
                                                    <a href="{{ $url }}" download="{{ $attachment->original_name }}" class="text-xs text-blue-600 flex-shrink-0">دانلود</a>
                                                </div>
                                            </div>
                                        @elseif($isVideo)
                                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-900">
                                                <video controls preload="metadata" class="block max-w-full max-h-72">
                                                    <source src="{{ $url }}" type="{{ $mime }}">
                                                    مرورگر شما امکان پخش این ویدئو را ندارد.
                                                </video>
                                                <div class="px-3 py-2 bg-white flex items-center justify-between gap-2">
                                                    <span class="text-xs text-slate-600 truncate">{{ $attachment->original_name }}</span>
                                                    <a href="{{ $url }}" download="{{ $attachment->original_name }}" class="text-xs text-blue-600 flex-shrink-0">دانلود</a>
                                                </div>
                                            </div>
                                        @else
                                            <a href="{{ $url }}" download="{{ $attachment->original_name }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-white hover:border-blue-300 transition">
                                                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6M8 13h8M8 17h8"/>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-medium text-slate-700 break-all">{{ $attachment->original_name }}</p>
                                                    <p class="text-[10px] text-slate-400 mt-1">{{ $sizeLabel }}</p>
                                                </div>
                                                <span class="text-xs text-blue-600 flex-shrink-0">دانلود</span>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            @if($attachments->isEmpty() && $message->attachment_path)
                                @php
                                    $legacyUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($message->attachment_path);
                                    $legacyMime = $message->attachment_mime ?? '';
                                @endphp
                                <div class="mt-2">
                                    @if(str_starts_with($legacyMime, 'image/'))
                                        <a href="{{ $legacyUrl }}" target="_blank" rel="noopener noreferrer">
                                            <img src="{{ $legacyUrl }}" alt="{{ $message->attachment_name }}" class="max-h-64 max-w-full rounded-xl border border-slate-200">
                                        </a>
                                    @elseif(str_starts_with($legacyMime, 'video/'))
                                        <video controls class="max-w-full max-h-72 rounded-xl">
                                            <source src="{{ $legacyUrl }}" type="{{ $legacyMime }}">
                                        </video>
                                    @else
                                        <a href="{{ $legacyUrl }}" download="{{ $message->attachment_name }}" class="block p-3 rounded-xl border border-slate-200 bg-white text-sm text-blue-600 break-all">
                                            📎 {{ $message->attachment_name ?? 'دانلود فایل پیوست' }}
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <p class="text-[10px] text-slate-400 mt-1 px-1">{{ verta($message->created_at)->format('Y/m/d · H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="min-h-[250px] flex items-center justify-center text-center">
                        <div>
                            <div class="text-3xl mb-2">💬</div>
                            <p class="text-sm text-slate-600">هنوز پیامی ارسال نشده</p>
                            <p class="text-xs text-slate-400 mt-1">اولین پیام را ارسال کنید.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <form id="taskMessageForm" action="{{ route('tasks.messages.store', $task) }}" method="POST" enctype="multipart/form-data" class="p-3 border-t border-slate-200 bg-white">
                @csrf

                <div id="selectedFilesPanel" class="hidden mb-2 rounded-xl bg-slate-50 border border-slate-200 p-2">
                    <div class="flex items-center justify-between gap-2">
                        <span id="fileCount" class="text-xs text-slate-600"></span>
                        <button type="button" id="clearFiles" class="text-xs text-red-500">حذف همه</button>
                    </div>
                    <div id="fileList" class="mt-2 space-y-1"></div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="file" name="attachments[]" id="taskAttachments" multiple accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar,.7z" class="hidden">

                    <label for="taskAttachments" title="انتخاب فایل" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 hover:bg-blue-50 hover:text-blue-600 flex items-center justify-center cursor-pointer flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                        </svg>
                    </label>

                    <textarea name="body" id="messageBody" rows="1" maxlength="5000" placeholder="پیام بنویسید..." class="flex-1 min-w-0 resize-none px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('body') }}</textarea>

                    <button type="submit" id="sendMessageBtn" disabled class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed flex-shrink-0">
                        <svg id="sendIcon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m22 2-7 20-4-9-9-4Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 2 11 13"/>
                        </svg>
                        <svg id="loadingIcon" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/>
                        </svg>
                    </button>
                </div>

                <div id="dropZone" class="mt-2 rounded-lg border border-dashed border-transparent px-2 py-1.5 text-center text-[11px] text-slate-400 hover:border-slate-200 hover:bg-slate-50">
                    فایل‌ها را اینجا رها کنید یا روی 📎 بزنید؛ حداکثر ۱۰ فایل، هر فایل تا ۵۰ مگابایت
                </div>

                <div id="largeFilePanel" class="hidden mt-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L7 9m5-5 5 5M5 14v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-5"/>
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-amber-900">حجم فایل بیشتر از حد مجاز است</p>
                            <p id="largeFileName" class="mt-1 text-xs text-amber-800 break-all"></p>
                            <p class="mt-2 text-xs leading-6 text-amber-800">فایل را در یک سرویس اشتراک‌گذاری آپلود کنید و لینک دانلود آن را اینجا قرار دهید.</p>


                            <div class="mt-3">
                                <label for="externalFileLink" class="block text-xs font-medium text-amber-900 mb-1.5">لینک فایل آپلودشده</label>
                                <div class="flex gap-2">
                                    <input type="url" id="externalFileLink" dir="ltr" placeholder="https://..." class="min-w-0 flex-1 rounded-lg border border-amber-200 bg-white px-3 py-2 text-xs focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                    <button type="button" id="addExternalLink" class="flex-shrink-0 rounded-lg bg-slate-900 px-3 py-2 text-xs text-white hover:bg-slate-700">افزودن لینک</button>
                                </div>
                                <p id="externalLinkError" class="hidden mt-2 text-xs text-red-600"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="uploadProgressPanel" class="hidden mt-3 rounded-xl border border-blue-100 bg-blue-50 p-3">
                    <div class="flex items-center justify-between gap-3">
                        <span id="uploadProgressTitle" class="text-xs font-medium text-slate-700">در حال ارسال پیام و فایل‌ها...</span>
                        <span id="uploadProgressPercent" class="text-xs font-semibold text-blue-700 tabular-nums">0%</span>
                    </div>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-blue-100">
                        <div id="uploadProgressBar" class="h-full w-0 rounded-full bg-blue-600 transition-all duration-150" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
                    </div>
                    <p id="uploadProgressDetails" class="mt-2 text-[10px] text-slate-500">لطفاً تا پایان ارسال صفحه را نبندید.</p>
                </div>

                <p class="text-[10px] text-slate-400 mt-1 text-center">عکس، ویدئو، PDF، Word، Excel، PowerPoint و فایل فشرده</p>

                @if($errors->has('body'))
                    <p class="mt-2 text-xs text-red-500">{{ $errors->first('body') }}</p>
                @endif
                @if($errors->has('attachments'))
                    <p class="mt-2 text-xs text-red-500">{{ $errors->first('attachments') }}</p>
                @endif
                @if($errors->has('attachments.*'))
                    <p class="mt-2 text-xs text-red-500">{{ $errors->first('attachments.*') }}</p>
                @endif
            </form>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const box = document.getElementById('taskMessagesBox');
            const form = document.getElementById('taskMessageForm');
            const input = document.getElementById('taskAttachments');
            const fileList = document.getElementById('fileList');
            const filesPanel = document.getElementById('selectedFilesPanel');
            const fileCount = document.getElementById('fileCount');
            const clearFiles = document.getElementById('clearFiles');
            const body = document.getElementById('messageBody');
            const sendButton = document.getElementById('sendMessageBtn');
            const sendIcon = document.getElementById('sendIcon');
            const loadingIcon = document.getElementById('loadingIcon');
            const dropZone = document.getElementById('dropZone');

            const largeFilePanel = document.getElementById('largeFilePanel');
            const largeFileName = document.getElementById('largeFileName');
            const externalFileLink = document.getElementById('externalFileLink');
            const addExternalLink = document.getElementById('addExternalLink');
            const externalLinkError = document.getElementById('externalLinkError');

            const progressPanel = document.getElementById('uploadProgressPanel');
            const progressBar = document.getElementById('uploadProgressBar');
            const progressPercent = document.getElementById('uploadProgressPercent');
            const progressTitle = document.getElementById('uploadProgressTitle');
            const progressDetails = document.getElementById('uploadProgressDetails');

            const MAX_FILE_SIZE = 50 * 1024 * 1024;
            const MAX_FILES = 10;

            let selected = [];
            let uploading = false;

            if (box) box.scrollTop = box.scrollHeight;

            const formatSize = bytes => {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / 1048576).toFixed(1) + ' MB';
            };

            const escapeHtml = value => {
                const element = document.createElement('span');
                element.textContent = value;
                return element.innerHTML;
            };

            function syncInput() {
                const transfer = new DataTransfer();
                selected.forEach(file => transfer.items.add(file));
                input.files = transfer.files;
            }

            function updateButton() {
                sendButton.disabled = uploading || (!body.value.trim() && selected.length === 0);
            }

            function renderFiles() {
                fileList.innerHTML = '';
                filesPanel.classList.toggle('hidden', selected.length === 0);
                fileCount.textContent = selected.length.toLocaleString('fa-IR') + ' فایل انتخاب شده';

                selected.forEach((file, index) => {
                    const row = document.createElement('div');
                    row.className = 'flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-2 py-1.5';
                    row.innerHTML = `
                        <span class="text-blue-600">📎</span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs text-slate-700 truncate">${escapeHtml(file.name)}</p>
                            <p class="text-[10px] text-slate-400">${formatSize(file.size)}</p>
                        </div>
                        <button type="button" data-index="${index}" class="remove-file text-red-400 hover:text-red-600 px-2" aria-label="حذف فایل">×</button>
                    `;
                    fileList.appendChild(row);
                });

                updateButton();
            }

            function addFiles(files) {
                const incoming = Array.from(files || []);
                const oversizedFiles = [];
                let limitReached = false;

                for (const file of incoming) {
                    if (file.size > MAX_FILE_SIZE) {
                        oversizedFiles.push(file);
                        continue;
                    }

                    const exists = selected.some(old =>
                        old.name === file.name &&
                        old.size === file.size &&
                        old.lastModified === file.lastModified
                    );

                    if (exists) continue;

                    if (selected.length >= MAX_FILES) {
                        limitReached = true;
                        continue;
                    }

                    selected.push(file);
                }

                if (oversizedFiles.length > 0) {
                    largeFilePanel.classList.remove('hidden');
                    largeFileName.textContent = oversizedFiles
                        .map(file => `${file.name} (${formatSize(file.size)})`)
                        .join('، ');
                }

                if (limitReached) {
                    alert('حداکثر می‌توانید ۱۰ فایل انتخاب کنید.');
                }

                syncInput();
                renderFiles();
            }

            input.addEventListener('change', () => {
                const files = Array.from(input.files || []);
                input.value = '';
                addFiles(files);
            });

            fileList.addEventListener('click', event => {
                const button = event.target.closest('.remove-file');
                if (!button) return;
                selected.splice(Number(button.dataset.index), 1);
                syncInput();
                renderFiles();
            });

            clearFiles.addEventListener('click', () => {
                selected = [];
                syncInput();
                renderFiles();
            });

            body.addEventListener('input', updateButton);

            body.addEventListener('keydown', event => {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    if (!sendButton.disabled) form.requestSubmit();
                }
            });

            addExternalLink.addEventListener('click', () => {
                const url = externalFileLink.value.trim();
                externalLinkError.classList.add('hidden');
                externalLinkError.textContent = '';

                let parsedUrl;

                try {
                    parsedUrl = new URL(url);
                } catch {
                    externalLinkError.textContent = 'لطفاً یک لینک معتبر وارد کنید.';
                    externalLinkError.classList.remove('hidden');
                    return;
                }

                if (!['https:', 'http:'].includes(parsedUrl.protocol)) {
                    externalLinkError.textContent = 'لطفاً یک لینک معتبر HTTP یا HTTPS وارد کنید.';
                    externalLinkError.classList.remove('hidden');
                    return;
                }

                const linkText = `لینک فایل: ${parsedUrl.href}`;

                if (!body.value.includes(parsedUrl.href)) {
                    body.value = body.value.trim()
                        ? `${body.value.trim()}\n${linkText}`
                        : linkText;
                }

                updateButton();
                body.focus();
                addExternalLink.textContent = 'اضافه شد';
                addExternalLink.classList.replace('bg-slate-900', 'bg-emerald-600');

                setTimeout(() => {
                    addExternalLink.textContent = 'افزودن لینک';
                    addExternalLink.classList.replace('bg-emerald-600', 'bg-slate-900');
                }, 1500);
            });

            externalFileLink.addEventListener('keydown', event => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    addExternalLink.click();
                }
            });

            ['dragenter', 'dragover'].forEach(name => {
                dropZone.addEventListener(name, event => {
                    event.preventDefault();
                    dropZone.classList.add('border-blue-300', 'bg-blue-50');
                });
            });

            ['dragleave', 'drop'].forEach(name => {
                dropZone.addEventListener(name, event => {
                    event.preventDefault();
                    dropZone.classList.remove('border-blue-300', 'bg-blue-50');
                });
            });

            dropZone.addEventListener('drop', event => {
                addFiles(event.dataTransfer.files);
            });

            form.addEventListener('submit', event => {
                event.preventDefault();

                if (uploading) return;

                if (!body.value.trim() && selected.length === 0) {
                    body.focus();
                    return;
                }

                uploading = true;
                updateButton();

                progressPanel.classList.remove('hidden');
                progressTitle.classList.remove('text-red-600');
                progressTitle.textContent = 'در حال ارسال پیام و فایل‌ها...';
                progressDetails.textContent = 'لطفاً تا پایان ارسال صفحه را نبندید.';
                progressBar.style.width = '0%';
                progressBar.setAttribute('aria-valuenow', 0);
                progressPercent.textContent = '0%';

                sendIcon.classList.add('hidden');
                loadingIcon.classList.remove('hidden');

                const xhr = new XMLHttpRequest();

                xhr.open('POST', form.action, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Accept', 'text/html, application/json');

                xhr.upload.addEventListener('progress', event => {
                    if (!event.lengthComputable) {
                        progressTitle.textContent = 'در حال آپلود فایل‌ها...';
                        return;
                    }

                    const percent = Math.min(100, Math.round((event.loaded / event.total) * 100));

                    progressBar.style.width = `${percent}%`;
                    progressBar.setAttribute('aria-valuenow', percent);
                    progressPercent.textContent = `${percent}%`;
                    progressDetails.textContent = `${formatSize(event.loaded)} از ${formatSize(event.total)}`;

                    if (percent >= 100) {
                        progressTitle.textContent = 'آپلود انجام شد؛ در حال ثبت پیام...';
                        progressDetails.textContent = 'در انتظار پاسخ سرور...';
                    }
                });

                function resetSendingState() {
                    uploading = false;
                    sendIcon.classList.remove('hidden');
                    loadingIcon.classList.add('hidden');
                    updateButton();
                }

                xhr.addEventListener('load', () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        progressBar.style.width = '100%';
                        progressBar.setAttribute('aria-valuenow', 100);
                        progressPercent.textContent = '100%';
                        progressTitle.textContent = 'ارسال تکمیل شد';
                        progressDetails.textContent = 'در حال باز کردن صفحه...';

                        window.location.assign(xhr.responseURL || window.location.href);
                        return;
                    }

                    resetSendingState();
                    progressTitle.textContent = 'ارسال ناموفق بود';
                    progressTitle.classList.add('text-red-600');

                    if (xhr.status === 413) {
                        progressDetails.textContent = 'حجم فایل از محدودیت سرور بیشتر است.';
                    } else if (xhr.status === 419) {
                        progressDetails.textContent = 'نشست شما منقضی شده است. صفحه را تازه‌سازی کنید.';
                    } else if (xhr.status === 422) {
                        progressDetails.textContent = 'اطلاعات یا فایل‌های ارسالی معتبر نیستند؛ حجم و نوع فایل را بررسی کنید.';
                    } else {
                        progressDetails.textContent = `خطای سرور (${xhr.status}). دوباره تلاش کنید.`;
                    }
                });

                xhr.addEventListener('error', () => {
                    resetSendingState();
                    progressTitle.textContent = 'خطا در اتصال';
                    progressTitle.classList.add('text-red-600');
                    progressDetails.textContent = 'اتصال اینترنت یا سرور را بررسی کنید و دوباره تلاش کنید.';
                });

                xhr.addEventListener('abort', () => {
                    resetSendingState();
                    progressTitle.textContent = 'ارسال لغو شد';
                    progressTitle.classList.add('text-red-600');
                    progressDetails.textContent = 'می‌توانید دوباره تلاش کنید.';
                });

                xhr.send(new FormData(form));
            });

            renderFiles();
        });
    </script>
</x-dashboard-layout>