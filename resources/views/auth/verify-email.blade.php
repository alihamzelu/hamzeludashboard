<x-guest-layout>

    <div class="relative z-10 w-full max-w-[400px] mx-4">

        <div
            class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl shadow-sky-200/50 border border-white/60 px-8 py-10"
        >

            <div class="flex justify-center mb-6">
                <div class="w-12 h-12 rounded-full bg-white shadow-md flex items-center justify-center">
                    <svg
                        class="w-5 h-5 text-gray-700"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                        />
                    </svg>
                </div>
            </div>

            <h1 class="text-center text-xl font-semibold text-gray-900 mb-2">
                تأیید ایمیل
            </h1>

            <p class="text-center text-sm text-gray-500 mb-8 leading-relaxed">
                قبل از ادامه، لطفاً ایمیل خود را با کلیک روی<br>
                لینک ارسال‌شده تأیید کنید.
            </p>

            @if (session('status') === 'verification-link-sent')
                <div
                    class="mb-5 rounded-xl bg-green-50 border border-green-100 px-4 py-3 text-xs font-medium text-green-600 text-center"
                >
                    لینک تأیید جدید به ایمیل شما ارسال شد.
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('verification.send') }}"
            >
                @csrf

                <button
                    type="submit"
                    class="w-full py-3.5 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-all duration-300 shadow-lg shadow-gray-900/20"
                >
                    ارسال مجدد لینک تأیید
                </button>

            </form>

            <div class="mt-6 text-center">

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="text-xs text-gray-500 hover:text-gray-800 underline hover:no-underline transition"
                    >
                        خروج از حساب
                    </button>

                </form>

            </div>

        </div>

    </div>

</x-guest-layout>