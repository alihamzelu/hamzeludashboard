<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-8 sticky top-0 z-20">
  <div class="flex items-center gap-3 flex-1">
    <button onclick="openSidebar()" class="lg:hidden p-2 rounded-xl hover:bg-gray-100">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>

  </div>

  <div class="flex items-center gap-2 sm:gap-4 mr-3">
    
    {{-- Notifications Dropdown --}}
    <div class="relative" id="notificationWrapper">
      <button onclick="toggleNotifications()" class="relative p-2 rounded-xl hover:bg-gray-100 transition focus:outline-none focus:bg-gray-100">
        <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        
        @if(auth()->user()->unreadNotifications->count() > 0)
          <span class="absolute top-1.5 left-1.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
        @endif
      </button>

      <div id="notificationsDropdown" class="hidden absolute left-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden transform origin-top-left transition-all">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
          <span class="text-sm font-bold text-gray-900">اعلان‌ها</span>
          @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('notifications.readAll') }}" method="POST" class="m-0 p-0">
              @csrf
              @method('PATCH')
              <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                همه را خوانده شده علامت بزن
              </button>
            </form>
          @endif
        </div>

        <div class="max-h-[350px] overflow-y-auto divide-y divide-gray-50">
          @forelse(auth()->user()->notifications()->take(5)->get() as $notification)
            <div class="px-4 py-3 hover:bg-gray-50 transition flex gap-3 {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50/20' }}">
              <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 {{ $notification->read_at ? 'bg-gray-100 text-gray-500' : 'bg-black text-white' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
              </div>

              <div class="flex-1 min-w-0 pt-0.5">
                <p class="text-sm {{ $notification->read_at ? 'text-gray-600' : 'text-gray-900 font-medium' }} line-clamp-2">
                  {{ $notification->data['message'] ?? 'اعلان جدید' }}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                  {{ $notification->created_at->diffForHumans() }}
                </p>
              </div>
            </div>
          @empty
            <div class="px-4 py-8 text-center flex flex-col items-center justify-center text-gray-500">
              <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
              <p class="text-sm">اعلان جدیدی وجود ندارد</p>
            </div>
          @endforelse
        </div>

        <div class="p-2 border-t border-gray-100 bg-gray-50/50">
          <a href="{{ route('notifications.index') }}" class="block w-full py-2 text-center text-sm font-medium text-gray-600 hover:text-black hover:bg-gray-200 rounded-xl transition">
            مشاهده همه اعلان‌ها
          </a>
        </div>
      </div>
    </div>

    {{-- Profile & Logout Dropdown --}}
    <div class="relative pr-2 sm:pr-4 border-r border-gray-200" id="profileWrapper">
      <button onclick="toggleProfileMenu()" class="flex items-center gap-3 focus:outline-none group">
        <div class="w-9 h-9 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center text-sm font-semibold group-hover:bg-gray-300 transition shrink-0">
            @if (auth()->user()->avatar)
                <img
                    src="{{ asset('storage/' . auth()->user()->avatar) }}"
                    alt="{{ auth()->user()->name }}"
                    class="w-full h-full object-cover"
                >
            @else
                {{ strtoupper(substr(auth()->user()->name ?? 'AK', 0, 2)) }}
            @endif
        </div>

        <div class="hidden sm:block text-right">
            <p class="text-sm font-medium text-gray-900">
                {{ auth()->user()->name ?? 'کاربر' }}
            </p>
        </div>
    </button>

      {{-- Profile Dropdown Menu --}}
      <div id="profileDropdown" class="hidden absolute left-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden transform origin-top-left transition-all">
        <div class="p-2">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-xl transition font-medium">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              خروج
            </button>
          </form>
        </div>
      </div>
    </div>

  </div>
</header>

<script>
  function toggleNotifications() {
    const dropdown = document.getElementById('notificationsDropdown');
    const profileDropdown = document.getElementById('profileDropdown');
    
    profileDropdown.classList.add('hidden');
    dropdown.classList.toggle('hidden');
  }

  function toggleProfileMenu() {
    const profileDropdown = document.getElementById('profileDropdown');
    const notificationsDropdown = document.getElementById('notificationsDropdown');
    
    notificationsDropdown.classList.add('hidden');
    profileDropdown.classList.toggle('hidden');
  }

  document.addEventListener('click', function(event) {
    const notifWrapper = document.getElementById('notificationWrapper');
    const notifDropdown = document.getElementById('notificationsDropdown');
    
    const profileWrapper = document.getElementById('profileWrapper');
    const profileDropdown = document.getElementById('profileDropdown');
    
    if (notifWrapper && !notifWrapper.contains(event.target)) {
      notifDropdown.classList.add('hidden');
    }

    if (profileWrapper && !profileWrapper.contains(event.target)) {
      profileDropdown.classList.add('hidden');
    }
  });
</script>