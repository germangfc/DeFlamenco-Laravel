@php
    use Illuminate\Support\Str;
@endphp

@extends('main')

@section('content')
        <div class="details-container relative z-10 p-8 md:p-16 text-white max-w-6xl mx-auto">
            <div class="glass-card bg-white/5 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-2xl">
                <div class="flex flex-col md:flex-row gap-8 items-center">
                    <!-- Avatar -->
                    <div class="w-full md:w-1/3 text-center">
                        <div class="relative inline-block">
                            <img src="{{ Str::startsWith($cliente->avatar, 'http') ? $cliente->avatar : asset('storage/images/' . $cliente->avatar) }}"
                                 alt="Foto Perfil"
                                 class="w-48 h-48 rounded-full border-4 border-primary/50 object-cover shadow-xl">
                        </div>
                    </div>

                    <!-- Detalles -->
                    <div class="w-full md:w-2/3 space-y-6">
                        <h1 class="event-title text-4xl md:text-5xl font-bold mb-6 border-l-4 border-primary pl-4">
                            {{ $cliente->user->name }}
                        </h1>

                        <div class="space-y-4">
                            <div class="detail-item flex items-center gap-4 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition">
                                <div class="text-amber-500 text-2xl">📧</div>
                                <div>
                                    <p class="text-sm text-gray-400">Email</p>
                                    <p class="text-lg font-semibold">{{ $cliente->user->email }}</p>
                                </div>
                            </div>

                        </div>

                        <div class="mt-8">
                            <a href="{{ route('clientes.index') }}"
                               class="inline-block bg-primary hover:bg-accent text-white px-8 py-3 rounded-lg transition-all transform hover:scale-105 shadow-lg hover:shadow-pink-700/20">
                                ← Volver al listado
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    <style>
        .event-hero {
            @apply relative py-24;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        }

        .event-title {
            @apply text-4xl md:text-5xl font-bold mb-6;
            font-family: 'Oswald', sans-serif;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .glass-card {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .detail-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
@endsection
