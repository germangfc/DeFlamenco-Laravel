@php
    use Illuminate\Support\Str;
@endphp

@extends('main')

@section('content')

        <div class="details-container relative z-10 p-4 md:p-8 text-white max-w-4xl mx-auto">
            <div class="glass-card bg-white/5 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-2xl">
                <h1 class="event-title text-3xl md:text-4xl font-bold mb-8 border-l-4 border-primary pl-4">
                    Editar Cliente
                </h1>

                <form action="{{ route('clientes.update', $cliente->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Campo Nombre -->
                    <div class="form-group">
                        <label for="name" class="block text-sm font-medium text-primary mb-2">Nombre</label>
                        <input type="text" name="name" id="name"
                               class="glass-input w-full p-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               value="{{ $cliente->user->name }}" required>
                        @error('name')
                        <div class="text-red-400 text-sm mt-2 bg-red-900/30 px-4 py-2 rounded-lg">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Campo Email -->
                    <div class="form-group">
                        <label for="email" class="block text-sm font-medium text-primary mb-2">Email</label>
                        <input type="email" name="email" id="email"
                               class="glass-input w-full p-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               value="{{ $cliente->user->email }}" required>
                        @error('email')
                        <div class="text-red-400 text-sm mt-2 bg-red-900/30 px-4 py-2 rounded-lg">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Campo Avatar -->
                    <div class="form-group">
                        <label class="block text-sm font-medium text-primary mb-2">Foto de perfil</label>

                        <div class="relative group">
                            <div class="flex flex-col items-center justify-center border-2 border-dashed border-white/20 rounded-xl p-6 hover:border-primary transition-all cursor-pointer"
                                 onclick="document.getElementById('avatar').click()">

                                @if($cliente->avatar)
                                    <img id="avatar-preview" src="{{ Str::startsWith($cliente->avatar, 'http') ? $cliente->avatar : asset('storage/images/' . $cliente->avatar) }}"
                                         class="w-32 h-32 rounded-full mb-4 object-cover border-4 border-primary/50 shadow-lg">
                                @else
                                    <div class="text-center">
                                        <span class="text-4xl mb-4">📷</span>
                                        <p class="text-sm text-gray-400">Haz clic para subir una foto</p>
                                    </div>
                                @endif

                                <input type="file" name="avatar" id="avatar"
                                       class="hidden"
                                       onchange="previewImage(event)">
                            </div>

                            @error('avatar')
                            <div class="text-red-400 text-sm mt-2 bg-red-900/30 px-4 py-2 rounded-lg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex flex-col md:flex-row gap-4 mt-8">
                        <a href="{{ route('clientes.index') }}"
                           class="bg-gray-700 hover:bg-gray-600 text-white px-8 py-3 rounded-lg transition-all transform hover:scale-105 shadow-lg hover:shadow-gray-500/20 flex-1 text-center">
                            Cancelar
                        </a>

                        <button type="submit"
                                class="bg-primary hover:bg-accent text-white px-8 py-3 rounded-lg transition-all transform hover:scale-105 shadow-lg hover:shadow-pink-700/20 flex-1">
                            Actualizar Cliente
                        </button>

                    </div>
                </form>
            </div>
        </div>

    <style>
        .glass-card {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .glass-input {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: white;
        }

        .glass-input:focus {
            box-shadow: 0 0 0 3px rgba(236, 173, 41, 0.25);
        }

        .event-title {
            font-family: 'Oswald', sans-serif;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
    </style>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('avatar-preview');
                if(!preview) {
                    const container = event.target.closest('.relative');
                    container.innerHTML = `
                        <img id="avatar-preview" class="w-32 h-32 rounded-full mb-4 object-cover border-4 border-primary/50 shadow-lg"
                             src="${reader.result}">`;
                } else {
                    preview.src = reader.result;
                }
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
