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
                فراموشی رمز عبور
            </h1>

            <p class="text-center text-sm text-gray-500 mb-8 leading-relaxed">
                ایمیل خود را وارد کنید تا لینک تغییر رمز عبور<br>
                برای شما ارسال شود
            </p>

            @if (session('status'))
                <div
                    class="mb-5 rounded-xl bg-green-50 border border-green-100 px-4 py-3 text-xs font-medium text-green-600 text-center"
                >
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 border border-red-100 px-4 py-3">
                    <p class="text-xs font-medium text-red-600 text-center">
                        {{ $errors->first() }}
                    </p>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <div>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="ایمیل"
                        dir="ltr"
                        class="w-full px-4 py-3.5 rounded-xl bg-gray-100/80 border-0 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition"
                    >

                    @error('email')
                        <span class="text-xs text-red-600 mt-1.5 block px-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full py-3.5 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-all duration-300 shadow-lg shadow-gray-900/20"
                >
                    ارسال لینک بازیابی
                </button>

            </form>

            <p class="mt-6 text-center text-xs text-gray-500">

                رمز عبورتان را به خاطر آوردید؟

                <a
                    href="{{ route('login') }}"
                    class="text-gray-800 font-medium underline hover:no-underline"
                >
                    بازگشت به ورود
                </a>

            </p>

        </div>

    </div>

</x-guest-layout>