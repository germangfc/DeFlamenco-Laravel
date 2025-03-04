@php
    use Illuminate\Support\Str;
@endphp

@extends('main')

@section("content")
    <div class="w-full max-w-6xl flex flex-col items-center px-4 mb-6 mt-12">
        <div class="flex w-full justify-between items-center">
            @if ($eventoAnterior)
                <a href="{{ route('eventos.show', $eventoAnterior->id) }}"
                   class="text-3xl transition duration-300">
                    ⬅
                </a>
            @else
                <div class="w-8"></div>
            @endif

            <div class="flex-1 flex flex-col items-center">
                <img class="object-cover w-72 h-72 rounded-xl"
                     src='{{ Str::startsWith($evento->foto, "http") ? $evento->foto : asset("storage/images/" . $evento->foto) }}'
                     alt="Evento" />
                <div class="w-full md:w-2/3 text-center md:text-left px-6 mt-4">
                    <h2 class="text-4xl md:text-5xl font-bold leading-tight mb-4">
                        {{ $evento->nombre }}
                    </h2>
                </div>
            </div>

            @if ($eventoSiguiente)
                <a href="{{ route('eventos.show', $eventoSiguiente->id) }}"
                   class="text-3xl transition duration-300">
                    ➡
                </a>
            @else
                <div class="w-8"></div>
            @endif
        </div>
    </div>

    <div class="p-6 mb-8 rounded-lg">
        <div class="max-w-5xl w-full mt-8">
            <div class="flex flex-col md:flex-row space-y-6 md:space-y-0 md:space-x-12 text-center md:text-left">
                <div class="flex-1 p-6">
                    <h3 class="text-2xl font-semibold mb-6">Información del Evento</h3>
                    <p class="flex items-center space-x-3 justify-start mb-4">
                        El mayor evento de música flamenca de 2025 en España 🔥
                        Un line up inmejorable con los mejores artistas y djs de la escena repartidos en 4 áreas musicales 🎵
                        El ambientazo que solo encuentras en {{ $evento->nombre }} con una fiesta non stop de más de 6 horas 🕺🏼
                        Todas las fiestas de {{ $evento->nombre }} en exclusiva en De Flamenco 🎁  <br>
                    </p>

                    <h3 class="text-2xl font-semibold mb-4">Detalles del Evento</h3>
                    <div class="flex items-center space-x-3 justify-start mb-4">
                        <p class="text-lg">
                            <strong>📅 Fecha:</strong> <span role="img" aria-label="calendar"</span> El {{ $evento->fecha }} a las {{ $evento->hora }}
                        </p>
                    </div>

                    <div class="flex items-center space-x-3 justify-start mb-4">
                        <p class="text-lg">
                            <strong>📍 Lugar:</strong> <span role="img" aria-label="location"></span> {{ $evento->direccion }}, {{ $evento->ciudad }}
                        </p>
                    </div>

                    <div class="flex items-center space-x-3 justify-start mb-4 font-bold">
                        <p class="text-lg">
                            <strong>Precio:</strong> <span role="img" aria-label="money"></span> ${{ $evento->precio }}
                        </p>
                    </div>
                </div>

                <div class="flex-1 p-10 ml-auto w-full md:w-1/2">
                    <div class="relative">
                        <h3 class="text-lg font-semibold mt-6 text-center border-2 border-blue-600 bg-blue-600 text-white py-2 px-4 rounded-lg">
                            {{ $evento->fecha }}
                        </h3>

                        <p class="mt-2 font-medium text-center">Entrada general para <strong>{{ $evento->nombre }}</strong></p>

                        <form action="{{ route('cart.add') }}" method="POST" class="w-full mt-4">
                            @csrf
                            <input type="hidden" name="idEvent" value="{{ $evento->id }}">
                            <input type="hidden" name="price" value="{{ $evento->precio }}">

                            <div class="flex items-center justify-center space-x-4 mb-4">
                                <button type="button" id="decrease" class="px-4 py-2">
                                    -
                                </button>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" class="w-16 text-center border rounded-lg text-lg font-semibold">
                                <button type="button" id="increase" class="px-4 py-2">
                                    +
                                </button>
                            </div>

                            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold text-lg shadow-md hover:bg-blue-700 transition">
                                Comprar ahora - $<span id="totalPrice">{{ $evento->precio }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8">
            <h3 class="text-xl font-semibold mb-3 text-center">Ubicación en el mapa</h3>
            <div id="map" class="w-full h-64 rounded-lg shadow-md"></div>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var address = '{{ $evento->ciudad . ' ' . $evento->direccion }}';

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        var lat = data[0].lat;
                        var lon = data[0].lon;

                        var map = L.map('map').setView([lat, lon], 13);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        }).addTo(map);

                        var marker = L.marker([lat, lon]).addTo(map);
                        marker.bindPopup('<b>{{ $evento->nombre }}</b><br>{{ $evento->direccion }}');
                    } else {
                        document.getElementById("map").innerHTML = "<p class='text-red-500 text-center'>No se pudo encontrar la ubicación.</p>";
                    }
                });
        });

        document.addEventListener("DOMContentLoaded", function () {
            const quantityInput = document.getElementById("quantity");
            const totalPrice = document.getElementById("totalPrice");
            const price = {{ $evento->precio }};
            const increaseBtn = document.getElementById("increase");
            const decreaseBtn = document.getElementById("decrease");

            increaseBtn.addEventListener("click", function () {
                quantityInput.value = parseInt(quantityInput.value) + 1;
                updateTotal();
            });

            decreaseBtn.addEventListener("click", function () {
                if (parseInt(quantityInput.value) > 1) {
                    quantityInput.value = parseInt(quantityInput.value) - 1;
                    updateTotal();
                }
            });

            function updateTotal() {
                totalPrice.textContent = (quantityInput.value * price).toFixed(2);
            }
        });
    </script>
@endsection
