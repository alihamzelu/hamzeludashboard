<head>
    <title>مدیریت پروژه | حساب</title>
</head>
<x-dashboard-layout>
    <div class="max-w-4xl">

        <div class="mb-8">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">تنظیمات</h1>
            <p class="text-slate-500 text-sm mt-1">مدیریت حساب و ترجیحات شما</p>
        </div>

        @if (session('status') === 'profile-updated')
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                پروفایل با موفقیت به‌روز شد.
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                رمز عبور با موفقیت به‌روز شد.
            </div>
        @endif

        {{-- Profile --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-1">پروفایل</h2>
            <p class="text-sm text-slate-500 mb-6">اطلاعات شخصی خود را به‌روز کنید</p>

            <form
                method="POST"
                action="{{ route('profile.update') }}"
                enctype="multipart/form-data"
                class="space-y-5">

                @csrf
                @method('PATCH')

                {{-- Avatar --}}
                


                <div class="flex items-center gap-5 mb-8">
                    <div class="relative flex-shrink-0">
                        @if(auth()->user()->avatar)
                            <img
                                id="avatar-preview"
                                src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                alt="{{ auth()->user()->name }}"
                                class="w-20 h-20 rounded-full object-cover border-2 border-white shadow-sm">
                        @else
                            <div
                                id="avatar-placeholder"
                                class="w-20 h-20 rounded-full bg-blue-600 text-white flex items-center justify-center text-2xl font-semibold">
                                {{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'ک', 0, 1, 'UTF-8'), 'UTF-8') }}
                            </div>

                            <img
                                id="avatar-preview"
                                src=""
                                alt="پیش‌نمایش آواتار"
                                class="hidden w-20 h-20 rounded-full object-cover border-2 border-white shadow-sm">
                        @endif
                    </div>

                    <div>
                        <label
                            for="avatar"
                            class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-xl cursor-pointer hover:bg-slate-200 transition">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M4 16.5V19a1 1 0 001 1h14a1 1 0 001-1v-2.5M16 7l-4-4-4 4m4-4v12"/>
                            </svg>
                            انتخاب عکس
                        </label>

                        <input
                            id="avatar"
                            type="file"
                            name="avatar"
                            accept="image/jpeg,image/png,image/webp"
                            class="hidden">

                        <p class="text-xs text-slate-400 mt-2">
                            JPG، PNG یا WEBP · حداکثر ۲ مگابایت
                        </p>

                        <p id="avatar-file-status" class="text-xs text-blue-600 mt-1 hidden">
                            عکس انتخاب شده و آماده ذخیره است.
                        </p>

                        @error('avatar')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>



                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        نام
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', auth()->user()->name) }}"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">

                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        ایمیل
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', auth()->user()->email) }}"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">

                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="username" class="mb-2 block text-sm font-medium text-slate-700">
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
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
                        placeholder="your_username"
                    >

                    @error('username')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
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

        {{-- Password --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-1">رمز عبور</h2>
            <p class="text-sm text-slate-500 mb-6">برای امنیت حساب، رمز عبور را به‌روز کنید</p>

            <form
                method="POST"
                action="{{ route('password.update') }}"
                class="space-y-5 max-w-md">

                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        رمز فعلی
                    </label>

                    <input
                        type="password"
                        name="current_password"
                        autocomplete="current-password"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">

                    @error('current_password', 'updatePassword')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        رمز جدید
                    </label>

                    <input
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">

                    @error('password', 'updatePassword')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        تکرار رمز جدید
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                        به‌روزرسانی رمز
                    </button>
                </div>
            </form>
        </div>

        {{-- Notifications --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-1">اعلان‌ها</h2>
            <p class="text-sm text-slate-500 mb-6">نحوه دریافت اعلان‌ها را مدیریت کنید</p>

            @if (session('status') === 'notifications-updated')
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    ترجیحات اعلان ذخیره شد.
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('profile.notifications') }}"
                class="space-y-5">

                @csrf
                @method('PATCH')

                @php
                    $user = auth()->user();

                    $prefs = [
                        [
                            'notify_email',
                            'اعلان ایمیل',
                            'ایمیل درباره به‌روزرسانی پروژه‌ها و موعدها دریافت کنید',
                            $user->notify_email
                        ],
                        [
                            'notify_task_assigned',
                            'واگذاری وظیفه',
                            'وقتی وظیفه‌ای به شما داده شد مطلع شوید',
                            $user->notify_task_assigned
                        ],
                        [
                            'notify_due_reminders',
                            'یادآوری موعد',
                            '۱ تا ۳ روز قبل از موعد وظیفه یادآوری بگیرید',
                            $user->notify_due_reminders
                        ],
                        [
                            'notify_weekly_digest',
                            'خلاصه هفتگی',
                            'هر هفته خلاصه فعالیت پروژه‌ها را دریافت کنید',
                            $user->notify_weekly_digest
                        ],
                    ];
                @endphp

                @foreach($prefs as [$name, $title, $desc, $on])
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ $title }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $desc }}</p>
                        </div>

                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input
                                type="checkbox"
                                name="{{ $name }}"
                                value="1"
                                class="sr-only peer"
                                {{ $on ? 'checked' : '' }}>

                            <div class="w-11 h-6 bg-slate-200 peer-checked:bg-blue-600 rounded-full transition peer-focus:ring-2 peer-focus:ring-blue-600/20"></div>

                            <div class="absolute top-0.5 right-0.5 w-5 h-5 bg-white rounded-full shadow transition peer-checked:-translate-x-5"></div>
                        </label>
                    </div>
                @endforeach

                <div class="flex justify-end pt-2">
                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                        ذخیره ترجیحات
                    </button>
                </div>
            </form>
        </div>

        {{-- Delete Account --}}
        <div class="bg-white rounded-2xl border border-red-200 p-6">
            <h2 class="text-lg font-semibold text-red-600 mb-1">منطقه خطر</h2>
            <p class="text-sm text-slate-500 mb-6">اقدامات غیرقابل بازگشت</p>

            <form
                method="POST"
                action="{{ route('profile.destroy') }}"
                onsubmit="return confirm('حساب شما برای همیشه حذف شود؟ این عمل قابل بازگشت نیست.')"
                class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">

                @csrf
                @method('DELETE')

                <div class="flex-1 max-w-sm">
                    <p class="text-sm font-medium text-slate-900 mb-2">حذف حساب</p>
                    <p class="text-xs text-slate-500 mb-3">برای تأیید، رمز عبور را وارد کنید</p>

                    <input
                        type="password"
                        name="password"
                        required
                        placeholder="رمز فعلی"
                        class="w-full px-4 py-2.5 rounded-xl border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">

                    @error('password', 'userDeletion')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="px-5 py-2.5 border border-red-300 text-red-600 text-sm font-medium rounded-xl hover:bg-red-50 transition flex-shrink-0">
                    حذف حساب
                </button>
            </form>
        </div>

    </div>
    
{{-- Avatar Cropper Modal --}}
    <div
        id="avatar-cropper-modal"
        class="fixed inset-0 hidden items-center justify-center bg-slate-950/60 p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="avatar-cropper-title">

        <div class="w-full max-w-xl bg-white rounded-2xl shadow-2xl overflow-hidden">

            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <div>
                    <h2 id="avatar-cropper-title" class="text-lg font-bold text-slate-900">
                        تنظیم عکس پروفایل
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        عکس را جابه‌جا کنید تا قسمت دلخواه داخل دایره قرار بگیرد.
                    </p>
                </div>

                <button
                    type="button"
                    id="avatar-cropper-close"
                    class="w-9 h-9 rounded-xl text-slate-500 hover:bg-slate-100 text-xl"
                    aria-label="بستن">
                    &times;
                </button>
            </div>

            <div class="p-5">
                <div id="avatar-cropper-container">
                    <img id="avatar-cropper-image" src="" alt="عکس برای برش">
                </div>

                <div class="mt-5">
                    <div class="flex items-center justify-between mb-2">
                        <label for="avatar-zoom" class="text-sm font-medium text-slate-700">
                            بزرگ‌نمایی
                        </label>
                        <span class="text-xs text-slate-400">زوم</span>
                    </div>

                    <input
                        id="avatar-zoom"
                        type="range"
                        min="0"
                        max="100"
                        value="0"
                        class="w-full cursor-pointer">

                    <p class="text-xs text-slate-400 mt-2">
                        برای تغییر کادر، عکس را بکشید. با اسلایدر می‌توانید زوم کنید.
                    </p>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 p-5 border-t border-slate-100">
                <button
                    type="button"
                    id="avatar-cropper-cancel"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50">
                    لغو
                </button>

                <button
                    type="button"
                    id="avatar-cropper-confirm"
                    class="px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
                    تأیید برش
                </button>
            </div>

        </div>
    </div>


    {{-- Avatar Preview --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('avatar');
    const preview = document.getElementById('avatar-preview');
    const placeholder = document.getElementById('avatar-placeholder');
    const modal = document.getElementById('avatar-cropper-modal');
    const cropperImage = document.getElementById('avatar-cropper-image');
    const zoomSlider = document.getElementById('avatar-zoom');
    const confirmButton = document.getElementById('avatar-cropper-confirm');
    const closeButton = document.getElementById('avatar-cropper-close');
    const cancelButton = document.getElementById('avatar-cropper-cancel');
    const fileStatus = document.getElementById('avatar-file-status');

    if (!input || !preview || !modal || !cropperImage) {
        return;
    }

    let cropper = null;
    let sourceUrl = null;
    let previousPreviewUrl = null;
    let previousFile = null;
    let previousPreviewSrc = preview.getAttribute('src') || '';
    let hadPlaceholder = !!placeholder;
    let lastZoom = 0;
    let isSavingCrop = false;

    const maxFileSize = 2 * 1024 * 1024;
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';

        if (cropper) {
            cropper.destroy();
            cropper = null;
        }

        cropperImage.removeAttribute('src');

        if (sourceUrl) {
            URL.revokeObjectURL(sourceUrl);
            sourceUrl = null;
        }

        zoomSlider.value = 0;
        lastZoom = 0;
    }

    function restoreInputFile(file) {
        const transfer = new DataTransfer();

        if (file) {
            transfer.items.add(file);
        }

        input.files = transfer.files;
    }

    function restorePreview() {
        if (previousPreviewUrl) {
            URL.revokeObjectURL(previousPreviewUrl);
            previousPreviewUrl = null;
        }

        if (previousPreviewSrc) {
            preview.src = previousPreviewSrc;
            preview.classList.remove('hidden');

            if (placeholder) {
                placeholder.classList.add('hidden');
            }
        } else {
            preview.removeAttribute('src');
            preview.classList.add('hidden');

            if (placeholder) {
                placeholder.classList.remove('hidden');
            }
        }

        if (fileStatus) {
            fileStatus.classList.add('hidden');
        }
    }

    input.addEventListener('change', function () {
        const file = input.files && input.files[0];

        if (!file) {
            return;
        }

        if (!allowedTypes.includes(file.type)) {
            alert('فرمت عکس باید JPG، PNG یا WEBP باشد.');
            restoreInputFile(previousFile);
            return;
        }

        if (file.size > maxFileSize) {
            alert('حجم عکس نباید بیشتر از ۲ مگابایت باشد.');
            restoreInputFile(previousFile);
            return;
        }

        if (typeof Cropper === 'undefined') {
            alert('ابزار برش عکس بارگذاری نشده است. اتصال اینترنت یا CDN را بررسی کنید.');
            restoreInputFile(previousFile);
            return;
        }

        if (cropper) {
            cropper.destroy();
            cropper = null;
        }

        if (sourceUrl) {
            URL.revokeObjectURL(sourceUrl);
        }

        sourceUrl = URL.createObjectURL(file);
        cropperImage.src = sourceUrl;

        openModal();

        cropperImage.onload = function () {
            if (cropper) {
                cropper.destroy();
            }

            cropper = new Cropper(cropperImage, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.8,
                responsive: true,
                restore: false,
                guides: false,
                center: true,
                highlight: false,
                background: false,
                movable: true,
                zoomable: true,
                zoomOnWheel: false,
                zoomOnTouch: true,
                cropBoxMovable: false,
                cropBoxResizable: false,
                toggleDragModeOnDblclick: false,
                ready: function () {
                    zoomSlider.value = 0;
                    lastZoom = 0;
                }
            });
        };
    });

    zoomSlider.addEventListener('input', function () {
        if (!cropper) {
            return;
        }

        const value = Number(zoomSlider.value);
        const delta = value - lastZoom;

        if (delta > 0) {
            cropper.zoom(delta / 100);
        } else if (delta < 0) {
            cropper.zoom(delta / 100);
        }

        lastZoom = value;
    });

    confirmButton.addEventListener('click', function () {
        if (!cropper || isSavingCrop) {
            return;
        }

        isSavingCrop = true;
        confirmButton.disabled = true;
        confirmButton.textContent = 'در حال آماده‌سازی...';

        const canvas = cropper.getCroppedCanvas({
            width: 512,
            height: 512,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
            fillColor: '#ffffff'
        });

        if (!canvas) {
            isSavingCrop = false;
            confirmButton.disabled = false;
            confirmButton.textContent = 'تأیید برش';
            alert('برش عکس ناموفق بود. لطفاً دوباره تلاش کنید.');
            return;
        }

        canvas.toBlob(function (blob) {
            if (!blob) {
                isSavingCrop = false;
                confirmButton.disabled = false;
                confirmButton.textContent = 'تأیید برش';
                alert('ساخت فایل عکس ناموفق بود.');
                return;
            }

            try {
                const croppedFile = new File(
                    [blob],
                    'avatar.jpg',
                    {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    }
                );

                const transfer = new DataTransfer();
                transfer.items.add(croppedFile);
                input.files = transfer.files;

                if (previousPreviewUrl) {
                    URL.revokeObjectURL(previousPreviewUrl);
                }

                previousPreviewUrl = URL.createObjectURL(blob);
                preview.src = previousPreviewUrl;
                preview.classList.remove('hidden');

                if (placeholder) {
                    placeholder.classList.add('hidden');
                }

                previousFile = croppedFile;
                previousPreviewSrc = previousPreviewUrl;
                hadPlaceholder = false;

                if (fileStatus) {
                    fileStatus.classList.remove('hidden');
                }

                closeModal();
            } catch (error) {
                alert('آماده‌سازی فایل ناموفق بود. لطفاً دوباره تلاش کنید.');
            } finally {
                isSavingCrop = false;
                confirmButton.disabled = false;
                confirmButton.textContent = 'تأیید برش';
            }
        }, 'image/jpeg', 0.92);
    });

    function cancelCrop() {
        restoreInputFile(previousFile);
        closeModal();
    }

    closeButton.addEventListener('click', cancelCrop);
    cancelButton.addEventListener('click', cancelCrop);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            cancelCrop();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            cancelCrop();
        }
    });

    const profileForm = input.closest('form');

    if (profileForm) {
        profileForm.addEventListener('submit', function (event) {
            if (input.files && input.files.length > 0) {
                const file = input.files[0];

                if (!allowedTypes.includes(file.type) || file.size > maxFileSize) {
                    event.preventDefault();
                    alert('فایل آواتار معتبر نیست یا حجم آن بیشتر از ۲ مگابایت است.');
                }
            }
        });
    }
});
</script>
</x-dashboard-layout>

