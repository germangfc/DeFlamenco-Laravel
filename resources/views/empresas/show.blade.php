@extends("main")


@section('content')
    <!-- Vista empresas.show -->
    <div class="container mx-auto px-4 md:px-8 py-12">
        <!-- Header de la empresa -->
        <div class="mb-12 text-center">
            <h1 class="text-4xl font-bol mb-4">Eventos de {{ $empresa->name }}</h1>
            <div class="flex items-center justify-center gap-4">
                <p class="badge badge-lg bg-accent text-white">
                    <i class="fas fa-phone mr-2"></i>{{ $empresa->telefono }}
                </p>
                <p class="badge badge-lg bg-secondary text-white">
                    <i class="fas fa-map-marker-alt mr-2"></i>{{ $empresa->direccion }}
                </p>
            </div>
        </div>
        <!-- Grid de eventos (mismo estilo que empresas) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 ">
            @foreach($eventos as $evento)
                <div class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 bg-base-100 h-[400px]">
                    <a href="{{ route('eventos.show', $evento->id) }}" class="block h-full">
                        <!-- Contenedor de imagen con efecto zoom -->
                        <div class="h-60 overflow-hidden relative ">
                            <img
                                src="{{ Str::startsWith($evento->foto, 'http') ? $evento->foto : asset('storage/images/' . $evento->foto) }}"
                                alt="{{ $evento->nombre }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            >
                            <!-- Badge de precio flotante -->
                            <div class="absolute top-4 right-4 badge badge-lg bg-primary glass shadow-lg">
                                {{ $evento->precio }}€
                            </div>
                        </div>

                        <!-- Contenido con efecto slide-up -->
                        <div class="p-6 transition-transform duration-500 group-hover:-translate-y-4">
                            <!-- Fecha en pequeño -->
                            <div class="text-sm text-primary mb-2 flex items-center">
                                <i class="fas fa-calendar-day mr-2"></i>
                                {{ \Carbon\Carbon::parse($evento->fecha)->format('d M Y') }}
                            </div>

                            <!-- Título con subrayado animado -->
                            <h3 class="text-xl font-bold text-base-content mb-3 relative inline-block">
                                {{ $evento->nombre }}
                                <span class="absolute bottom-0 left-0 w-0 h-1 bg-primary transition-all duration-300 group-hover:w-full"></span>
                            </h3>

                        </div>

                        <!-- Efecto hover: Detalles que aparecen -->
                        <div class="absolute inset-0 bg-gradient-to-t from-base-100 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-6 flex flex-col justify-end">
                            <p class="text-sm text-base-content line-clamp-3 mb-4">
                                {{ $evento->descripcion }}
                            </p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        @if($eventos->count() > 0)
            <div class="flex justify-center mt-8 space-x-2">
                @if (!$eventos->onFirstPage())
                    <a href="{{ $eventos->previousPageUrl() }}" class="btn btn-square">«</a>
                @endif

                @for ($i = max(1, $eventos->currentPage() - 1); $i <= min($eventos->lastPage(), $eventos->currentPage() + 1); $i++)
                    <a href="{{ $eventos->url($i) }}" class="btn btn-square {{ $i == $eventos->currentPage() ? 'btn-active' : '' }}">
                        {{ $i }}
                    </a>
                @endfor

                @if ($eventos->hasMorePages())
                    <a href="{{ $eventos->nextPageUrl() }}" class="btn btn-square">»</a>
                @endif
            </div>
        @else
            <p class="text-center mt-8">No se han encontrado eventos.</p>
        @endif
    </div>

@endsection

