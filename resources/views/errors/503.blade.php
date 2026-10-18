@extends('layouts.error')

@section('title', 'سرویس موقتاً در دسترس نیست')

@section('content')
    <p class="error-code">503</p>

    <h1 class="error-title">در حال انجام عملیات نگهداری هستیم!</h1>

    <p class="error-description">
        سرویس در حال حاضر موقتاً در دسترس نیست.
        ممکن است عملیات به‌روزرسانی یا نگهداری در حال انجام باشد.
        لطفاً کمی بعد دوباره مراجعه کنید.
    </p>

    <div class="error-actions">
        <a href="{{ url('/') }}" class="error-button">
            تلاش مجدد
        </a>
    </div>
@endsection