<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-slate-900">حذف حساب</h2>
        <p class="mt-1 text-sm text-slate-500">
            با حذف حساب، همه داده‌ها برای همیشه پاک می‌شوند. قبل از حذف، هر چیزی که لازم دارید ذخیره کنید.
        </p>
    </header>

    <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        حذف حساب
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-slate-900">حساب حذف شود؟</h2>
            <p class="mt-1 text-sm text-slate-500">برای تأیید، رمز عبور را وارد کنید. این عمل قابل بازگشت نیست.</p>

            <div class="mt-6">
                <x-input-label for="password" value="رمز عبور" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-3/4" placeholder="رمز عبور" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">انصراف</x-secondary-button>
                <x-danger-button class="ms-3">حذف حساب</x-danger-button>
            </div>
        </form>
    </x-modal>
</section>