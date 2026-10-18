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
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />

                    </svg>

                </div>

            </div>

            <h1 class="text-center text-xl font-semibold text-gray-900 mb-2">
                ورود به حساب
            </h1>

            <p class="text-center text-sm text-gray-500 mb-8 leading-relaxed">
                برای ادامه و دسترسی به فضای کاری ProSync<br>
                وارد حساب کاربری خود شوید
            </p>

            @if (session('status'))

                <div class="mb-4 rounded-xl bg-green-50 border border-green-100 px-4 py-3 text-xs font-medium text-green-600 text-center">
                    {{ session('status') }}
                </div>

            @endif

            @if ($errors->has('username'))

                @php
                    $usernameError = $errors->first('username');
                @endphp

                @if ($usernameError !== 'The username field is required.')
                    <div class="mb-4 rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-xs font-medium text-red-600 text-center">
                        نام کاربری یا رمز عبور وارد شده صحیح نیست.
                    </div>
                @endif

            @endif

            <form
                method="POST"
                action="{{ route('login') }}"
                class="space-y-4">

                @csrf

                {{-- Username --}}
                <div>

                    <div class="relative">

                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">

                            <svg
                                class="w-4 h-4 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0z" />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />

                            </svg>

                        </div>

                        <input
                            id="username"
                            type="text"
                            name="username"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="نام کاربری"
                            dir="ltr"
                            class="w-full pr-11 pl-4 py-3.5 rounded-xl bg-gray-100/80 border-0 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition">

                    </div>

                    @error('username')

                        @if ($message === 'The username field is required.')
                            <span class="text-xs text-red-600 mt-1.5 block px-1">
                                نام کاربری الزامی است.
                            </span>
                        @endif

                    @enderror

                </div>

                {{-- Password --}}
                <div>

                    <div class="relative">

                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">

                            <svg
                                class="w-4 h-4 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />

                            </svg>

                        </div>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="رمز عبور"
                            dir="ltr"
                            class="w-full pr-12 pl-12 py-3.5 rounded-xl bg-gray-100/80 border-0 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition">

                        <button
                            type="button"
                            onclick="togglePassword()"
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
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />

                            </svg>

                        </button>

                    </div>

                    @error('password')
                        <span class="text-xs text-red-600 mt-1.5 block px-1">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

                {{-- Remember --}}
                <div class="flex items-center justify-between text-xs">

                    <label class="flex items-center gap-2 cursor-pointer text-gray-500">

                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-300">

                        <span>
                            مرا به خاطر بسپار
                        </span>

                    </label>

                    @if (Route::has('password.request'))

                        <a
                            href="{{ route('password.request') }}"
                            class="text-gray-500 hover:text-gray-800 transition">
                            فراموشی رمز عبور؟
                        </a>

                    @endif

                </div>

                <button
                    type="submit"
                    class="w-full py-3.5 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-all duration-300 shadow-lg shadow-gray-900/20">
                    ورود به حساب
                </button>

            </form>

            @if (Route::has('register'))

                <p class="mt-4 text-center text-xs text-gray-500">

                    حساب کاربری ندارید؟

                    <a
                        href="{{ route('register') }}"
                        class="text-gray-800 font-medium underline hover:no-underline">
                        ثبت‌نام
                    </a>

                </p>

            @endif

        </div>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');

            input.type = input.type === 'password'
                ? 'text'
                : 'password';
        }
    </script>

</x-guest-layout>