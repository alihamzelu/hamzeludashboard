<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>مدیریت پروژه و تیم</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Vazirmatn', 'sans-serif'],
                    },
                    colors: {
                        ink: '#17201F',
                        green: '#294744',
                        soft: '#F4F0E9',
                        peach: '#E7A47D',
                    }
                }
            }
        }
    </script>

    <style>
        * {
            font-family: 'Vazirmatn', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: #f4f0e9;
        }

        .hero-bg {
            background:
                linear-gradient(
                    90deg,
                    rgba(244, 240, 233, 0.12) 0%,
                    rgba(244, 240, 233, 0.35) 45%,
                    rgba(244, 240, 233, 0.82) 100%
                ),
                url('{{ asset('storage/home/hero.jpg') }}');

            background-size: cover;
            background-position: center;
        }

        .hero-overlay {
            background:
                radial-gradient(
                    circle at 15% 25%,
                    rgba(231, 164, 125, 0.25),
                    transparent 30%
                ),
                radial-gradient(
                    circle at 70% 80%,
                    rgba(41, 71, 68, 0.12),
                    transparent 35%
                );
        }

        .soft-shadow {
            box-shadow:
                0 20px 60px rgba(23, 32, 31, 0.10);
        }

        .fade-up {
            opacity: 0;
            transform: translateY(18px);
            animation: fadeUp .7s ease forwards;
        }

        .delay-1 {
            animation-delay: .1s;
        }

        .delay-2 {
            animation-delay: .2s;
        }

        .delay-3 {
            animation-delay: .3s;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mobile-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: .25s ease;
        }

        .mobile-menu.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
    </style>
</head>

<body class="min-h-screen text-ink antialiased">

<div class="min-h-screen p-3 sm:p-5 lg:p-6">

    <!-- Main -->
    <main
        class="hero-bg relative min-h-[calc(100vh-1.5rem)] sm:min-h-[calc(100vh-2.5rem)] lg:min-h-[calc(100vh-3rem)] overflow-hidden rounded-[28px] sm:rounded-[36px]"
    >

        <!-- Overlay -->
        <div class="hero-overlay absolute inset-0"></div>

        <!-- Header -->
        <header class="relative z-20 mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8 lg:px-10">

            <!-- Logo -->
            <a
                href="{{ url('/') }}"
                class="flex items-center gap-2.5"
            >
                <span
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-green text-white shadow-sm"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                        />
                    </svg>
                </span>

                <div class="hidden sm:block">
                    <p class="text-sm font-bold text-ink">
                        مدیریت تیم
                    </p>
                    <p class="text-[11px] text-ink/50">
                        ساده و متمرکز
                    </p>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-2">

                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="mr-2 rounded-full bg-ink px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green"
                    >
                        داشبورد
                    </a>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="mr-2 rounded-full border border-ink/20 bg-white/50 px-6 py-2.5 text-sm font-semibold text-ink transition hover:bg-white"
                    >
                        ورود
                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="rounded-full bg-ink px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green"
                    >
                        شروع کنید
                    </a>
                @endauth

            </nav>

            <!-- Mobile Button -->
            <button
                type="button"
                onclick="toggleMenu()"
                class="flex h-11 w-11 items-center justify-center rounded-full border border-ink/10 bg-white/70 text-ink shadow-sm md:hidden"
                aria-label="باز کردن منو"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
            </button>

        </header>


        <!-- Mobile Menu -->
        <div
            id="mobileMenu"
            class="mobile-menu absolute left-5 right-5 top-20 z-30 rounded-3xl border border-white/70 bg-white/90 p-3 shadow-xl backdrop-blur-xl md:hidden"
        >

            <div class="flex flex-col gap-1">



                @auth

                    <a
                        href="{{ route('dashboard') }}"
                        class="rounded-2xl bg-ink px-4 py-3 text-center text-sm font-semibold text-white"
                    >
                        داشبورد
                    </a>

                @else

                    <a
                        href="{{ route('login') }}"
                        class="rounded-2xl px-4 py-3 text-center text-sm font-semibold text-ink transition hover:bg-soft"
                    >
                        ورود
                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="rounded-2xl bg-ink px-4 py-3 text-center text-sm font-semibold text-white"
                    >
                        شروع کنید
                    </a>

                @endauth

            </div>

        </div>


        <!-- Hero -->
        <section
            id="about"
            class="relative z-10 flex min-h-[calc(100vh-6rem)] items-center"
        >

            <div class="mx-auto grid w-full max-w-7xl grid-cols-1 px-6 pb-14 pt-8 sm:px-10 lg:grid-cols-2 lg:px-12 lg:pb-20">

                <!-- Right Content -->
                <div class="flex flex-col justify-center text-right lg:pr-10">

                    <!-- Badge -->
                    <div class="fade-up inline-flex w-fit items-center gap-2 rounded-full border border-white/80 bg-white/65 px-4 py-2 text-xs font-medium text-green backdrop-blur-md">

                        <span class="h-2 w-2 rounded-full bg-peach"></span>

                        مدیریت پروژه و تیم

                    </div>


                    <!-- Heading -->
                    <h1
                        class="fade-up delay-1 mt-7 max-w-xl text-4xl font-extrabold leading-[1.45] tracking-tight text-ink sm:text-5xl lg:text-[3.8rem]"
                    >
                        کار تیم را
                        <span class="text-green">آرام‌تر</span>
                        و
                        <span class="text-green">منظم‌تر</span>
                        پیش ببرید
                    </h1>


                    <!-- Description -->
                    <p
                        class="fade-up delay-2 mt-6 max-w-lg text-sm leading-8 text-ink/65 sm:text-base"
                    >
                        پروژه‌ها، وظایف، اعضای تیم و گفتگوها را در یک محیط ساده و متمرکز مدیریت کنید؛
                        بدون شلوغی و ابزارهای اضافی.
                    </p>


                    <!-- Buttons -->
                    <div
                        class="fade-up delay-3 mt-9 flex flex-col gap-3 sm:flex-row sm:justify-start"
                    >

                        @auth

                            <a
                                href="{{ route('dashboard') }}"
                                class="group inline-flex items-center justify-center gap-3 rounded-2xl bg-ink px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-ink/10 transition duration-300 hover:-translate-y-0.5 hover:bg-green"
                            >
                                ورود به داشبورد

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 transition group-hover:-translate-x-1"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M5 12h14M13 6l6 6-6 6"
                                    />
                                </svg>
                            </a>

                        @else

                            <a
                                href="{{ route('register') }}"
                                class="group inline-flex items-center justify-center gap-3 rounded-2xl bg-ink px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-ink/10 transition duration-300 hover:-translate-y-0.5 hover:bg-green"
                            >
                                شروع کنید

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 transition group-hover:-translate-x-1"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M5 12h14M13 6l6 6-6 6"
                                    />
                                </svg>
                            </a>

                            <a
                                href="{{ route('login') }}"
                                class="inline-flex items-center justify-center rounded-2xl border border-ink/15 bg-white/65 px-7 py-3.5 text-sm font-semibold text-ink backdrop-blur-md transition duration-300 hover:bg-white"
                            >
                                ورود
                            </a>

                        @endauth

                    </div>


                    <!-- Small Info -->
                    <div
                        class="fade-up delay-3 mt-10 flex items-center gap-6 text-xs text-ink/50"
                    >

                        <div class="flex items-center gap-2">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-white/70">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-3.5 w-3.5 text-green"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M5 13l4 4L19 7"
                                    />
                                </svg>
                            </span>
                            ساده و سریع
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-white/70">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-3.5 w-3.5 text-green"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-7a2 2 0 00-2-2H6a2 2 0 00-2 2v7a2 2 0 002 2zm10-9V7a4 4 0 00-8 0v3h8z"
                                    />
                                </svg>
                            </span>
                            محیط امن
                        </div>

                    </div>

                </div>


                <!-- Left Empty Visual Space -->
                <div class="hidden lg:block"></div>

            </div>

        </section>


        <!-- Bottom Features -->
        <section
            id="features"
            class="absolute bottom-0 left-0 right-0 z-20 hidden border-t border-white/50 bg-white/25 backdrop-blur-md lg:block"
        >

            <div class="mx-auto grid max-w-7xl grid-cols-3 divide-x divide-x-reverse divide-ink/10 px-12">

                <div class="flex items-center gap-4 py-5">

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white/75 text-green">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-9a4 4 0 110 8 4 4 0 010-8zm6 4a3 3 0 10-2.83-4"
                            />
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-ink">
                            همکاری تیمی
                        </p>

                        <p class="mt-1 text-xs text-ink/50">
                            همه اعضا در جریان کار
                        </p>
                    </div>

                </div>


                <div class="flex items-center gap-4 px-8 py-5">

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white/75 text-green">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 3l7 4v5c0 4.5-3 7.8-7 9-4-1.2-7-4.5-7-9V7l7-4z"
                            />
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-ink">
                            مدیریت متمرکز
                        </p>

                        <p class="mt-1 text-xs text-ink/50">
                            پروژه‌ها در یک محیط
                        </p>
                    </div>

                </div>


                <div class="flex items-center gap-4 px-8 py-5">

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white/75 text-green">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13 10V3L4 14h7v7l9-11h-7z"
                            />
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-ink">
                            تمرکز بیشتر
                        </p>

                        <p class="mt-1 text-xs text-ink/50">
                            ابزارهای ضروری، بدون اضافه‌کاری
                        </p>
                    </div>

                </div>

            </div>

        </section>

    </main>

</div>


<script>
    function toggleMenu() {
        const menu = document.getElementById('mobileMenu');

        menu.classList.toggle('active');
    }

    function closeMenu() {
        const menu = document.getElementById('mobileMenu');

        menu.classList.remove('active');
    }

    document.addEventListener('click', function (event) {

        const menu = document.getElementById('mobileMenu');
        const button = event.target.closest('button');

        if (
            menu &&
            menu.classList.contains('active') &&
            !menu.contains(event.target) &&
            !button
        ) {
            menu.classList.remove('active');
        }

    });
</script>

</body>
</html>