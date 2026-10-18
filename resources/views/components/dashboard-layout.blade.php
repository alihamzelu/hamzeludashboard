<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
    <meta name="robots" content="noindex, nofollow">

    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Estedad:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="../path/to/flowbite/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#EFF6FF',
                            100: '#DBEAFE',
                            500: '#3B82F6',
                            600: '#2563EB',
                            700: '#1D4ED8',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        html,
        body,
        button,
        input,
        textarea,
        select,
        a,
        p,
        span,
        div,
        li,
        nav,
        td,
        th {
            font-family: "Vazirmatn", sans-serif !important;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: "Estedad", "Vazirmatn", sans-serif !important;
        }

        .sidebar-link {
            transition: background .25s ease, color .25s ease;
        }

        .sidebar-link.active {
            background: #2563EB;
            color: #fff;
        }

        .sidebar-link:hover:not(.active) {
            background: #EFF6FF;
            color: #1D4ED8;
        }

        #sidebar {
            transition: width .3s ease, transform .3s ease;
        }

        #overlay {
            transition: opacity .3s ease;
        }

        #sidebar.collapsed {
            width: 72px;
        }

        #sidebar.collapsed .sidebar-text,
        #sidebar.collapsed .sidebar-user-info,
        #sidebar.collapsed .sidebar-badge {
            display: none;
        }

        #sidebar.collapsed .sidebar-link {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        @media (min-width: 1024px) {
            #main-content.expanded {
                margin-right: 72px;
            }
        }

        .card-lift {
            transition: border-color .25s ease, box-shadow .25s ease;
        }

        .card-lift:hover {
            border-color: #BFDBFE;
            box-shadow: 0 4px 14px rgba(37, 99, 235, .06);
        }
    </style>
    
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">

    <style>
        #avatar-cropper-modal {
            z-index: 9999;
        }

        #avatar-cropper-container {
            width: 100%;
            height: min(55vh, 420px);
            min-height: 260px;
            background: #f1f5f9;
            overflow: hidden;
            border-radius: 16px;
        }

        #avatar-cropper-image {
            display: block;
            max-width: 100%;
        }

        #avatar-cropper-container .cropper-view-box,
        #avatar-cropper-container .cropper-face {
            border-radius: 50%;
        }

        #avatar-cropper-container .cropper-view-box {
            outline: 2px solid #ffffff;
            box-shadow: 0 0 0 1000px rgba(15, 23, 42, 0.55);
        }

        #avatar-cropper-container .cropper-face {
            background: transparent;
        }

        #avatar-cropper-modal input[type="range"] {
            accent-color: #2563eb;
        }

        @media (max-width: 640px) {
            #avatar-cropper-container {
                height: 55vh;
                min-height: 220px;
            }
        }
    </style>

</head>

<body class="bg-[#F4F7FB] text-slate-800">

    <div class="flex">
        @include('components.dashboard.sidebar')

        <div id="overlay"
            class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-40 hidden opacity-0 lg:hidden"
            onclick="closeSidebar()"></div>

        <div id="main-content" class="flex-1 lg:mr-64 w-full transition-all duration-300">
            @include('components.dashboard.header')
            <main class="p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("collapsed");
            document.getElementById("main-content").classList.toggle("expanded");
        }

        function openSidebar() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("overlay");
            sidebar.classList.remove("translate-x-full");
            overlay.classList.remove("hidden");
            setTimeout(() => overlay.classList.remove("opacity-0"), 10);
        }

        function closeSidebar() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("overlay");
            sidebar.classList.add("translate-x-full");
            overlay.classList.add("opacity-0");
            setTimeout(() => overlay.classList.add("hidden"), 300);
        }
    </script>
</body>

</html>