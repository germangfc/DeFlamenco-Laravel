<!doctype html>
<html data-theme="cupcake">
<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('images/LogoTablao.jpg') }}" rel="icon" type="image/png">
    @vite('resources/css/app.css')
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
</div>


{{-- Esto es el footer --}}
@include('footer')

</body>
</html>
