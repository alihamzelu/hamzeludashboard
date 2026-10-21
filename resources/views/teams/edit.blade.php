<x-dashboard-layout>
    <div class="max-w-4xl">

        <div class="mb-8">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">ویرایش کاربر</h1>
            <p class="text-slate-500 text-sm mt-1">
                اطلاعات و دسترسی‌های این کاربر را مدیریت کنید.
            </p>
        </div>

        @if (session('status') === 'profile-updated')
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                اطلاعات کاربر با موفقیت به‌روز شد.
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                رمز عبور با موفقیت به‌روز شد.
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                لطفاً خطاهای فرم را بررسی کنید.
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-6">

            <h2 class="text-lg font-semibold text-slate-900 mb-1">
                اطلاعات کاربر
            </h2>

            <p class="text-sm text-slate-500 mb-6">
                اطلاعات شخصی و حساب کاربر را ویرایش کنید.
            </p>

            <form
                method="POST"
                action="{{ route('teams.update', $user->username) }}"
                class="space-y-5">

                @csrf
                @method('PATCH')

                <div>
                    <label
                        for="name"
                        class="block text-sm font-medium text-slate-700 mb-1.5">
                        نام
                    </label>

                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $user->name) }}"
                        required
                        autofocus
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">

                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label
                        for="email"
                        class="block text-sm font-medium text-slate-700 mb-1.5">
                        ایمیل
                    </label>

                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">

                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label
                        for="username"
                        class="block text-sm font-medium text-slate-700 mb-1.5">
                        نام کاربری
                    </label>

                    <input
                        id="username"
                        name="username"
                        type="text"
                        value="{{ old('username', $user->username) }}"
                        required
                        autocomplete="username"
                        dir="ltr"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600">

                    @error('username')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror

                    <p class="mt-2 text-xs text-slate-500">
                        نام کاربری باید یکتا باشد.
                    </p>
                </div>

                <div class="flex justify-end pt-2">
                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                        ذخیره تغییرات
                    </button>
                </div>

            </form>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-6">

            <h2 class="text-lg font-semibold text-slate-900 mb-1">
                رمز عبور
            </h2>

            <p class="text-sm text-slate-500 mb-6">
                برای این کاربر یک رمز عبور جدید تعیین کنید.
            </p>

            <form
                method="POST"
                action="{{ route('teams.password.update', $user->username) }}"
                class="space-y-5 max-w-md">

                @csrf
                @method('PUT')

                <div>
                    <label
                        for="password"
                        class="block text-sm font-medium text-slate-700 mb-1.5">
                        رمز جدید
                    </label>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">

                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label
                        for="password_confirmation"
                        class="block text-sm font-medium text-slate-700 mb-1.5">
                        تکرار رمز جدید
                    </label>

                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">

                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end pt-2">
                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                        تغییر رمز
                    </button>
                </div>

            </form>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6">

            <h2 class="text-lg font-semibold text-slate-900 mb-1">
                اطلاعات عضویت
            </h2>

            <p class="text-sm text-slate-500 mb-6">
                اطلاعات مربوط به عضویت این کاربر در پروژه را مشاهده کنید.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                    <p class="text-xs text-slate-500 mb-1">نام کاربر</p>
                    <p class="text-sm font-medium text-slate-900">
                        {{ $user->name }}
                    </p>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                    <p class="text-xs text-slate-500 mb-1">پروژه</p>
                    <p class="text-sm font-medium text-slate-900">
                        {{ $project->name }}
                    </p>
                </div>

            </div>

        </div>

    </div>
</x-dashboard-layout>