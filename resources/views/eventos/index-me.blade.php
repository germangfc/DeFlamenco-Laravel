@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
@endphp
@extends('main')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-6">🏢 Mis Eventos</h1>

        @if($eventos->isEmpty())
            <p class="text-center text-gray-500">No has creado eventos aún.</p>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($eventos as $evento)
                    @php
                        $imageSrc = Str::startsWith($evento->foto, 'http')
                                    ? $evento->foto
                                    : asset('storage/images/' . ($evento->foto ?? 'default.jpg'));

                        $isExpired = isset($evento->fecha) && Carbon::parse($evento->fecha)->isPast();
                    @endphp

                    <div class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500
                             {{ $isExpired ? 'bg-gray-200 text-gray-500' : 'bg-base-100' }} h-[400px]">

                        <a href="{{ route('eventos.show', $evento->id) }}" class="block h-full">
                            <div class="h-60 overflow-hidden relative">
                                <img src="{{ $imageSrc }}"
                                     alt="{{ $evento->nombre }}"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105
                                               {{ $isExpired ? 'grayscale brightness-75' : '' }}">

                                <div class="absolute top-4 right-4 badge badge-lg bg-primary glass shadow-lg">
                                    {{ number_format($evento->precio, 2, ',', '.') }}€
                                </div>
                            </div>

                            <div class="p-6 transition-transform duration-500 group-hover:-translate-y-4">
                                <div class="text-sm text-primary mb-2 flex items-center">
                                    <i class="fas fa-calendar-day mr-2"></i>
                                    {{ Carbon::parse($evento->fecha)->translatedFormat('d M Y') }}
                                </div>

                                <h3 class="text-xl font-bold text-base-content mb-3 relative inline-block">
                                    {{ $evento->nombre }}
                                    <span class="absolute bottom-0 left-0 w-0 h-1 bg-primary transition-all duration-300 group-hover:w-full"></span>
                                </h3>

                                <p class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2"></i> {{ $evento->ciudad ?? 'Ubicación desconocida' }}
                                </p>
                            </div>

                            <div class="absolute inset-0 bg-gradient-to-t from-base-100 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-6 flex flex-col justify-end">
                                <p class="text-sm text-base-content line-clamp-3 mb-4">
                                    {{ $evento->descripcion }}
                                </p>

                                @if($isExpired)
                                    <p class="text-sm font-bold text-gray-700">⚠️ Evento Finalizado</p>
                                @else
                                    <p class="text-sm font-semibold text-green-500">✅ Evento Activo</p>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
