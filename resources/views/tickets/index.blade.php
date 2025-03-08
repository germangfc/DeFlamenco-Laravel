@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
@endphp
@extends('main')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-6">🎟️ Mis Entradas</h1>

        @if($tickets->isEmpty())
            <p class="text-center text-gray-500">No tienes entradas compradas.</p>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($tickets as $ticket)
                    @php
                        $imageSrc = Str::startsWith($ticket->evento->foto, 'http')
                                    ? $ticket->evento->foto
                                    : asset('storage/images/' . ($ticket->evento->foto ?? 'default.jpg'));

                        $isExpired = isset($ticket->evento->fecha) && Carbon::parse($ticket->evento->fecha)->isPast();
                    @endphp

                    <div class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500
            {{ $isExpired ? 'bg-gray-200 text-gray-500' : 'bg-base-100' }} h-[400px]">

                        <a href="{{ route('eventos.show', $ticket->evento->id) }}" class="block h-full">
                            <!-- Contenedor de imagen con efecto zoom -->
                            <div class="h-60 overflow-hidden relative">
                                <img src="{{ $imageSrc }}"
                                     alt="{{ $ticket->evento->nombre ?? 'Evento' }}"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105
                         {{ $isExpired ? 'grayscale brightness-75' : '' }}">

                                <!-- Badge de precio flotante -->
                                <div class="absolute top-4 right-4 badge badge-lg bg-primary glass shadow-lg">
                                    {{ number_format($ticket->price, 2, ',', '.') }}€
                                </div>
                            </div>

                            <!-- Contenido con efecto slide-up -->
                            <div class="p-6 transition-transform duration-500 group-hover:-translate-y-4">
                                <!-- Fecha con icono -->
                                <div class="text-sm text-primary mb-2 flex items-center">
                                    <i class="fas fa-calendar-day mr-2"></i>
                                    {{ Carbon::parse($ticket->evento->fecha)->translatedFormat('d M Y') }}
                                </div>

                                <!-- Título con subrayado animado -->
                                <h3 class="text-xl font-bold text-base-content mb-3 relative inline-block">
                                    {{ $ticket->evento->nombre ?? 'Evento no disponible' }}
                                    <span class="absolute bottom-0 left-0 w-0 h-1 bg-primary transition-all duration-300 group-hover:w-full"></span>
                                </h3>

                                <!-- Ciudad y dirección -->
                                <p class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2"></i> {{ $ticket->evento->ciudad ?? 'Ubicación desconocida' }}
                                </p>
                                <p class="text-sm text-gray-600 flex items-center mt-1">
                                    <i class="fas fa-map-pin mr-2"></i> {{ $ticket->evento->direccion ?? 'Dirección no disponible' }}
                                </p>
                            </div>

                            <!-- Efecto hover: detalles adicionales -->
                            <div class="absolute inset-0 bg-gradient-to-t from-base-100 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-6 flex flex-col justify-end">
                                @if($isExpired)
                                    <p class="text-sm font-bold text-gray-700">⚠️ Ticket Expirado</p>
                                @else
                                    <p class="text-sm font-semibold {{ $ticket->isReturned ? 'text-red-500' : 'text-green-500' }}">
                                        {{ $ticket->isReturned ? '❌ Entrada Devuelta' : '✅ Entrada Válida' }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

        @endif
    </div>
@endsection
