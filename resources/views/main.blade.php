<!doctype html>
<html data-theme="flamencoLight">
<head>
    <title>{{ config('app.name', 'Tablao Pass') }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ Vite::asset("resources/images/foto_login_flamenco.svg") }}" rel="icon" type="image/png">
    <script>
        // Cuando la página y todos los recursos hayan cargado
        window.addEventListener('load', function() {
            document.getElementById('loader').style.display = 'none';
            document.getElementById('contenido').style.display = 'block';
        });
    </script>
    <style>
        /* From Uiverse.io by alexruix */
        .loader {
            width: 48px;
            height: 48px;
            margin: auto;
            position: relative;
        }

        .loader:before {
            content: '';
            width: 48px;
            height: 5px;
            background: #f0808050;
            position: absolute;
            top: 60px;
            left: 0;
            border-radius: 50%;
            animation: shadow324 0.5s linear infinite;
        }

        .loader:after {
            content: '';
            width: 100%;
            height: 100%;
            background: #f08080;
            position: absolute;
            top: 0;
            left: 0;
            border-radius: 4px;
            animation: jump7456 0.5s linear infinite;
        }

        @keyframes jump7456 {
            15% {
                border-bottom-right-radius: 3px;
            }

            25% {
                transform: translateY(9px) rotate(22.5deg);
            }

            50% {
                transform: translateY(18px) scale(1, .9) rotate(45deg);
                border-bottom-right-radius: 40px;
            }

            75% {
                transform: translateY(9px) rotate(67.5deg);
            }

            100% {
                transform: translateY(0) rotate(90deg);
            }
        }

        @keyframes shadow324 {

            0%,
            100% {
                transform: scale(1, 1);
            }

            50% {
                transform: scale(1.2, 1);
            }
        }
    </style>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&display=swap&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>

<body class="flex flex-col min-h-screen">
<div class="loader" id="loader"></div>
@if (session('success'))
    <div id="successAlert" class="fixed top-5 right-5 bg-green-600 text-white p-4 rounded-lg z-50 shadow-lg flex items-center gap-3 transition-opacity duration-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('success') }}</span>
        <button type="button" class="ml-auto text-white hover:text-gray-300 focus:outline-none" onclick="closeAlert('successAlert')">
            ✖
        </button>
    </div>
@endif

@if (session('error'))
    <div id="errorAlert" class="fixed top-5 right-5 bg-red-600 text-white p-4 z-50 rounded-lg shadow-lg flex items-center gap-3 transition-opacity duration-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('error') }}</span>
        <button type="button" class="ml-auto text-white hover:text-gray-300 focus:outline-none" onclick="closeAlert('errorAlert')">
            ✖
        </button>
    </div>
@endif

<div id="contenido" class="flex flex-col flex-grow" style="display: none">
    {{-- Header --}}
    @include('header')

    {{-- Contenido principal --}}
    <main class="flex-grow container">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('footer')
</div>

</body>
</html>

<script>
    function closeAlert(id) {
        let alert = document.getElementById(id);
        if (alert) {
            alert.classList.add('opacity-0');
            setTimeout(() => alert.remove(), 500);
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(() => closeAlert('successAlert'), 3000);
        setTimeout(() => closeAlert('errorAlert'), 3000);
    });
</script>
