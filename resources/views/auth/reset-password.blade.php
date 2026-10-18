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
                تغییر رمز عبور
            </h1>

            <p class="text-center text-sm text-gray-500 mb-8">
                یک رمز عبور جدید برای حساب خود انتخاب کنید
            </p>

            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 border border-red-100 px-4 py-3">
                    <p class="text-xs font-medium text-red-600 text-center">
                        {{ $errors->first() }}
                    </p>
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                @csrf

                <input
                    type="hidden"
                    name="token"
                    value="{{ $request->route('token') }}"
                >

                <div>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', $request->email) }}"
                        required
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

                <div>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="رمز عبور جدید"
                        dir="ltr"
                        class="w-full px-4 py-3.5 rounded-xl bg-gray-100/80 border-0 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition"
                    >

                    @error('password')
                        <span class="text-xs text-red-600 mt-1.5 block px-1">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="تکرار رمز عبور جدید"
                        dir="ltr"
                        class="w-full px-4 py-3.5 rounded-xl bg-gray-100/80 border-0 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full py-3.5 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-all duration-300 shadow-lg shadow-gray-900/20"
                >
                    تغییر رمز عبور
                </button>

            </form>

        </div>

    </div>

</x-guest-layout>