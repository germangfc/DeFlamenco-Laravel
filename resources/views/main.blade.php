<!doctype html>
<html data-theme="flamencoLight">
<head>
    <title>{{ config('app.name', 'Tablao Pass') }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('resources/images/foto_login_flamenco.svg') }}" rel="icon" type="image/svg+xml">
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="flex flex-col min-h-screen">

{{-- Esto es el header --}}
@include('header')

{{-- Para los mensajes de éxito que queramos generar --}}
@if (session('success'))
    <div id="successAlert" class="fixed top-5 right-5 bg-green-600 text-white p-4 rounded-lg shadow-lg flex items-center gap-3 transition-opacity duration-500">
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
    <div id="errorAlert" class="fixed top-5 right-5 bg-red-600 text-white p-4 rounded-lg shadow-lg flex items-center gap-3 transition-opacity duration-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('error') }}</span>
        <button type="button" class="ml-auto text-white hover:text-gray-300 focus:outline-none" onclick="closeAlert('errorAlert')">
            ✖
        </button>
    </div>
@endif

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



{{-- Para los mensajes de errores que queramos gererar --}}
{{--
<div class="mx-2 my-2">
    @include('flash::message')
</div>
--}}

{{-- Esto es el contenido --}}
<div class="container flex-grow">
@yield('content')
    {{-- <form action="/checkout" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <button type="submit" class="btn btn-primary">Pagar</button>
    </form> --}}
</div>
{{-- Esto es el footer --}}
@include('footer')


</body>
</html>
