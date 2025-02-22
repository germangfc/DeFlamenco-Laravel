<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Tablao Pass') }}</title>
        <link href="{{ Vite::asset("resources/images/foto_login_flamenco.svg") }}" rel="icon" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Lado superior (móviles) o lateral (pantallas grandes): Imagen -->
        <div class="w-full md:w-1/2 h-64 md:h-auto flex items-center justify-center bg-cover bg-center relative"
             style="background-image: url('{{ Vite::asset("resources/images/foto_login_flamenco_con_fondo.svg") }}');">
            <!-- Contenedor para el mensaje solo en móviles -->
            <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 md:hidden">
                <h2 class="text-3xl text-white" >
                    Bienvenido a Tablao Pass
                </h2>
            </div>
        </div>
        <!-- Lado inferior (móviles) o lateral (pantallas grandes): Formulario de login -->
        <div class="flex flex-col w-full md:w-1/2 justify-center items-center bg-base-100">
            <!-- Mensaje visible solo en pantallas medianas y superiores -->
            <h2 class="text-3xl hidden md:block">
                Bienvenido a Tablao Pass
            </h2>
            <div class="w-full sm:max-w-md mt-6 bg-base-200 px-6 py-4 shadow-2xl overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </div>
    </body>




</html>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const themeToggle = document.getElementById("theme-toggle");
        const currentTheme = localStorage.getItem("theme") || "flamencoLight"; // Tema por defecto

        document.documentElement.setAttribute("data-theme", currentTheme);
        themeToggle.checked = currentTheme === "flamencoDark"; // Activa el checkbox si el tema es oscuro

        themeToggle.addEventListener("change", function () {
            const newTheme = themeToggle.checked ? "flamencoDark" : "flamencoLight";
            document.documentElement.setAttribute("data-theme", newTheme);
            localStorage.setItem("theme", newTheme);
        });
    });
</script>
