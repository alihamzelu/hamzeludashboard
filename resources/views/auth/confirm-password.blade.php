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
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 002-2H6a2 2 0 00-2 2v6a2 2 0 002 2h12z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8 11V7a4 4 0 018 0v4"
                        />
                    </svg>
                </div>
            </div>

            <h1 class="text-center text-xl font-semibold text-gray-900 mb-2">
                تأیید رمز عبور
            </h1>

            <p class="text-center text-sm text-gray-500 mb-8 leading-relaxed">
                برای ادامه، لطفاً رمز عبور خود را<br>
                دوباره وارد کنید.
            </p>

            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 border border-red-100 px-4 py-3">
                    <p class="text-xs font-medium text-red-600 text-center">
                        {{ $errors->first() }}
                    </p>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('password.confirm') }}"
                class="space-y-4"
            >
                @csrf

                <div>

                    <div class="relative">

                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            autofocus
                            placeholder="رمز عبور"
                            dir="ltr"
                            class="w-full px-4 py-3.5 rounded-xl bg-gray-100/80 border-0 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition"
                        >

                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute inset-y-0 left-0 pl-4 flex items-center"
                        >
                            <svg
                                class="w-4 h-4 text-gray-400 hover:text-gray-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                />
                            </svg>
                        </button>

                    </div>

                    @error('password')
                        <span class="text-xs text-red-600 mt-1.5 block px-1">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

                <button
                    type="submit"
                    class="w-full py-3.5 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-all duration-300 shadow-lg shadow-gray-900/20"
                >
                    تأیید و ادامه
                </button>

            </form>

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