@extends('main')

@section("content")
    <div class="flex justify-center items-center min-h-screen bg-gradient-to-br from-gray-900 to-gray-800 px-6">
        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden max-w-3xl w-full transform transition-all duration-500 hover:scale-[1.03] hover:shadow-4xl">

            <!-- Imagen con overlay -->
            <div class="relative group">
                <img class="object-cover h-80 w-full rounded-t-3xl transition-transform duration-500 group-hover:scale-110"
                     src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp" alt="Evento" />
                <div class="absolute inset-0 bg-black/50 rounded-t-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <h2 class="absolute bottom-6 left-6 text-white text-4xl font-extrabold drop-shadow-xl transition-transform duration-500 group-hover:translate-y-2">
                    {{ $evento->nombre }}
                </h2>
            </div>

            <div class="p-8 text-gray-800">
                <!-- Detalles del evento con iconos y efectos -->
                <div class="text-lg space-y-6 leading-relaxed">
                    <p class="flex items-center gap-2 transition-transform duration-300 hover:translate-x-2">
                        <span class="text-indigo-600 text-2xl">📅</span>
                        <span class="font-semibold text-gray-900">Fecha:</span>
                        <span class="text-gray-700">{{ $evento->fecha }}</span>
                    </p>

                    <p class="flex items-center gap-2 transition-transform duration-300 hover:translate-x-2">
                        <span class="text-indigo-600 text-2xl">⏰</span>
                        <span class="font-semibold text-gray-900">Hora:</span>
                        <span class="text-gray-700">{{ $evento->hora }}</span>
                    </p>

                    <p class="flex items-center gap-2 transition-transform duration-300 hover:translate-x-2">
                        <span class="text-indigo-600 text-2xl">📍</span>
                        <span class="font-semibold text-gray-900">Dirección:</span>
                        <span class="text-gray-700">{{ $evento->direccion }}</span>
                    </p>

                    <p class="flex items-center gap-2 transition-transform duration-300 hover:translate-x-2">
                        <span class="text-indigo-600 text-2xl">🌆</span>
                        <span class="font-semibold text-gray-900">Ciudad:</span>
                        <span class="text-gray-700">{{ $evento->ciudad }}</span>
                    </p>

                    <p class="text-2xl font-bold text-indigo-800 mt-6 flex items-center gap-2 transition-transform duration-300 hover:translate-x-2">
                        <span class="text-3xl">💰</span>
                        Precio: <span class="text-gray-900">${{ $evento->precio }}</span>
                    </p>
                </div>

                <!-- Botones de acción con efectos -->
                <div class="flex justify-between items-center mt-10">
                    <a href="{{ route('eventos') }}" class="px-6 py-3 rounded-full bg-gray-600 text-white text-lg font-semibold
                        hover:bg-gray-700 transition-all duration-300 transform hover:scale-105 shadow-md flex items-center gap-2">
                        ⬅ Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
