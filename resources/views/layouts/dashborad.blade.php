<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .sidebar-link.active {
            background: #111;
            color: white;
        }

        .sidebar-link:hover:not(.active) {
            background:#f3f4f6;
        }

        #sidebar {
            transition:
            width .3s ease,
            transform .3s ease;
        }

        #overlay {
            transition: opacity .3s ease;
        }

        #sidebar.collapsed {
            width:72px;
        }

        #sidebar.collapsed .sidebar-text,
        #sidebar.collapsed .sidebar-user-info,
        #sidebar.collapsed .sidebar-badge {
            display:none;
        }

        #sidebar.collapsed .sidebar-link {
            justify-content:center;
            padding-left:0;
            padding-right:0;
        }

        @media(min-width:1024px){
            #main-content.expanded{
                margin-left:72px;
            }
        }
    </style>

</head>

<body class="bg-gray-50">


<div class="flex">


    {{-- Sidebar --}}
    @include('components.dashboard.sidebar')


    <div id="main-content"
         class="flex-1 lg:ml-64 w-full transition-all duration-300">


        {{-- Header --}}
        @include('components.dashboard.header')


        <main class="p-4 sm:p-6 lg:p-8">

            {{ $slot }}

        </main>


    </div>

</div>



<script>

function toggleSidebar(){

    const sidebar=document.getElementById("sidebar");
    const main=document.getElementById("main-content");

    sidebar.classList.toggle("collapsed");
    main.classList.toggle("expanded");

}


function openSidebar(){

    const sidebar=document.getElementById("sidebar");
    const overlay=document.getElementById("overlay");

    sidebar.classList.remove("-translate-x-full");

    overlay.classList.remove("hidden");

    setTimeout(()=>{
        overlay.classList.remove("opacity-0")
    },10);

}


function closeSidebar(){

    const sidebar=document.getElementById("sidebar");
    const overlay=document.getElementById("overlay");


    sidebar.classList.add("-translate-x-full");

    overlay.classList.add("opacity-0");


    setTimeout(()=>{
        overlay.classList.add("hidden")
    },300);

}

</script>


</body>
</html>