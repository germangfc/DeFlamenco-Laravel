@php
    use Illuminate\Support\Str;
@endphp
@extends('main')

@section("content")
    <div class="flex justify-center items-center min-h-screen">
        <div class=" shadow-2xl rounded-3xl overflow-hidden max-w-3xl w-full">

            <div class="relative group overflow-hidden">
                <img class="object-cover h-120 w-full rounded-t-3xl transition-transform duration-500 group-hover:scale-110"
                     src='{{ Str::startsWith($evento->foto, 'http') ? $evento->foto : asset("storage/images/" . $evento->foto) }}' alt="Evento" />
                <div class="absolute inset-0 bg-black/50 rounded-t-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <h2 class="absolute bottom-6 left-6 text-white text-4xl font-extrabold uppercase text-center drop-shadow-xl ">
                    {{ $evento->nombre }}
                </h2>
            </div>

            <div class="p-8 ">
                <div class="text-lg space-y-6 leading-relaxed">
                    <p class="flex items-center gap-2 ">
                        <span class="text-emerald-500 text-2xl">📅</span>
                        <span class="font-semibold ">Fecha:</span>
                        <span class="">{{ $evento->fecha }}</span>
                    </p>

                    <p class="flex items-center gap-2 ">
                        <span class="text-emerald-500 text-2xl">⏰</span>
                        <span class="font-semibold ">Hora:</span>
                        <span class="">{{ $evento->hora }}</span>
                    </p>

                    <p class="flex items-center gap-2 ">
                        <span class="text-emerald-500 text-2xl">📍</span>
                        <span class="font-semibold ">Dirección:</span>
                        <span class="">{{ $evento->direccion }}</span>
                    </p>

                    <p class="flex items-center gap-2 ">
                        <span class="text-emerald-500 text-2xl">🌆</span>
                        <span class="font-semibold ">Ciudad:</span>
                        <span class="">{{ $evento->ciudad }}</span>
                    </p>

                    <p class="text-2xl font-bold text-emerald-500 mt-6 flex items-center gap-2 ">
                        <span class="text-3xl">💰</span>
                        Precio: <span class="">${{ $evento->precio }}</span>
                    </p>
                </div>

                <div class="flex justify-between items-center mt-10">
                    <a href="{{ route('eventos') }}" class="px-6 py-3 rounded-full bg-emerald-600 text-white text-lg font-semibold
                        hover:bg-emerald-700 transition-all duration-300 transform hover:scale-105 shadow-md flex items-center gap-2">
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
