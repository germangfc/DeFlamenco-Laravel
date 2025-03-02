@php
    use Illuminate\Support\Str;
@endphp

@extends('main')

@section("content")
    <div class="min-h-screen w-full relative flex flex-col items-center py-10">

        <div class="w-full max-w-6xl flex justify-between items-center px-4 mb-6">
            @if ($eventoAnterior)
                <a href="{{ route('eventos.show', $eventoAnterior->id) }}"
                   class="text-3xl transition duration-300">
                    ⬅
                </a>
            @endif

            <div class="flex-1 flex justify-center">
                <img class="object-cover w-72 h-72 rounded-xl"
                     src='{{ Str::startsWith($evento->foto, "http") ? $evento->foto : asset("storage/images/" . $evento->foto) }}'
                     alt="Evento" />
            </div>

            @if ($eventoSiguiente)
                <a href="{{ route('eventos.show', $eventoSiguiente->id) }}"
                   class="text-3xl transition duration-300">
                    ➡
                </a>
            @endif
        </div>


        <div class="max-w-5xl w-full ">

            <div class="p-6">
                <!-- Contenedor principal con flex para la información y el formulario -->
                <div class="flex flex-col md:flex-row space-y-6 md:space-y-0 md:space-x-12 text-center md:text-left">

                    <!-- Información del evento -->
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 justify-center md:justify-start">
                            <span class="text-2xl"></span>
                            <p class="text-lg"><strong>Fecha:</strong> {{ $evento->fecha }}</p>
                        </div>

                        <div class="flex items-center space-x-3 justify-center md:justify-start">
                            <span class="text-2xl"></span>
                            <p class="text-lg"><strong>Hora:</strong> {{ $evento->hora }}</p>
                        </div>

                        <div class="flex items-center space-x-3 justify-center md:justify-start">
                            <span class="text-2xl"></span>
                            <p class="text-lg"><strong>Ubicación:</strong> {{ $evento->direccion }}, {{ $evento->ciudad }}</p>
                        </div>

                        <div class="flex items-center space-x-3 justify-center md:justify-start font-bold">
                            <span class="text-2xl"></span>
                            <p class="text-lg">Precio: ${{ $evento->precio }}</p>
                        </div>
                    </div>

                    <div class="md:w-1/3 p-6 border rounded-lg shadow-lg text-center">
                        <h3 class="text-lg font-semibold mb-3">Cantidad</h3>
                        <form action="{{ route('cart.add') }}" method="POST" class="w-full">
                            @csrf
                            <input type="hidden" name="idEvent" value="{{ $evento->id }}">
                            <input type="hidden" name="price" value="{{ $evento->precio }}">

                            <div class="flex items-center justify-center space-x-4 mb-4">
                                <button type="button" id="decrease" class="px-3 py-1">-</button>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" class="w-16 text-center border rounded-lg">
                                <button type="button" id="increase" class="px-3 py-1 ">+</button>
                            </div>

                            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold">
                                Comprar ahora $<span id="totalPrice">{{ $evento->precio }}</span>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-xl font-semibold mb-3 text-center">Ubicación en el mapa</h3>
                    <div id="map" class="w-full h-64 rounded-lg shadow-md"></div>
                </div>
            </div>
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
