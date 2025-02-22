@extends('main')

@section("content")
    <!-- Cargar la API de Google Maps con el autocompletado de direcciones -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAToojCJw_9KvxDrlkEbwR9YkQ-Ib4sVxA&libraries=places"></script>

    <div class="flex justify-center items-center min-h-screen ring-gray-700">
        <div class="bg-gray-800 bg-opacity-80 backdrop-blur-lg shadow-2xl ring-1 ring-gray-700 rounded-3xl max-w-3xl w-full p-8 transition-all duration-500 hover:shadow-4xl hover:scale-[1.02]">

            <form action="{{ route('eventos.store') }}" method="POST" enctype="multipart/form-data" class="text-gray-300">
                @csrf

                <div class="relative group mb-6">
                    <input type="file" name="foto" id="foto" accept="image/*" class="hidden" onchange="previewImage(event)" required>
                    <label for="foto" class="cursor-pointer block">
                        <img id="preview" class="object-cover h-60 w-full rounded-2xl transition-transform duration-500 group-hover:scale-105"
                             src="https://via.placeholder.com/800x400" alt="Selecciona una imagen" />
                    </label>
                </div>

                <div class="text-lg space-y-6">
                    <div>
                        <label class="text-white font-semibold">Nombre del Evento</label>
                        <input type="text" name="nombre" class="w-full px-4 py-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-teal-400" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-white font-semibold">Fecha</label>
                            <input type="date" name="fecha" class="w-full px-4 py-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-teal-400" required>
                        </div>
                        <div>
                            <label class="text-white font-semibold">Hora</label>
                            <input type="time" name="hora" class="w-full px-4 py-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-teal-400" required>
                        </div>
                    </div>

                    <!-- Campo de Dirección con Autocompletado -->
                    <div>
                        <label class="text-white font-semibold">Dirección</label>
                        <input type="text" id="autocomplete" name="direccion" class="w-full px-4 py-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-teal-400" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-white font-semibold">Ciudad</label>
                            <input type="text" name="ciudad" class="w-full px-4 py-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-teal-400" required>
                        </div>
                        <div>
                            <label class="text-white font-semibold">Precio</label>
                            <input type="number" name="precio" step="0.01" class="w-full px-4 py-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-teal-400" required>
                        </div>
                        <div>
                            <label class="text-white font-semibold">Stock</label>
                            <input type="number" name="stock" min="1" class="w-full px-4 py-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-teal-400" required>
                        </div>

                    </div>
                </div>

                <div class="flex justify-between items-center mt-10">
                    <button type="submit" class="px-6 py-3 rounded-full bg-teal-500 text-white text-lg font-semibold hover:bg-teal-600 transition-all duration-300 transform hover:scale-105 shadow-md flex items-center gap-2">
                        Crear Evento
                    </button>
                    <a href="{{ route('eventos') }}" class="px-6 py-3 rounded-full bg-gray-600 text-white text-lg font-semibold hover:bg-gray-500 transition-all duration-300 transform hover:scale-105 shadow-md flex items-center gap-2">
                        Volver
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Función para inicializar el autocompletado de Google Places
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

                // Puedes acceder a la dirección completa o datos específicos
                const direccion = place.formatted_address || "";
                const ciudad = place.address_components.find(c => c.types.includes("locality"))?.long_name || "";

                // Aquí puedes poner la lógica para llenar los campos con los valores correspondientes
                // Si quieres llenar automáticamente los campos de ciudad, puedes agregar algo como:
                document.querySelector('[name="direccion"]').value = direccion;
                document.querySelector('[name="ciudad"]').value = ciudad;
            });
        }

        // Cargar el autocompletado al cargar la página
        google.maps.event.addDomListener(window, "load", initAutocomplete);
    </script>

    <script>
        // Función para la vista previa de la imagen
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                const output = document.getElementById('preview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
