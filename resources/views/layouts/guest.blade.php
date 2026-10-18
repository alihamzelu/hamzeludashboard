<!doctype html>
<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >
    
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Estedad:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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

    <title>
        {{ $title ?? 'ProSync' }}
    </title>

</head>

<body
    class="font-[Inter] min-h-screen flex items-center justify-center relative overflow-hidden"
>

    {{-- Background --}}
    <div class="absolute inset-0">

        <img
            src="https://images.unsplash.com/photo-1499346030926-9a72daac6c63?q=80&w=2070&auto=format&fit=crop"
            alt="آسمان"
            class="w-full h-full object-cover"
        >

        <div
            class="absolute inset-0 bg-gradient-to-b from-sky-200/40 via-transparent to-white/30"
        ></div>

    </div>

    {{-- Page Content --}}
    {{ $slot }}

</body>

</html>