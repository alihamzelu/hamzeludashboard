@extends('layouts.error')

@section('title', 'دسترسی غیرمجاز')

@section('content')
    <p class="error-code">403</p>

    <h1 class="error-title">اجازه دسترسی ندارید!</h1>

    <p class="error-description">
        شما مجوز مشاهده این صفحه یا انجام این عملیات را ندارید.
        اگر فکر می‌کنید این خطا اشتباهی رخ داده، با مدیر سیستم تماس بگیرید.
    </p>

    <div class="error-actions">
        <a href="{{ url('/') }}" class="error-button">
            بازگشت به صفحه اصلی
        </a>

        @auth
            <a href="{{ route('dashboard') }}" class="error-button error-button-secondary">
                ورود به داشبورد
            </a>
        @else
            <a href="{{ route('login') }}" class="error-button error-button-secondary">
                ورود به حساب
            </a>
        @endauth
    </div>
@endsection