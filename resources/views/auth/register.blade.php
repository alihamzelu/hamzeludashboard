<x-guest-layout>

    <div class="relative z-10 w-full max-w-[400px] mx-4">

        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl shadow-sky-200/50 border border-white/60 px-8 py-10">

            <div class="flex justify-center mb-6">
                <div class="w-12 h-12 rounded-full bg-white shadow-md flex items-center justify-center">
                    <svg
                        class="w-5 h-5 text-gray-700"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 4v16m8-8H4" />
                    </svg>
                </div>
            </div>

            <h1 class="text-center text-xl font-semibold text-gray-900 mb-2">
                ساخت حساب کاربری
            </h1>

            <p class="text-center text-sm text-gray-500 mb-8 leading-relaxed">
                برای شروع کار با ProSync حساب خود را بسازید
            </p>

            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 border border-red-100 px-4 py-3">
                    <p class="text-xs font-medium text-red-600 text-center">
                        لطفاً اطلاعات وارد شده را بررسی کنید.
                    </p>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('register') }}"
                class="space-y-4">

                @csrf

                {{-- Name --}}
                <div>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="نام و نام خانوادگی"
                        class="w-full px-4 py-3.5 rounded-xl bg-gray-100/80 border-0 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition">

                    @error('name')
                        <span class="text-xs text-red-600 mt-1.5 block px-1">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

                {{-- Username --}}
                <div>

                    <input
                        id="username"
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        autocomplete="username"
                        placeholder="نام کاربری"
                        dir="ltr"
                        class="w-full px-4 py-3.5 rounded-xl bg-gray-100/80 border-0 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition">

                    @error('username')
                        <span class="text-xs text-red-600 mt-1.5 block px-1">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

                {{-- Email --}}
                <div>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        placeholder="ایمیل"
                        dir="ltr"
                        class="w-full px-4 py-3.5 rounded-xl bg-gray-100/80 border-0 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition">

                    @error('email')
                        <span class="text-xs text-red-600 mt-1.5 block px-1">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

                {{-- Password --}}
                <div>

                    <div class="relative">

                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="رمز عبور"
                            dir="ltr"
                            class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-gray-100/80 border-0 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition">

                        <button
                            type="button"
                            onclick="togglePassword('password', this)"
                            class="absolute inset-y-0 left-0 w-12 flex items-center justify-center">

                            <svg
                                class="eye-icon w-4 h-4 text-gray-400 hover:text-gray-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7z" />

                            </svg>

                        </button>

                    </div>

                    @error('password')
                        <span class="text-xs text-red-600 mt-1.5 block px-1">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

                {{-- Confirm Password --}}
                <div>

                    <div class="relative">

                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="تکرار رمز عبور"
                            dir="ltr"
                            class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-gray-100/80 border-0 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition">

                        <button
                            type="button"
                            onclick="togglePassword('password_confirmation', this)"
                            class="absolute inset-y-0 left-0 w-12 flex items-center justify-center">

                            <svg
                                class="w-4 h-4 text-gray-400 hover:text-gray-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7z" />

                            </svg>

                        </button>

                    </div>

                    @error('password_confirmation')
                        <span class="text-xs text-red-600 mt-1.5 block px-1">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

                <button
                    type="submit"
                    class="w-full py-3.5 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-all duration-300 shadow-lg shadow-gray-900/20">
                    ساخت حساب
                </button>

            </form>

            <p class="mt-6 text-center text-xs text-gray-500">

                قبلاً حساب ساخته‌اید؟

                <a
                    href="{{ route('login') }}"
                    class="text-gray-800 font-medium underline hover:no-underline">
                    ورود
                </a>

            </p>

        </div>

    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('.eye-icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.add('opacity-50');
            } else {
                input.type = 'password';
                icon.classList.remove('opacity-50');
            }
        }
    </script>

</x-guest-layout>