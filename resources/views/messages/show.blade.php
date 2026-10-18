<x-dashboard-layout>
  @php $other = $conversation->users->firstWhere('id', '!=', auth()->id()); @endphp

  <div class="mb-4">
    <a href="{{ route('messages.index') }}" class="text-sm text-slate-500 hover:text-blue-600 transition">بازگشت</a>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 flex flex-col h-[calc(100vh-12rem)] min-h-[480px] overflow-visible">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3 flex-shrink-0">
      <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center text-sm font-semibold">
        {{ strtoupper(substr($other?->name ?? '?', 0, 1)) }}
      </div>
      <div>
        <p class="text-sm font-semibold text-slate-900">{{ $other?->name }}</p>
        <p class="text-xs text-slate-500">{{ $other?->email }}</p>
      </div>
    </div>

    <div class="flex-1 overflow-y-auto p-5 space-y-4 bg-slate-50/50" id="messagesBox">
      @foreach($messages as $message)
        @php $mine = (int) $message->user_id === (int) auth()->id(); @endphp
        <div class="flex {{ $mine ? 'justify-end' : 'items-end gap-2' }}">
          @unless($mine)
            <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center text-[10px] font-medium flex-shrink-0">
              {{ strtoupper(substr($message->user->name ?? '?', 0, 1)) }}
            </div>
          @endunless

          <div class="max-w-[75%] {{ $mine ? 'text-left' : '' }}">
            @if(($message->type ?? 'text') === 'image' && $message->attachment_path)
              <div class="inline-block text-right overflow-hidden rounded-2xl {{ $mine ? 'rounded-bl-md' : 'rounded-br-md' }} border border-slate-100 bg-white">
                <a href="{{ asset('storage/' . $message->attachment_path) }}" target="_blank">
                  <img src="{{ asset('storage/' . $message->attachment_path) }}" alt="" class="max-w-full max-h-64 object-cover block">
                </a>
                @if($message->body)
                  <p class="px-3 py-2 text-sm text-slate-800">{{ $message->body }}</p>
                @endif
              </div>
            @elseif(($message->type ?? 'text') === 'video' && $message->attachment_path)
              <div class="inline-block text-right overflow-hidden rounded-2xl {{ $mine ? 'rounded-bl-md' : 'rounded-br-md' }} border border-slate-100 bg-slate-900">
                <video controls class="max-w-full max-h-64 block">
                  <source src="{{ asset('storage/' . $message->attachment_path) }}" type="{{ $message->attachment_mime }}">
                </video>
                @if($message->body)
                  <p class="px-3 py-2 text-sm text-white bg-slate-900">{{ $message->body }}</p>
                @endif
              </div>
            @else
              <div @class([
                'inline-block px-3.5 py-2.5 text-sm rounded-2xl text-right',
                'bg-blue-600 text-white rounded-bl-md' => $mine,
                'bg-white border border-slate-200 text-slate-800 rounded-br-md' => !$mine,
              ])>
                {{ $message->body }}
              </div>
            @endif

            <p class="text-[10px] text-slate-400 mt-1 {{ $mine ? 'ml-1' : 'mr-1' }}">
              {{ $message->created_at->format('Y/m/d · H:i') }}
            </p>
          </div>
        </div>
      @endforeach
    </div>

    <form action="{{ route('messages.store', $conversation) }}" method="POST" enctype="multipart/form-data"
      class="p-4 border-t border-slate-200 bg-white flex-shrink-0 relative z-20" id="chatForm">
      @csrf

      <div id="attachChip" class="hidden items-center gap-2 px-3 py-2 mb-3 rounded-xl bg-slate-100">
        <span id="attachChipIcon" class="text-sm">تصویر</span>
        <span id="attachChipName" class="text-xs text-slate-700 truncate flex-1"></span>
        <button type="button" onclick="clearAttachment()" class="text-slate-500 hover:text-blue-600 text-sm font-medium">حذف</button>
      </div>

      <div class="flex items-center gap-2">
        <div class="relative flex-shrink-0">
          <button type="button" id="attachBtn"
            class="w-11 h-11 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition"
            title="پیوست فایل">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
            </svg>
          </button>

          <div id="attachMenu"
            class="hidden absolute bottom-14 right-0 w-56 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 overflow-hidden">
            <button type="button" data-kind="image"
              class="attach-option w-full flex items-center gap-3 px-4 py-3.5 text-sm text-slate-800 hover:bg-slate-50 text-right">
              <span class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-medium">عکس</span>
              <div>
                <p class="font-medium">تصویر</p>
                <p class="text-xs text-slate-400">JPG, PNG, WEBP</p>
              </div>
            </button>
            <button type="button" data-kind="video"
              class="attach-option w-full flex items-center gap-3 px-4 py-3.5 text-sm text-slate-800 hover:bg-slate-50 text-right border-t border-slate-100">
              <span class="w-10 h-10 rounded-full bg-violet-50 text-violet-600 flex items-center justify-center text-sm font-medium">وید</span>
              <div>
                <p class="font-medium">ویدیو</p>
                <p class="text-xs text-slate-400">MP4, MOV, WEBM</p>
              </div>
            </button>
          </div>

          <input type="file" name="attachment" id="attachmentInput" class="hidden">
        </div>

        <input type="text" name="body" id="bodyInput" placeholder="پیام بنویسید..."
          class="flex-1 min-w-0 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">

        <button type="submit"
          class="w-11 h-11 rounded-xl bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition flex-shrink-0">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>
          </svg>
        </button>
      </div>
    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const box = document.getElementById('messagesBox');
      if (box) box.scrollTop = box.scrollHeight;

      const attachBtn = document.getElementById('attachBtn');
      const attachMenu = document.getElementById('attachMenu');
      const attachmentInput = document.getElementById('attachmentInput');
      const attachChip = document.getElementById('attachChip');
      const attachChipName = document.getElementById('attachChipName');
      const attachChipIcon = document.getElementById('attachChipIcon');
      const bodyInput = document.getElementById('bodyInput');
      const chatForm = document.getElementById('chatForm');

      attachBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        attachMenu.classList.toggle('hidden');
      });

      document.querySelectorAll('.attach-option').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          const kind = btn.dataset.kind;
          attachMenu.classList.add('hidden');
          attachmentInput.accept = kind === 'image'
            ? 'image/jpeg,image/png,image/gif,image/webp'
            : 'video/mp4,video/quicktime,video/webm';
          attachmentInput.click();
        });
      });

      attachmentInput.addEventListener('change', function () {
        const file = attachmentInput.files && attachmentInput.files[0];
        if (!file) return;
        attachChipIcon.textContent = file.type.startsWith('video/') ? 'ویدیو' : 'تصویر';
        attachChipName.textContent = file.name;
        attachChip.classList.remove('hidden');
        attachChip.classList.add('flex');
      });

      window.clearAttachment = function () {
        attachmentInput.value = '';
        attachChip.classList.add('hidden');
        attachChip.classList.remove('flex');
      };

      document.addEventListener('click', function (e) {
        if (!e.target.closest('#attachBtn') && !e.target.closest('#attachMenu')) {
          attachMenu.classList.add('hidden');
        }
      });

      chatForm.addEventListener('submit', function (e) {
        const hasText = bodyInput.value.trim().length > 0;
        const hasFile = attachmentInput.files && attachmentInput.files.length > 0;
        if (!hasText && !hasFile) {
          e.preventDefault();
          bodyInput.focus();
        }
      });
    });
  </script>
</x-dashboard-layout>