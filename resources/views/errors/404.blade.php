@extends('layouts.error')

@section('title', 'صفحه پیدا نشد')

@section('content')
    <p class="error-code">404</p>

    <h1 class="error-title">صفحه‌ای پیدا نشد!</h1>

    <p class="error-description">
        متأسفانه صفحه‌ای که به دنبال آن هستید وجود ندارد،
        حذف شده یا آدرس آن تغییر کرده است.
    </p>

    <div class="error-actions">
        <a href="{{ url('dashboard') }}" class="error-button">
            بازگشت به صفحه اصلی
        </a>

        <a href="javascript:history.back()" class="error-button error-button-secondary">
            بازگشت به صفحه قبل
        </a>
    </div>
@endsection