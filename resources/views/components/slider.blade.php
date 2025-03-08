@php
    use App\Models\Evento;
    $eventos = Evento::recientes()->get();
@endphp
<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
<div class="w-full max-w-6xl">
    <h1 class="text-2xl font-bold mb-6">Nuestras recomendaciones</h1>
    <div class="relative">
        <!-- Botón Izquierda: ocupa full height -->
        <button
            class="group absolute left-0 top-0 bottom-0 z-10 px-2 bg-black/0 text-white focus:outline-none"
            onclick="scrollSliderLeft()"
        >
            <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <!-- Contenedor principal del slider -->
        <div id="slider" class="flex overflow-x-auto scroll-smooth space-x-4 px-8 py-2 no-scrollbar">
            @foreach($eventos as $evento)
                <div class="flex-shrink-0">
                    <!-- Enlace a la vista detallada del evento -->
                    <a href="{{ route('eventos.show', $evento->id) }}">
                        <!-- Carta del evento -->
                        <div class="relative group w-40 md:w-48 lg:w-56 h-64">
                            <img
                                src="{{ Str::startsWith($evento->foto, 'http') ? $evento->foto : asset('storage/images/' . $evento->foto) }}"
                                alt="{{ $evento->nombre }}"
                                class="w-full h-full object-cover"
                            >
                            <!-- Overlay para mostrar la descripción en hover -->
                            <div class="absolute inset-0 flamenco-dark:group-hover:bg-gray-900/90 flamenco-light:group-hover:bg-gray-900/90 transition duration-300 flex items-center justify-center p-2">
                                <p class="opacity-0 group-hover:opacity-100 transition duration-300 text-sm text-start flamenco-light:text-white whitespace-normal break-words">
                                    {{ $evento->descripcion }}
                                </p>
                            </div>
                        </div>
                    </a>
                    <!-- Título del evento -->
                    <div class="mt-2 text-start text-sm opacity-75 font-semibold">
                        {{ $evento->nombre }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Botón Derecha: ocupa full height -->
        <button
            class="group absolute right-0 top-0 bottom-0 z-10 px-2 bg-black/0 text-white focus:outline-none"
            onclick="scrollSliderRight()"
        >
            <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>
</div>

<!-- JavaScript para el scroll horizontal -->
<script>
    const SCROLL_AMOUNT = 300;
    const slider = document.getElementById('slider');

    function scrollSliderLeft() {
        slider.scrollBy({ top: 0, left: -SCROLL_AMOUNT, behavior: 'smooth' });
    }

    function scrollSliderRight() {
        slider.scrollBy({ top: 0, left: SCROLL_AMOUNT, behavior: 'smooth' });
    }
</script>
