@extends('main')

@section("content")
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAToojCJw_9KvxDrlkEbwR9YkQ-Ib4sVxA&libraries=places"></script>

    <x-edit>
        <div>
            <div>
                <form action="{{ route('eventos.create') }}" method="POST" enctype="multipart/form-data" class="">
                    @csrf
                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed flamenco-dark:border-base-100 rounded-lg cursor-pointer bg-white hover:bg-gray-300">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <img id="preview" class="object-cover h-60 w-full rounded-2xl hidden" src="https://via.placeholder.com/800x400" alt="Preview" />
                                <svg id="upload-icon" class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                </svg>
                                <p id="upload-text" class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                            </div>
                            <input id="dropzone-file" type="file" name="foto" accept="image/*" class="hidden" onchange="previewImage(event)" required />
                        </label>
                    </div>

                    <div class="text-lg space-y-6">
                        <div>
                            <x-input-label for="nombre" :value="__('Evento')" />
                            <x-text-input id="nombre" placeholder="Introduce el nombre del evento." class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre')" required autofocus autocomplete="off" />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="fecha" :value="__('Fecha')" />
                                <x-text-input id="fecha" placeholder="dd/mm/aaaa." class="block mt-1 w-full" type="date" name="fecha" :value="old('fecha')" required autofocus autocomplete="fecha" />
                                <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="hora" :value="__('Hora')" />
                                <x-text-input id="hora" placeholder="" class="block mt-1 w-full" type="time" name="hora" :value="old('hora')" required autofocus autocomplete="hora" />
                                <x-input-error :messages="$errors->get('hora')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="direccion" :value="__('Direccion')" />
                            <input type="text" id="autocomplete" name="direccion" class="w-full px-4 py-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-teal-400" required>
                            <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="ciudad" :value="__('Ciudad')" />
                            <x-text-input id="ciudad" placeholder="Introduce la ciudad del evento." class="block mt-1 w-full" type="text" name="ciudad" :value="old('ciudad')" required autofocus autocomplete="ciudad" />
                            <x-input-error :messages="$errors->get('ciudad')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="precio" :value="__('Precio')" />
                                <x-text-input id="precio" placeholder="Introduce el precio." class="block mt-1 w-full" type="number" name="precio" :value="old('precio')" required autofocus autocomplete="precio" />
                                <x-input-error :messages="$errors->get('precio')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="stock" :value="__('Aforo')" />
                                <x-text-input id="stock" placeholder="Introduce el aforo." class="block mt-1 w-full" type="number" name="stock" :value="old('stock')" required autofocus autocomplete="off" />
                                <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end items-center mt-10">
                        <a href="{{ route('eventos') }}" class="underline text-sm hover:text-gray-900 dark:hover:text-gray-100 rounded-md">
                            Volver
                        </a>
                        <x-primary-button type="submit" class="ms-3">
                            Crear Evento
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </x-edit>

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

                const direccion = place.formatted_address || "";
                const ciudad = place.address_components.find(c => c.types.includes("locality"))?.long_name || "";

                document.querySelector('[name="direccion"]').value = direccion;
                document.querySelector('[name="ciudad"]').value = ciudad;
            });
        }

        google.maps.event.addDomListener(window, "load", initAutocomplete);
    </script>

    <script>
        function previewImage(event) {
            const input = event.target;
            const file = input.files[0];
            const preview = document.getElementById('preview');
            const uploadIcon = document.getElementById('upload-icon');
            const uploadText = document.getElementById('upload-text');

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    uploadIcon.classList.add('hidden');
                    uploadText.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
