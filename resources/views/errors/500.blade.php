@extends('layouts.error')

@section('title', 'خطای داخلی سرور')

@section('content')
    <p class="error-code">500</p>

    <h1 class="error-title">مشکلی در سرور رخ داده است!</h1>

    <p class="error-description">
        هنگام پردازش درخواست شما مشکلی غیرمنتظره رخ داد.
        این مشکل معمولاً موقتی است. چند لحظه صبر کنید
        و دوباره تلاش کنید.
    </p>

    <div class="error-actions">
        <a href="{{ url('/') }}" class="error-button">
            بازگشت به صفحه اصلی
        </a>

        <a href="javascript:location.reload()" class="error-button error-button-secondary">
            تلاش مجدد
        </a>
    </div>
@endsection