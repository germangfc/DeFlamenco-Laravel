@extends('main')

@section("content")
    <div class="flex justify-center items-center min-h-screen bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900">
        <div class="bg-gray-800 shadow-2xl rounded-3xl overflow-hidden max-w-3xl w-full transform transition-all duration-500 hover:scale-[1.03] hover:shadow-4xl p-8">
            <form action="{{ route('eventos.store') }}" method="POST" enctype="multipart/form-data" class="text-gray-300">
                @csrf

                <div class="relative group mb-6">
                    <input type="file" name="imagen" id="imagen" accept="image/*" class="hidden" onchange="previewImage(event)" required>
                    <label for="imagen" class="cursor-pointer block">
                        <img id="preview" class="object-cover h-60 w-full rounded-t-3xl transition-transform duration-500 group-hover:scale-110" src="https://via.placeholder.com/800x400" alt="Selecciona una imagen" />
                    </label>
                </div>

                <div class="text-lg space-y-6 leading-relaxed">
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

                    <div>
                        <label class="text-white font-semibold">Dirección</label>
                        <input type="text" name="direccion" class="w-full px-4 py-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-teal-400" required>
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
