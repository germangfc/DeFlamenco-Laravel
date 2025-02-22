@php
    use Illuminate\Support\Str;
@endphp

@extends('main')

@section("content")
    <div class="min-h-screen w-full">
        <div class="flex items-center justify-center w-full py-12">
            <div class="max-w-6xl w-full flex flex-col md:flex-row items-center space-y-8 md:space-y-0">
                <div class="w-full md:w-1/3">
                    <div class="overflow-hidden relative group">
                        <img class="object-cover w-full h-72 rounded-xl transition-transform duration-500 transform group-hover:scale-110 cursor-pointer"
                             src='{{ Str::startsWith($evento->foto, 'http') ? $evento->foto : asset("storage/images/" . $evento->foto) }}'
                             alt="Evento" />
                    </div>
                </div>

                <div class="w-full md:w-2/3 text-center md:text-left px-6">
                    <h2 class="text-4xl md:text-5xl font-bold leading-tight mb-4">
                        {{ $evento->nombre }}
                    </h2>
                    <p class="text-lg font-medium">¡No te pierdas este increíble evento!</p>
                </div>
            </div>
        </div>

        <!-- Parte inferior: Información del evento -->
        <div class="py-12 px-6 md:px-12">
            <div class="max-w-4xl mx-auto space-y-8">
                <!-- Fecha, Hora, Precio, etc -->
                <div class="flex items-center gap-4 text-xl">
                    <span class="text-xl">📅</span>
                    <span class="font-semibold">Fecha:</span>
                    <span>{{ $evento->fecha }}</span>
                </div>

                <div class="flex items-center gap-4 text-xl">
                    <span class="text-xl">⏰</span>
                    <span class="font-semibold">Hora:</span>
                    <span>{{ $evento->hora }}</span>
                </div>

                <div class="flex items-center gap-4 text-xl">
                    <span class="text-xl">📍</span>
                    <span class="font-semibold">Dirección:</span>
                    <span>{{ $evento->direccion }}</span>
                </div>

                <div class="flex items-center gap-4 text-xl">
                    <span class="text-xl">🌆</span>
                    <span class="font-semibold">Ciudad:</span>
                    <span>{{ $evento->ciudad }}</span>
                </div>

                <div class="flex items-center gap-4 text-2xl font-bold">
                    <span class="text-xl">💰</span>
                    Precio: <span>${{ $evento->precio }}</span>
                </div>

                <div class="mt-12">
                    <h3 class="text-2xl font-semibold mb-4">Ubicación del Evento</h3>

                    <div id="map" class="w-full h-96"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Leaflet.js (OpenStreetMap) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        // Dirección del evento (puedes cambiarlo por cualquier dirección que tengas en tu base de datos)
        var address = '{{ $evento->ciudad . ' ' . $evento->direccion }}'; // Puedes cambiar 'ciudad' por cualquier campo de la base de datos

        // Obtener las coordenadas usando la API de Nominatim (OpenStreetMap)
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    var lat = data[0].lat;
                    var lon = data[0].lon;

                    var map = L.map('map').setView([lat, lon], 13);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    var marker = L.marker([lat, lon]).addTo(map);
                    marker.bindPopup('<b>{{ $evento->nombre }}</b><br>{{ $evento->direccion }}');
                } else {
                    alert('No se pudo encontrar la ubicación del evento.');
                }
            })
    </script>
@endsection
