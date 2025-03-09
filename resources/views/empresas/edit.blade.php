@php
    use Illuminate\Support\Str;
@endphp

@extends('main')

@section('content')

        <div class="details-container relative z-10 p-4 md:p-8  max-w-4xl mx-auto">
            <div class="glass-card bg-white/5 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-2xl flamenco-light:bg-base-200">
                <h1 class="event-title text-3xl md:text-4xl font-bold mb-8 border-l-4 border-primary pl-4">
                    Editar Empresa
                </h1>

                <form method="POST" action="{{ route('empresas.update', $empresa->id) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div class="form-group">
                            <x-input-label for="name" class="text-primary" :value="__('Nombre')" />
                            <x-text-input type="text" name="name" id="nameEmpresa"
                                          class="glass-input w-full p-3 bg-white/10 border border-white/20 rounded-lg"
                                          value="{{ old('name', $empresa->name) }}"
                                          required
                                          placeholder="Nombre de la Empresa" />
                            <x-input-error :messages="$errors->get('name')" />
                        </div>

                        <!-- CIF -->
                        <div class="form-group">
                            <x-input-label for="cif" class="text-primary" :value="__('CIF')" />
                            <x-text-input type="text" name="cif" id="cif"
                                          class="glass-input w-full p-3 bg-white/10 border border-white/20 rounded-lg"
                                          value="{{ old('cif', $empresa->cif) }}"
                                          required
                                          placeholder="CIF de la Empresa" />
                            <x-input-error  :messages="$errors->get('cif')" />
                        </div>

                        <!-- Dirección -->
                        <div class="form-group">
                            <x-input-label for="direccion" class="text-primary" :value="__('Dirección')" />
                            <x-text-input type="text" name="direccion" id="direccion"
                                          class="glass-input w-full p-3 bg-white/10 border border-white/20 rounded-lg"
                                          value="{{ old('direccion', $empresa->direccion) }}"
                                          required
                                          placeholder="Dirección de la Empresa" />
                            <x-input-error  :messages="$errors->get('direccion')" />
                        </div>

                        <!-- Teléfono -->
                        <div class="form-group">
                            <x-input-label for="telefono" class="text-primary" :value="__('Teléfono')" />
                            <x-text-input type="text" name="telefono" id="telefono"
                                          class="glass-input w-full p-3 bg-white/10 border border-white/20 rounded-lg"
                                          value="{{ old('telefono', $empresa->telefono) }}"
                                          required
                                          placeholder="Número de Teléfono" />
                            <x-input-error  :messages="$errors->get('telefono')" />
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <x-input-label for="email" class="text-primary" :value="__('Email')" />
                            <x-text-input type="email" name="email" id="emailEmpresa"
                                          class="glass-input w-full p-3 bg-white/10 border border-white/20 rounded-lg"
                                          value="{{ old('email', $empresa->email) }}"
                                          required
                                          placeholder="Correo Electrónico" />
                            <x-input-error  :messages="$errors->get('email')" />
                        </div>

                        <!-- Cuenta Bancaria -->
                        <div class="form-group">
                            <x-input-label for="cuentaBancaria" class="text-primary" :value="__('Cuenta Bancaria')" />
                            <x-text-input type="text" name="cuentaBancaria" id="cuentaBancaria"
                                          class="glass-input w-full p-3 bg-white/10 border border-white/20 rounded-lg"
                                          value="{{ old('cuentaBancaria', $empresa->cuentaBancaria) }}"
                                          required
                                          placeholder="Cuenta Bancaria" />
                            <x-input-error  :messages="$errors->get('cuentaBancaria')" />
                        </div>
                    </div>

                    <!-- Imagen -->
                    <div class="form-group mt-6">
                        <x-input-label class="text-primary block mb-4" :value="__('Foto de Perfil')" />

                        <div class="relative group">
                            <div class="flex flex-col items-center justify-center border-2 border-dashed border-white/20 rounded-xl p-6 hover:border-primary transition-all cursor-pointer"
                                 onclick="document.getElementById('imagen').click()">

                                @if($empresa->imagen)
                                    <img id="imagen-preview" src="{{ Str::startsWith($empresa->imagen, 'http') ? $empresa->imagen : asset('storage/empresas/' . $empresa->imagen) }}"
                                         class="w-32 h-32 rounded-full mb-4 object-cover border-4 border-primary/50 shadow-lg">
                                @else
                                    <div class="text-center space-y-4">
                                        <span class="text-4xl">🏢</span>
                                        <p class="text-sm text-gray-400">Haz clic para subir un logo</p>
                                    </div>
                                @endif

                                <x-file-input-basico name="imagen" id="imagen"
                                                     class="hidden"
                                                     accept="image/*"
                                                     onchange="previewCompanyImage(event)"/>
                            </div>

                            <x-input-error  :messages="$errors->get('imagen')" />
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex flex-col md:flex-row gap-4 mt-8">
                        <a href="{{ route('empresas.index') }}"
                           class="bg-gray-700 hover:bg-gray-600 text-white px-8 py-3 rounded-lg transition-all transform hover:scale-105 shadow-lg hover:shadow-gray-500/20 flex-1 text-center">
                            Cancelar
                        </a>

                        <button class="bg-primary hover:bg-accent text-white px-8 py-3 rounded-lg transition-all transform hover:scale-105 shadow-lg hover:shadow-pink-700/20 flex-1">
                            {{ __('Actualizar Empresa') }}
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
            @apply bg-white/10 border border-white/20 rounded-lg p-3 text-white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-input:focus {
            @apply border-primary ring-2 ring-primary/20;
        }

        .event-title {
            font-family: 'Oswald', sans-serif;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
    </style>

    <script>
        function previewCompanyImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('imagen-preview');
                const container = event.target.closest('.relative');

                if(!preview) {
                    container.innerHTML = `
                        <img id="imagen-preview" class="w-32 h-32 rounded-full mb-4 object-cover border-4 border-primary/50 shadow-lg"
                             src="${reader.result}">`;
                } else {
                    preview.src = reader.result;
                }
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
