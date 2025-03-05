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

                    <div class="rounded-lg overflow-hidden shadow-lg
                        {{ $isExpired ? 'bg-gray-200 text-gray-500' : 'bg-white' }}">

                        <img src="{{ $imageSrc }}"
                             alt="{{ $ticket->evento->nombre ?? 'Evento' }}"
                             class="w-full h-64 object-cover object-center transition
                             {{ $isExpired ? 'grayscale brightness-75' : '' }}">


                        <div class="p-4">
                            <h2 class="text-xl font-semibold">{{ $ticket->evento->nombre ?? 'Evento no disponible' }}</h2>
                            <p>{{ $ticket->evento->ciudad ?? 'Ubicación desconocida' }}</p>
                            <p class="mt-2">
                                📅 {{ date('d M Y', strtotime($ticket->evento->fecha ?? now())) }}
                                ⏰ {{ date('H:i', strtotime($ticket->evento->hora ?? '00:00')) }}
                            </p>
                            <p>📍 {{ $ticket->evento->direccion ?? 'Dirección no disponible' }}</p>
                            <p class="text-lg font-bold mt-2">💶 {{ number_format($ticket->price, 2, ',', '.') }} €</p>

                            @if($isExpired)
                                <p class="mt-3 text-sm font-bold text-gray-700">⚠️ Caducada</p>
                            @else
                                <p class="mt-3 text-sm font-semibold {{ $ticket->isReturned ? 'text-red-500' : 'text-green-500' }}">
                                    {{ $ticket->isReturned ? '❌ Entrada Devuelta' : '✅ Entrada Válida' }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
