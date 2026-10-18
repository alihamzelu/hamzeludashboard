@extends('layouts.error')

@section('title', 'تعداد درخواست‌ها زیاد است')

@section('content')
    <p class="error-code">429</p>

    <h1 class="error-title">کمی آهسته‌تر!</h1>

    <p class="error-description">
        در مدت کوتاهی درخواست‌های زیادی ارسال کرده‌اید.
        لطفاً کمی صبر کنید و سپس دوباره تلاش کنید.
    </p>

    <div class="error-actions">
        <a href="{{ url('/') }}" class="error-button">
            بازگشت به صفحه اصلی
        </a>

        <a href="javascript:history.back()" class="error-button error-button-secondary">
            بازگشت به صفحه قبل
        </a>
    </div>
@endsection