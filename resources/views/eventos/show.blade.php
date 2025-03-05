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
                <div class="w-full text-center px-6 mt-4">
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
        <div class="w-full flex flex-col md:flex-row justify-between">
            <div class="flex-1 p-6 text-left">
                <h3 class="text-2xl font-semibold mb-6">Información del Evento</h3>
                <p class="mb-4 text-lg leading-relaxed">
                    <span class="hidden-text">El mayor evento de música flamenca de 2025 en España 🔥</span><br>
                    <span class="hidden-text">Un line up inmejorable con los mejores artistas y DJs de la escena repartidos en 4 áreas musicales 🎵</span><br>
                    <span class="hidden-text">El ambientazo que solo encuentras en {{ $evento->nombre }} con una fiesta non-stop de más de 6 horas 🕺🏼</span><br>
                    <span class="hidden-text">Todas las fiestas de {{ $evento->nombre }} en exclusiva en De Flamenco 🎁</span>
                </p>


                <h3 class="text-2xl font-semibold mb-4">Detalles del Evento</h3>
                <p class="text-lg"><strong>📅 Fecha:</strong> {{ $evento->fecha }} a las {{ $evento->hora }}</p>
                <p class="text-lg"><strong>📍 Lugar:</strong> {{ $evento->direccion }}, {{ $evento->ciudad }}</p>
                <p class="text-lg font-bold"><strong>Precio:</strong> {{ $evento->precio }}€</p>
            </div>

            <div class="p-10 w-full md:w-auto flex flex-col items-center text-white rounded-xl shadow-lg">
                <h3 class="text-sm font-bold text-center border-2 border-blue-500 bg-blue-600 text-white py-1 px-4 rounded-lg w-full shadow-md">
                    {{ $evento->fecha }}
                </h3>

                <p class="mt-3 text-lg font-medium text-center">Entrada general para <strong>{{ $evento->nombre }}</strong></p>

                <form action="{{ route('cart.add') }}" method="POST" class="w-full mt-6">
                    @csrf
                    <input type="hidden" name="idEvent" value="{{ $evento->id }}">
                    <input type="hidden" name="price" value="{{ $evento->precio }}">

                    <div class="flex items-center justify-center space-x-4 mb-6">
                        <button type="button" id="decrease" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition shadow-md">-</button>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" class="w-16 text-center border rounded-lg text-lg font-semibold bg-gray-800 text-white shadow-md">
                        <button type="button" id="increase" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition shadow-md">+</button>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold text-lg shadow-lg hover:bg-blue-700 transition transform hover:scale-105">
                        Comprar ahora - <span id="totalPrice">{{ $evento->precio }}€</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-8">
        <h3 class="text-xl font-semibold mb-3 text-center">Ubicación en el mapa</h3>
        <div id="map" class="w-full h-64 rounded-lg shadow-md"></div>
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
