<head>
    <title>پیام ها | مدیریت پروژه</title>
</head>
<x-dashboard-layout>
  <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h1 class="text-xl sm:text-2xl font-bold text-slate-900">پیام‌ها</h1>
      <p class="text-slate-500 text-sm mt-1">گفتگو با اعضای تیم</p>
    </div>

    <details class="relative">
      <summary class="list-none cursor-pointer inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        گفتگوی جدید
      </summary>

      <div class="absolute left-0 mt-2 w-80 bg-white border border-slate-200 rounded-2xl shadow-lg p-4 z-20">
        <form action="{{ route('messages.storeConversation') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
          @csrf

          <select name="user_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
            <option value="">انتخاب هم‌تیمی</option>

            @foreach($contacts as $contact)
              <option value="{{ $contact->id }}">
                {{($contact->username ?? 'نامشخص') }}
              </option>
            @endforeach
          </select>

          <textarea name="body" rows="3" placeholder="اولین پیام..."
            class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-600">{{ old('body') }}</textarea>

          <input type="file" name="attachment" accept="image/*,video/*" class="w-full text-xs text-slate-500">

          <button type="submit" class="w-full py-2 bg-blue-600 text-white text-sm rounded-xl hover:bg-blue-700 transition">
            شروع
          </button>
        </form>
      </div>
    </details>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex" style="height: calc(100vh - 11rem); min-height: 520px;">

    {{-- Conversation list --}}
    <div class="w-full {{ $conversation ? 'hidden sm:flex' : 'flex' }} sm:w-80 lg:w-96 border-l border-slate-200 flex-col">

      <div class="p-4 border-b border-slate-100">
        <div class="relative">
          <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>

          <input type="text" id="searchConversations" placeholder="جستجوی گفتگوها..."
            class="w-full pr-10 pl-4 py-2.5 rounded-xl bg-slate-100 border-0 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600"/>
        </div>
      </div>

      <div class="flex-1 overflow-y-auto">
        @forelse($conversations as $c)
          @php
            $other = $c->users->firstWhere('id', '!=', auth()->id());
            $last = $c->messages->first();
            $active = $conversation && $conversation->id === $c->id;

            $username = $other?->username ?? 'نامشخص';

            $preview = match($last?->type) {
              'image' => 'تصویر',
              'video' => 'ویدیو',
              default => $last?->body ?: 'هنوز پیامی نیست',
            };
          @endphp

          <a href="{{ route('messages.show', $c) }}"
            data-name="{{ strtolower($username) }}"
            class="conversation-item flex items-center gap-3 px-4 py-3 transition
              {{ $active ? 'bg-blue-50 border-r-4 border-blue-600' : 'hover:bg-slate-50 border-r-4 border-transparent' }}">

            <div class="w-11 h-11 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center text-sm font-semibold flex-shrink-0">
              {{ strtoupper(substr($username, 0, 1)) }}
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-2">
                <p class="text-sm {{ $active ? 'font-semibold' : 'font-medium' }} text-slate-900 truncate">
                  {{$username }}
                </p>

                @if($c->last_message_at)
                  <span class="text-xs text-slate-400 flex-shrink-0">
                    {{ $c->last_message_at->locale('fa')->diffForHumans(null, true) }}
                  </span>
                @endif
              </div>

              <p class="text-sm text-slate-500 truncate">{{ $preview }}</p>
            </div>
          </a>

        @empty
          <p class="p-8 text-center text-sm text-slate-400">
            هنوز گفتگویی نیست. یک گفتگوی جدید شروع کنید.
          </p>
        @endforelse
      </div>
    </div>

    {{-- Chat area --}}
    <div class="flex-1 flex-col bg-slate-50 {{ $conversation ? 'flex' : 'hidden sm:flex' }}">

      @if($conversation)
        @php
          $other = $conversation->users->firstWhere('id', '!=', auth()->id());
          $username = $other?->username ?? 'نامشخص';
        @endphp

        {{-- Chat header --}}
        <div class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 flex-shrink-0">
          <div class="flex items-center gap-3">

            <a href="{{ route('messages.index') }}" class="sm:hidden p-1.5 rounded-lg hover:bg-slate-100">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
              </svg>
            </a>

            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center text-sm font-semibold">
              {{ strtoupper(substr($username, 0, 1)) }}
            </div>

            <div>
              <p class="text-sm font-semibold text-slate-900">
                {{$username }}
              </p>

              <p class="text-xs text-slate-500">{{ $other?->email }}</p>
            </div>
          </div>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4" id="messagesBox">

          @foreach($messages as $message)
            @php
              $mine = (int) $message->user_id === (int) auth()->id();
            @endphp

            <div class="flex {{ $mine ? 'justify-end' : 'gap-3' }}">

              @unless($mine)
                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center text-xs font-semibold flex-shrink-0">
                  {{ strtoupper(substr($message->user->username ?? '?', 0, 1)) }}
                </div>
              @endunless

              <div class="max-w-md {{ $mine ? 'text-left' : '' }}">

                @if(($message->type ?? 'text') === 'image' && $message->attachment_path)

                  <div class="inline-block text-right overflow-hidden rounded-2xl border border-slate-100 bg-white {{ $mine ? 'rounded-tl-md' : 'rounded-tr-md' }}">
                    <a href="{{ asset('storage/'.$message->attachment_path) }}" target="_blank">
                      <img src="{{ asset('storage/'.$message->attachment_path) }}" class="max-w-full max-h-64 object-cover block" alt="">
                    </a>

                    @if($message->body)
                      <p class="px-3 py-2 text-sm text-slate-800">{{ $message->body }}</p>
                    @endif
                  </div>

                @elseif(($message->type ?? 'text') === 'video' && $message->attachment_path)

                  <div class="inline-block text-right overflow-hidden rounded-2xl bg-slate-900 {{ $mine ? 'rounded-tl-md' : 'rounded-tr-md' }}">
                    <video controls class="max-w-full max-h-64 block">
                      <source src="{{ asset('storage/'.$message->attachment_path) }}" type="{{ $message->attachment_mime }}">
                    </video>

                    @if($message->body)
                      <p class="px-3 py-2 text-sm text-white">{{ $message->body }}</p>
                    @endif
                  </div>

                @else

                  <div class="inline-block px-4 py-2.5 text-sm rounded-2xl text-right
                    {{ $mine ? 'bg-blue-600 text-white rounded-tl-md' : 'bg-white border border-slate-200 text-slate-800 rounded-tr-md' }}">
                    <p class="whitespace-pre-wrap">{{ $message->body }}</p>
                  </div>

                @endif

                <p class="text-xs text-slate-400 mt-1 {{ $mine ? 'ml-1 text-left' : 'mr-1' }}">
                  {{ $message->created_at->format('H:i') }}
                </p>

              </div>
            </div>
          @endforeach

        </div>

        {{-- Message form --}}
        <div class="p-4 bg-white border-t border-slate-200 flex-shrink-0 relative z-30">

          <form action="{{ route('messages.store', $conversation) }}" method="POST" enctype="multipart/form-data" id="chatForm">
            @csrf

            <div id="attachChip" class="hidden items-center gap-2 px-3 py-2 mb-3 rounded-xl bg-slate-100">
              <span id="attachChipIcon">تصویر</span>
              <span id="attachChipName" class="text-xs text-slate-700 truncate flex-1"></span>
              <button type="button" id="clearAttachBtn" class="text-sm text-slate-500">حذف</button>
            </div>

            <div class="flex items-center gap-3">

              <div class="relative flex-shrink-0">
                <button type="button" id="attachBtn"
                  class="w-11 h-11 rounded-full bg-blue-600 text-white text-2xl font-light flex items-center justify-center hover:bg-blue-700 transition">
                  +
                </button>

                <div id="attachMenu" class="hidden absolute bottom-14 right-0 w-52 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 overflow-hidden">

                  <button type="button" class="attach-option w-full flex items-center gap-3 px-4 py-3 text-sm hover:bg-slate-50 text-right" data-kind="image">
                    <span class="font-medium">تصویر</span>
                  </button>

                  <button type="button" class="attach-option w-full flex items-center gap-3 px-4 py-3 text-sm hover:bg-slate-50 text-right border-t border-slate-100" data-kind="video">
                    <span class="font-medium">ویدیو</span>
                  </button>

                </div>

                <input type="file" name="attachment" id="attachmentInput" class="hidden">
              </div>

              <input type="text" name="body" id="bodyInput" placeholder="پیام بنویسید..." autocomplete="off"
                class="flex-1 px-4 py-2.5 rounded-xl bg-slate-100 border-0 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600"/>

              <button type="submit" class="p-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition flex-shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
              </button>

            </div>
          </form>
        </div>

      @else

        <div class="flex-1 flex flex-col items-center justify-center text-center p-8">
          <p class="text-sm font-medium text-slate-900">یک گفتگو انتخاب کنید</p>
          <p class="text-sm text-slate-500 mt-1">
            از لیست سمت راست انتخاب کنید یا گفتگوی جدید بسازید
          </p>
        </div>

      @endif
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var box = document.getElementById('messagesBox');
      if (box) box.scrollTop = box.scrollHeight;

      document.getElementById('searchConversations')?.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('.conversation-item').forEach(function (el) {
          el.style.display = (el.dataset.name || '').indexOf(q) !== -1 ? '' : 'none';
        });
      });

      var attachBtn = document.getElementById('attachBtn');
      var attachMenu = document.getElementById('attachMenu');
      var attachmentInput = document.getElementById('attachmentInput');
      var attachChip = document.getElementById('attachChip');
      var attachChipName = document.getElementById('attachChipName');
      var attachChipIcon = document.getElementById('attachChipIcon');
      var bodyInput = document.getElementById('bodyInput');

      if (!attachBtn) return;

      attachBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        attachMenu.classList.toggle('hidden');
      });

      document.querySelectorAll('.attach-option').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          attachMenu.classList.add('hidden');
          attachmentInput.accept = btn.dataset.kind === 'image'
            ? 'image/jpeg,image/png,image/gif,image/webp'
            : 'video/mp4,video/quicktime,video/webm';
          attachmentInput.click();
        });
      });

      attachmentInput.addEventListener('change', function () {
        var file = attachmentInput.files[0];
        if (!file) return;
        attachChipIcon.textContent = file.type.indexOf('video') === 0 ? 'ویدیو' : 'تصویر';
        attachChipName.textContent = file.name;
        attachChip.classList.remove('hidden');
        attachChip.classList.add('flex');
      });

      document.getElementById('clearAttachBtn')?.addEventListener('click', function () {
        attachmentInput.value = '';
        attachChip.classList.add('hidden');
        attachChip.classList.remove('flex');
      });

      document.addEventListener('click', function (e) {
        if (!e.target.closest('#attachBtn') && !e.target.closest('#attachMenu')) {
          attachMenu.classList.add('hidden');
        }
      });

      document.getElementById('chatForm')?.addEventListener('submit', function (e) {
        var hasText = bodyInput.value.trim().length > 0;
        var hasFile = attachmentInput.files && attachmentInput.files.length > 0;
        if (!hasText && !hasFile) {
          e.preventDefault();
          bodyInput.focus();
        }
      });
    });
  </script>
</x-dashboard-layout>