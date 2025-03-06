@php
    $subtotal = 0;
    foreach (session('cart', []) as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
@endphp

<header class="">
    <div class="navbar bg-base-300">
        <div class="flex-1">
            <a href="/" class="btn btn-ghost text-2xl"><h3 class="text-2xl"> Tablao Pass</h3></a>
        </div>
        <div class="flex-none">
            {{--menu--}}
            <div>
                <div class="navbar bg-base-300">
                    <!-- Menú móvil (se muestra en pantallas pequeñas) -->
                    <div class="navbar-start">
                        <div class="dropdown">
                            <!-- Botón hamburguesa (visible en lg:hidden) -->
                            <label id="hamburguer" tabindex="0" class="btn btn-ghost lg:hidden">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    id="menu-hamburguer"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h8m-8 6h16" />
                                </svg>
                            </label>

                            <!-- Dropdown de items (visible cuando se hace clic en el botón hamburguesa) -->
                            <ul
                                tabindex="0"
                                class="menu menu-sm dropdown-content mt-3 z-[1] w-52 rounded-box bg-base-300 p-2 shadow">
                                <li><a class="{{ Request::is('/') || Request::is('/eventos') ? 'bg-primary' : '' }}" href=" {{ route("eventos") }}">Eventos</a></li>
                                <li><a class="{{ Request::is('/empresa') ? 'bg-primary' : '' }}" href=" {{ route("empresas.index") }}" >Empresas</a></li>
                                @guest
                                    <li><a id="loginhamburger" href="{{ route('login') }}">Iniciar Sesión</a></li>
                                    <li><a id="registerhamburger" href="{{ route('register') }}">Registrarse</a></li>
                                @endguest
                            </ul>
                        </div>
                    </div>

                    <!-- Menú escritorio (se muestra en pantallas grandes) -->
                    <div class="navbar-center hidden lg:flex">
                        <ul class="menu menu-horizontal px-1">
                            <li><a class="{{ Request::is('/') || Request::is('/eventos') ? 'bg-primary' : '' }}" href=" {{ route("eventos") }}">Eventos</a></li>
                            <li><a class="{{ Request::is('/empresa') ? 'bg-primary' : '' }}" id="empresa" href=" {{ route("empresas.index") }}" >Empresas</a></li>

                        </ul>
                    </div>
                </div>

            </div>
            {{--carrito--}}
            @can("cliente")
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                    <div class="indicator">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            id="carrito"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="badge badge-sm indicator-item"> {{ count(session('cart', [])) > 0 ? count(session('cart', [])) : 0 }}</span>
                    </div>
                </div>
                <div
                    tabindex="0"
                    class="card card-compact dropdown-content bg-base-300 z-[1] mt-3 w-52 shadow">
                    <div class="card-body">
                        <span class="text-lg font-bold">{{ count(session('cart', [])) > 0 ? count(session('cart', [])) : 0 }} Entradas</span>
                        <span class="text-info">Subtotal: {{ number_format($subtotal, 2) }}€</span>
                        <div class="card-actions">
                            <a href="{{ route('cart.index') }}" class="btn btn-primary btn-block">Ver Carrito</a>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
            {{--Perfil--}}
            @guest
                <div class="navbar-center hidden lg:flex">
                    <ul class="menu menu-horizontal px-1">
                        <li><a id="login" href="{{ route('login') }}">Iniciar Sesión</a></li>
                        <li><a id="register" href="{{ route('register') }}">Registrarse</a></li>
                    </ul>
                    {{--carrito guest--}}
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                            <div class="indicator">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="badge badge-sm indicator-item"> {{ count(session('cart', [])) > 0 ? count(session('cart', [])) : 0 }}</span>
                            </div>
                        </div>
                        <div
                            tabindex="0"
                            class="card card-compact dropdown-content bg-base-300 z-[1] mt-3 w-52 shadow">
                            <div class="card-body">
                                <span class="text-lg font-bold">{{ count(session('cart', [])) > 0 ? count(session('cart', [])) : 0 }} Entradas</span>
                                <span class="text-info">Subtotal: {{ number_format($subtotal, 2) }}€</span>
                                <div class="card-actions">
                                    <a href="{{ route('cart.index') }}" class="btn btn-primary btn-block">Ver Carrito</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full">
                        <img
                            id="imagenPerfil"
                            alt="User avatar"
                            src="{{ Auth::user()->getProfilePhotoUrl() }}" />
                    </div>
                </div>
                @can("admin")
                <ul
                    tabindex="0"
                    class="menu menu-sm dropdown-content bg-base-300 rounded-box z-[1] mt-3 w-52 p-2 shadow">
                    <li><a href="{{ route('clientes.index') }}">Clientes</a> </li>
                    <li><a href="{{ route('eventos.index-admin') }}">Eventos</a></li>
                    <li><a href="{{ route('profile.edit') }}">Perfil</a></li>
                    <li><a href="{{ route('ventas.index') }}">Ventas</a></li>
                    <li><form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button id="cerrarSesioncliente" type="submit" >Cerrar Sesión</button>
                        </form>

                    </li>
                </ul>
                @endcan
                @can("cliente")
                    <ul
                        tabindex="0"
                        class="menu menu-sm dropdown-content bg-base-300 rounded-box z-[1] mt-3 w-52 p-2 shadow">
                        <li><a href="{{ route('profile.edit') }}">Perfil</a></li>
                        <li><a href="{{ route('tickets.index') }}">Ver mis entradas</a></li>
                        <li><form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button id="cerrarSesioncliente" type="submit" >Cerrar Sesión</button>
                            </form>
                        </li>
                    </ul>
                @endcan
                @can("empresa")
                    <ul
                        tabindex="0"
                        class="menu menu-sm dropdown-content bg-base-300 rounded-box z-[1] mt-3 w-52 p-2 shadow">
                        <li><a href="{{ route('profile.edit') }}">Perfil</a></li>
                        <li><a href="{{ route('eventos.create') }}">Crear Eventos</a></li>
                        <li><form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button id="cerrarSesioncliente" type="submit" >Cerrar Sesión</button>
                            </form>
                        </li>
                    </ul>
                @endcan
            </div>
            @endguest


            {{--   icono de tema     --}}
            <label class="swap swap-rotate">
                <!-- this hidden checkbox controls the state -->
                <input id="theme-toggle" type="checkbox" class="theme-controller appearance-none hidden" value="synthwave" />

                <!-- sun icon -->
                <svg
                    class="swap-off h-10 w-10 fill-current"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24">
                    <path
                        d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z" />
                </svg>

                <!-- moon icon -->
                <svg
                    class="swap-on h-10 w-10 fill-current"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24">
                    <path
                        d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z" />
                </svg>
            </label>
        </div>
    </div>
</header>
{{--script para cambiar el tema--}}
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
