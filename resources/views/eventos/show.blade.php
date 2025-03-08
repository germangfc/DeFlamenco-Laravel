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

    <div class="mt-6 text-center">
        <div class=" p-4 rounded-lg inline-block shadow-lg">
            <div id="countdown" class="text-2xl font-semibold">
                <ul class="list-none flex justify-center gap-8">
                    <li><span id="days"></span> DIAS</li>
                    <li><span id="hours"></span> HORAS</li>
                    <li><span id="minutes"></span> MINUTOS</li>
                    <li><span id="seconds"></span> SEGUNDOS</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="p-6 mb-8 rounded-lg flex flex-col md:flex-row justify-between">
        <!-- Fila de detalles -->
        <div class="flex-1 p-6 text-left">
            <h3 class="text-2xl font-semibold mb-6 text-center text-blue-600">Información del Evento</h3>

            <p class="mb-6 text-lg leading-relaxed text-gray-700">
                {{$evento->descripcion}}
            </p>

            <h3 class="text-2xl font-semibold mb-6 text-center text-blue-600">Detalles del Evento</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-lg shadow-md border border-gray-200">
                    <p class="text-lg font-medium text-gray-800"><strong>📅 Fecha:</strong> {{ \Carbon\Carbon::parse($evento->fecha)->format('d M, Y') }} a las {{ $evento->hora }}</p>
                </div>

                <div class="bg-white p-4 rounded-lg shadow-md border border-gray-200">
                    <p class="text-lg font-medium text-gray-800"><strong>📍 Lugar:</strong> {{ $evento->direccion }}, {{ $evento->ciudad }}</p>
                </div>

                <div class="bg-white p-4 rounded-lg shadow-md border border-gray-200 col-span-2">
                    <p class="text-lg font-medium text-gray-800"><strong>💰 Precio:</strong> {{ $evento->precio }}€</p>
                </div>
            </div>
        </div>

        <!-- Opción de añadir al carrito solo si es cliente -->
        @if(!Auth::check() || (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('empresa')))
            <div class="flex-1 p-4 ml-auto  md:w-1/12"> <!-- Reducción del tamaño de la columna -->
                <div class="relative">
                    <div class="p-4 w-full md:w-auto flex flex-col items-center text-white rounded-xl shadow-lg md:ml-6">
                        <h3 class="text-xs font-bold text-center border-2 bg-primary border-primary text-white py-1 px-4 rounded-lg w-full shadow-md">
                            Añadir a la cesta
                        </h3>

                        <form action="{{ route('cart.add') }}" method="POST" class="w-full mt-4">
                            @csrf

                            <input type="hidden" name="idEvent" value="{{ $evento->id }}">
                            <input type="hidden" name="price" value="{{ $evento->precio }}">
                            <input type="hidden" name="name" value="{{ $evento->nombre }}">

                            <div class="flex items-center justify-center space-x-4 mb-4">
                                <button type="button" id="decrease" class="px-3 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition shadow-md">-</button>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" max="5" class="w-12 text-center border rounded-lg text-lg font-semibold bg-gray-800 text-white shadow-md">
                                <button type="button" id="increase" class="px-3 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition shadow-md">+</button>
                            </div>

                            <x-primary-button class="ml-4 w-full bg-primary text-primary-content py-2 rounded-lg font-bold text-lg shadow-lg hover:bg-primary-focus transition transform hover:scale-105 text-center">
                                {{ __('Añadir al carrito') }} <span id="totalPrice">  {{ $evento->precio }} €</span>
                            </x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

    </div>


    <div class="w-full mt-8 mb-8">
        <h3 class="text-xl font-semibold mb-3 text-center">Ubicación en el mapa</h3>
        <div id="map" class="w-full h-64 rounded-lg shadow-md"></div>
    </div>


    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css " />

@endsection

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
        increaseBtn.addEventListener("click", function () {quantityInput.value = parseInt(quantityInput.value) + 1;
            updateTotal();
        });
        decreaseBtn.addEventListener("click", function () {if (parseInt(quantityInput.value) > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
            updateTotal();
        }
        });

        function updateTotal() {
            totalPrice.textContent = (quantityInput.value * price).toFixed(2);
        }
        const eventDate = "{{ \Carbon\Carbon::parse($evento->fecha)->format('M d, Y H:i') }}"; // Usamos la fecha del evento
        const eventTimestamp = new Date(eventDate).getTime();

        const second = 1000,
            minute = second * 60,
            hour = minute * 60,
            day = hour * 24;

        const countdown = setInterval(function() {
            const now = new Date().getTime(),
                distance = eventTimestamp - now;

            document.getElementById("days").innerText = Math.floor(distance / (day));
            document.getElementById("hours").innerText = Math.floor((distance % (day)) / (hour));
            document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute));
            document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);

            // Al llegar a la fecha, cambiar el mensaje
            if (distance < 0) {
                document.getElementById("headline").innerText = "¡Es el día del evento!";
                document.getElementById("countdown").style.display = "none";
                clearInterval(countdown);
            }
        }, 0);
    });
</script>
