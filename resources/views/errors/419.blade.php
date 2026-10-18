@extends('layouts.error')

@section('title', 'نشست منقضی شده')

@section('content')
    <p class="error-code">419</p>

    <h1 class="error-title">نشست شما منقضی شده است!</h1>

    <p class="error-description">
        ممکن است مدت زیادی از باز بودن صفحه گذشته باشد
        یا نشست شما منقضی شده باشد. صفحه را دوباره بارگذاری کنید
        و در صورت نیاز مجدداً وارد حساب خود شوید.
    </p>

    <div class="error-actions">
        <a href="{{ url()->current() }}" class="error-button">
            بارگذاری مجدد صفحه
        </a>

        @auth
            <a href="{{ route('dashboard') }}" class="error-button error-button-secondary">
                بازگشت به داشبورد
            </a>
        @else
            <a href="{{ route('login') }}" class="error-button error-button-secondary">
                ورود مجدد
            </a>
        @endauth
    </div>
@endsection