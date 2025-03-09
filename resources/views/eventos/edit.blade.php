@extends('main')

@section("content")
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAToojCJw_9KvxDrlkEbwR9YkQ-Ib4sVxA&libraries=places"></script>

    <div class="details-container relative z-10 p-4 md:p-8 max-w-4xl mx-auto">
        <div class="glass-card bg-white/5 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-2xl flamenco-light:bg-base-200">
            <h1 class="event-title text-3xl md:text-4xl font-bold mb-8 border-l-4 border-primary pl-4">
                Editar Evento
            </h1>

            <form action="{{ route('eventos.update', $evento->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Imagen del evento -->
                <div class="form-group">
                    <x-input-label class="text-primary block mb-4" :value="__('Imagen del Evento')" />
                    <div class="relative group">
                        <div class="flex flex-col items-center justify-center border-2 border-dashed border-white/20 rounded-xl p-6 hover:border-primary transition-all cursor-pointer"
                             onclick="document.getElementById('dropzone-file').click()">

                            @if($evento->foto)
                                <img id="preview" class="w-64 h-64 rounded-full object-cover border-4 border-primary/50 shadow-lg"
                                     src="{{ Str::startsWith($evento->foto, 'http') ? $evento->foto : asset('storage/images/' . $evento->foto) }}">
                            @else
                                <div class="text-center space-y-4">
                                    <span class="text-4xl">🎉</span>
                                    <p class="text-sm text-gray-400">Haz clic para subir una imagen</p>
                                </div>
                            @endif

                            <input id="dropzone-file" type="file" name="foto" accept="image/*"
                                   class="hidden" onchange="previewImage(event)">
                        </div>
                        <x-input-error :messages="$errors->get('foto')" />
                    </div>
                </div>

                <!-- Campos del formulario -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div class="form-group">
                        <x-input-label for="nombre" class="text-primary" :value="__('Nombre del Evento')" />
                        <x-text-input type="text" name="nombre"
                                      value="{{ old('nombre', $evento->nombre) }}"
                                      required
                                      placeholder="Nombre del evento" />
                        <x-input-error :messages="$errors->get('nombre')" />
                    </div>

                    <!-- Fecha y Hora -->
                    <div class="form-group">
                        <x-input-label for="fecha" class="text-primary" :value="__('Fecha')" />
                        <x-text-input type="date" name="fecha"
                                      value="{{ old('fecha', $evento->fecha) }}"
                                      required />
                        <x-input-error :messages="$errors->get('fecha')" />
                    </div>

                    <div class="form-group">
                        <x-input-label for="hora" class="text-primary" :value="__('Hora')" />
                        <x-text-input type="time" name="hora"
                                      value="{{ old('hora', $evento->hora) }}"
                                      required />
                        <x-input-error :messages="$errors->get('hora')" />
                    </div>

                    <!-- Dirección y Ciudad -->
                    <div class="form-group">
                        <x-input-label for="autocomplete" class="text-primary" :value="__('Dirección')" />
                        <input type="text" id="autocomplete" name="direccion"
                               class="glass-input bg-base-100/20 border border-base-content/20 text-base-content placeholder-base-content/60 rounded-lg shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/30 transition duration-200 w-full p-3"
                               value="{{ old('direccion', $evento->direccion) }}"
                               required>
                        <x-input-error :messages="$errors->get('direccion')" />
                    </div>

                    <div class="form-group">
                        <x-input-label for="ciudad" class="text-primary" :value="__('Ciudad')" />
                        <x-text-input type="text" name="ciudad"
                                      value="{{ old('ciudad', $evento->ciudad) }}"
                                      required
                                      placeholder="Ciudad" />
                        <x-input-error :messages="$errors->get('ciudad')" />
                    </div>

                    <!-- Descripción -->
                    <div class="form-group col-span-full">
                        <x-input-label for="descripcion" class="text-primary" :value="__('Descripción')" />
                        <textarea name="descripcion"
                                  class="glass-input bg-base-100/20 border border-base-content/20 text-base-content placeholder-base-content/60 rounded-lg shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/30 transition duration-200 w-full p-3 resize-y min-h-[150px]"
                                  required>{{ old('descripcion', $evento->descripcion) }}
                        </textarea>
                        <x-input-error :messages="$errors->get('descripcion')" />
                    </div>

                    <!-- Precio y Aforo -->
                    <div class="form-group">
                        <x-input-label for="precio" class="text-primary" :value="__('Precio')" />
                        <x-text-input id="precio" placeholder="Introduce el precio en €" type="number" name="precio" :value="old('precio', $evento->precio)" required autofocus autocomplete="precio" />
                        <x-input-error :messages="$errors->get('precio')" />
                    </div>

                    <div class="form-group">
                        <x-input-label for="stock" class="text-primary" :value="__('Aforo')" />
                        <x-text-input type="number" name="stock"
                                      value="{{ old('stock', $evento->stock) }}"
                                      required
                                      placeholder="Número de plazas" />
                        <x-input-error :messages="$errors->get('stock')" />
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex flex-col md:flex-row gap-4 mt-8">
                    <a href="{{ route('eventos') }}"
                       class="bg-gray-700 hover:bg-gray-600 text-white px-8 py-3 rounded-lg transition-all transform hover:scale-105 shadow-lg hover:shadow-gray-500/20 flex-1 text-center">
                        Volver
                    </a>
                    <button type="submit"
                            class="bg-primary hover:bg-accent text-white px-8 py-3 rounded-lg transition-all transform hover:scale-105 shadow-lg hover:shadow-pink-700/20 flex-1">
                        Actualizar Evento
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
            @apply bg-white/5 border border-white/20 rounded-lg p-3 text-white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-input:focus {
            @apply border-primary ring-2 ring-primary/20;
        }

        .event-title {
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        /* Ajustes para light theme */
        .flamenco-light .glass-input {
            @apply bg-base-200/50 border-base-300 text-base-content;
        }
    </style>

    <script>
        function initAutocomplete() {
            const autocomplete = new google.maps.places.Autocomplete(
                document.getElementById("autocomplete"),
                { types: ["geocode"] }
            );

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    alert("Dirección no válida");
                    return;
                }

                document.querySelector('[name="direccion"]').value = place.formatted_address || "";
                document.querySelector('[name="ciudad"]').value =
                    place.address_components.find(c => c.types.includes("locality"))?.long_name || "";
            });
        }

        function previewImage(event) {
            const input = event.target;
            const file = input.files[0];
            const preview = document.getElementById('preview');
            const container = event.target.closest('.relative');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (!preview) {
                        container.innerHTML = `
                            <img id="preview" class="w-64 h-64 rounded-xl object-cover border-4 border-primary/50 shadow-lg"
                                 src="${e.target.result}">`;
                    } else {
                        preview.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        google.maps.event.addDomListener(window, "load", initAutocomplete);
    </script>
@endsection
