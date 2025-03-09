@php
    use Illuminate\Support\Str;
@endphp

@extends('main')

@section('content')
    <style>
        /* Estilos personalizados esenciales */
        .event-hero {
            @apply relative h-[80vh] overflow-hidden mb-16 mt-20 rounded-xl;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
            url('{{ Str::startsWith($evento->foto, 'http') ? $evento->foto : asset('storage/images/' . $evento->foto) }}');
            background-size: cover;
            background-position: center;
        }

        .hero-parallax {
            @apply absolute top-0 left-0 w-full h-[120%] bg-cover bg-center;
            transform: translateZ(0);
            will-change: transform;
        }

        .event-title {
            @apply font-bold text-6xl md:text-7xl leading-tight mb-6;
            font-family: 'Oswald', sans-serif;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
    </style>

    <div class="event-hero relative mt-16 min-h-[80vh] flex flex-col justify-center overflow-hidden mb-16 rounded-xl" id="heroParallax">
        <div class="hero-parallax"></div>

        <div class="navigation-arrows absolute top-1/2 transform w-full flex justify-between px-8 z-20">
            @if($eventoAnterior)
                <a href="{{ route('eventos.show', $eventoAnterior->id) }}"
                   class="transition duration-300 ease-in-out backdrop-blur-sm bg-white/10 hover:bg-white/20 transform hover:scale-110 rounded-full w-12 h-12 flex items-center justify-center cursor-pointer border border-white/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
            @endif

            @if($eventoSiguiente)
                <a href="{{ route('eventos.show', $eventoSiguiente->id) }}"
                   class="transition duration-300 ease-in-out backdrop-blur-sm bg-white/10 hover:bg-white/20 transform hover:scale-110 rounded-full w-12 h-12 flex items-center justify-center cursor-pointer border border-white/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            @endif
        </div>

        <div class="details-container relative z-10 p-8 md:p-16 text-white">
            <div class="mb-8 space-y-6">
                <div class="text-xl mb-2 relative pl-12">
                    <div class="absolute left-0 top-1/2 w-8 h-1 bg-white"></div>
                    {{ $evento->ciudad }}
                </div>
                <h1 class="event-title text-2xl">{{ $evento->nombre }}</h1>
            </div>

            <div class="grid gap-4 mb-12">
                <div class="flex items-center gap-4 py-3 border-b border-white/10 hover:bg-white/5 transition px-2">
                    <span class="text-2xl">📅</span>
                    <div class="space-y-1">
                        <p class="font-semibold text-lg">{{ $evento->fecha }}</p>
                        <p class="opacity-80">{{ $evento->hora }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 py-3 border-b border-white/10 hover:bg-white/5 transition px-2">
                    <span class="text-2xl">📍</span>
                    <div class="space-y-1">
                        <p class="font-semibold text-lg">{{ $evento->direccion }}</p>
                        <p class="opacity-80">{{ $evento->ciudad }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 py-3 border-b border-white/10 hover:bg-white/5 transition px-2">
                    <span class="text-2xl">💶</span>
                    <div class="space-y-1">
                        <p class="font-semibold text-lg">{{ $evento->precio }}€</p>
                        <p class="opacity-80">Por persona</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-8">
                @if(!Auth::check() || (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('empresa')))
                    <div class="ticket-form max-w-md bg-white/10 backdrop-blur-xl rounded-xl p-8 border border-white/20 shadow-2xl">
                        <form action="{{ route('cart.add') }}" method="POST" class="space-y-6">
                            @csrf
                            <input type="hidden" name="idEvent" value="{{ $evento->id }}">
                            <input type="hidden" name="price" value="{{ $evento->precio }}">
                            <input type="hidden" name="name" value="{{ $evento->nombre }}">

                            <div class="quantity-selector flex items-center justify-center gap-4">
                                <button type="button" id="decrease"
                                        class="w-12 h-12 rounded-full bg-primary hover:bg-accent text-white text-2xl flex items-center justify-center transition-all">
                                    -
                                </button>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" max="5"
                                       class="w-20 text-center bg-white/5 border border-white/20 rounded-xl py-3 text-xl font-bold">
                                <button type="button" id="increase"
                                        class="w-12 h-12 rounded-full bg-primary hover:bg-accent text-white text-2xl flex items-center justify-center transition-all">
                                    +
                                </button>
                            </div>

                            <button type="submit"
                                    class="w-full py-4 bg-primary hover:bg-accent text-white font-bold rounded-xl transition-all transform hover:scale-[1.02] shadow-lg hover:shadow-pink-700/20">
                                COMPRAR - <span id="totalPrice">{{ $evento->precio }}</span>€
                            </button>
                        </form>
                    </div>
                @endif

                <div class="event-description flex-1 bg-white/10 backdrop-blur-xl rounded-xl p-8 border border-white/20 shadow-2xl">
                    <h3 class="text-xl font-bold mb-4">Descripción del Evento</h3>
                    <p>{{ $evento->descripcion }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="map-container w-full mx-auto mb-24 rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
        <div id="map" class="w-full h-96"></div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Parallax mejorado
            const hero = document.querySelector('.hero-parallax');
            window.addEventListener('scroll', () => {
                const scrollPosition = window.scrollY;
                hero.style.transform = `translate3d(0, ${scrollPosition * 0.2}px, 0)`;
                hero.style.opacity = 1 - Math.min(scrollPosition / 300, 0.3);
            });

            // Mapa
            const address = '{{ $evento->ciudad }} {{ $evento->direccion }}';
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        const map = L.map('map').setView([data[0].lat, data[0].lon], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

                        L.marker([data[0].lat, data[0].lon]).addTo(map)
                            .bindPopup(`
                                <div class="space-y-2 p-2">
                                    <h3 class="font-bold text-lg">{{ $evento->nombre }}</h3>
                                    <p class="text-sm text-gray-600">{{ $evento->direccion }}</p>
                                </div>
                            `)
                            .openPopup();
                    }
                });

            // Controlador de cantidad
            const quantity = document.getElementById('quantity');
            const totalPrice = document.getElementById('totalPrice');
            const price = {{ $evento->precio }};

            const updateTotal = () => {
                totalPrice.textContent = (quantity.value * price).toFixed(2);
                quantity.dispatchEvent(new Event('change'));
            };

            document.getElementById('increase').addEventListener('click', () => {
                quantity.value = Math.min(5, ++quantity.value);
                updateTotal();
            });

            document.getElementById('decrease').addEventListener('click', () => {
                quantity.value = Math.max(1, --quantity.value);
                updateTotal();
            });
        });
    </script>
@endsection
