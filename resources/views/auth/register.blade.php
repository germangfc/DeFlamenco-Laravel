<x-guest-layout>
        <!-- Selección de Entidad -->
        <div class="mt-4">
            <label for="selectEntidad" class="block text-sm font-medium text-gray-700">Seleccione una opción</label>
            <select name="selectEntidad" id="selectEntidad" class="mt-1 block w-full">
                <option value="">-- Seleccione una opción --</option>
                <option value="cliente" {{ old('selectEntidad') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                <option value="empresa" {{ old('selectEntidad') == 'empresa' ? 'selected' : '' }}>Empresa</option>
            </select>
        </div>

        <!-- Secciones adicionales -->
        <div id="seccionCliente" class="mt-4" style="display: none;">
            @include('clientes.form-create')
        </div>

        <div id="seccionEmpresa" class="mt-4" style="display: none;">
            @include('empresas.form-create')
        </div>

        <!-- Botón de Registro -->
        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <x-primary-button class="ml-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>

    <script>
        const selectEntidad = document.getElementById('selectEntidad');
        const seccionCliente = document.getElementById('seccionCliente');
        const seccionEmpresa = document.getElementById('seccionEmpresa');

        function actualizarSecciones() {
            const valor = selectEntidad.value;
            if(valor === 'cliente') {
                seccionCliente.style.display = 'block';
                seccionEmpresa.style.display = 'none';
            } else if(valor === 'empresa') {
                seccionCliente.style.display = 'none';
                seccionEmpresa.style.display = 'block';
            } else {
                seccionCliente.style.display = 'none';
                seccionEmpresa.style.display = 'none';
            }
        }

        // Actualizar la vista al cambiar el select
        selectEntidad.addEventListener('change', actualizarSecciones);

        // Si hay un valor seleccionado previamente (por ejemplo, tras una validación fallida)
        document.addEventListener('DOMContentLoaded', actualizarSecciones);
    </script>

</x-guest-layout>
