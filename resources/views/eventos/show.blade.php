@php
    use Illuminate\Support\Str;
@endphp

@extends('main')

@section('content')
    <!-- Sección Hero del Evento con margen superior para evitar el header -->
    <div id="heroParallax"
         class="relative mt-16 min-h-[80vh] flex flex-col justify-center overflow-hidden mb-16"
         style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ Str::startsWith($evento->foto, 'http') ? $evento->foto : asset('storage/images/' . $evento->foto) }}'); background-size: cover; background-position: center;">

        <!-- Flechas de Navegación -->
        <div class="absolute top-1/2 transform -translate-y-1/2 w-full flex items-center justify-between px-5 gap-8 z-20">
            @if($eventoAnterior)
                <a href="{{ route('eventos.show', $eventoAnterior->id) }}"
                   class="transition duration-300 ease-in-out backdrop-blur-sm bg-white/10 hover:bg-white/20 transform hover:scale-110 rounded-full w-12 h-12 flex items-center justify-center cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
            @endif

            @if($eventoSiguiente)
                <a href="{{ route('eventos.show', $eventoSiguiente->id) }}"
                   class="transition duration-300 ease-in-out backdrop-blur-sm bg-white/10 hover:bg-white/20 transform hover:scale-110 rounded-full w-12 h-12 flex items-center justify-center cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            @endif
        </div>

        <!-- Contenedor de Detalles (incluye datos y formulario de compra) -->
        <div class="relative z-20 px-4 md:px-10 py-8 bg-black/30 mx-auto max-w-4xl rounded-md">
            <!-- Caja de Ciudad -->
            <div class="mb-5">
                <div class="w-8 h-1 bg-white mb-2"></div>
                <div class="text-lg">{{ $evento->ciudad }}</div>
            </div>

            <!-- Título del Evento -->
            <h1 class="font-oswald text-5xl md:text-6xl uppercase leading-tight tracking-tight mb-4"
                style="text-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                {{ $evento->nombre }}
            </h1>

            <!-- Detalles del Evento -->
            <div class="max-w-xl my-8 text-lg space-y-4">
                <div class="flex items-center gap-4 py-4 border-b border-white/10 transition-colors hover:bg-white/5">
                    <strong class="font-semibold min-w-[90px] inline-flex items-center gap-2">📅 Fecha:</strong>
                    <span>{{ $evento->fecha }} a las {{ $evento->hora }}</span>
                </div>
                <div class="flex items-center gap-4 py-4 border-b border-white/10 transition-colors hover:bg-white/5">
                    <strong class="font-semibold min-w-[90px] inline-flex items-center gap-2">📍 Lugar:</strong>
                    <span>{{ $evento->direccion }}, {{ $evento->ciudad }}</span>
                </div>
                <div class="flex items-center gap-4 py-4 border-b border-white/10 transition-colors hover:bg-white/5">
                    <strong class="font-semibold min-w-[90px] inline-flex items-center gap-2">Precio:</strong>
                    <span>{{ $evento->precio }}€</span>
                </div>
            </div>

            <!-- Formulario de Compra -->
            @if(!Auth::check() || (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('empresa')))
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/10 shadow-lg mt-8">
                    <h3 class="text-xl font-semibold mb-4">COMPRA TUS ENTRADAS</h3>
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="idEvent" value="{{ $evento->id }}">
                        <input type="hidden" name="price" value="{{ $evento->precio }}">
                        <input type="hidden" name="name" value="{{ $evento->nombre }}">

                        <div class="flex items-center gap-4 my-8">
                            <button type="button" id="decrease"
                                    class="w-10 h-10 rounded-full bg-[#ecad29] text-white text-xl transition duration-300">
                                -
                            </button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="5"
                                   class="w-16 text-center bg-transparent border border-white/20 text-white p-2 rounded-md text-xl">
                            <button type="button" id="increase"
                                    class="w-10 h-10 rounded-full bg-[#ecad29] text-white text-xl transition duration-300">
                                +
                            </button>
                        </div>

                        <button type="submit"
                                class="w-full py-5 bg-[#ecad29] rounded-lg text-white font-semibold uppercase tracking-wide transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                            COMPRAR <span id="totalPrice">{{ $evento->precio }}€</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Contenedor del Mapa con altura fija -->
    <div class="my-16 mx-auto w-11/12 rounded-2xl overflow-hidden shadow-lg border border-white/10">
        <div id="map" class="w-full h-[400px]"></div>
    </div>

    <!-- Leaflet Map Scripts -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var address = '{{ $evento->ciudad . ' ' . $evento->direccion }}';

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        var map = L.map('map').setView([data[0].lat, data[0].lon], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                        L.marker([data[0].lat, data[0].lon]).addTo(map)
                            .bindPopup('{{ $evento->nombre }}');
                    }
                });

            // Controlador de Cantidad
            const quantityInput = document.getElementById('quantity');
            const totalPrice = document.getElementById('totalPrice');
            const price = {{ $evento->precio }};

            document.getElementById('increase').addEventListener('click', () => {
                quantityInput.value = Math.min(5, ++quantityInput.value);
                updateTotal();
            });

            document.getElementById('decrease').addEventListener('click', () => {
                quantityInput.value = Math.max(1, --quantityInput.value);
                updateTotal();
            });

            function updateTotal() {
                totalPrice.textContent = (quantityInput.value * price).toFixed(2) + '€';
            }
        });
    </script>
@endsection
