<!doctype html>
<html data-theme="flamencoLight">
<head>
    <title>{{ config('app.name', 'Tablao Pass') }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ Vite::asset("resources/images/foto_login_flamenco.svg") }}" rel="icon" type="image/png">
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="flex flex-col min-h-screen">

{{-- Esto es el header --}}
@include('header')

{{-- Para los mensajes de errores que queramos gererar --}}
{{--
<div class="mx-2 my-2">
    @include('flash::message')
</div>
--}}

{{-- Esto es el contenido --}}
<div class="container flex-grow">
@yield('content')
    <form action="/checkout" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <button type="submit" class="btn btn-primary">Pagar</button>
    </form>
</div>
{{-- Esto es el footer --}}
@include('footer')


</body>
</html>
