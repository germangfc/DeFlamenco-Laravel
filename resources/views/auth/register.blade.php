<x-guest-layout>
        <div class="mt-4">
            <x-input-label >Elija Como se quiere Registrar</x-input-label>
            <x-select-basico name="selectEntidad" id="selectEntidad" class="mt-1 block w-full">
                <option value="">-- Seleccione una opción --</option>
                <option value="cliente" {{ old('selectEntidad') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                <option value="empresa" {{ old('selectEntidad') == 'empresa' ? 'selected' : '' }}>Empresa</option>
            </x-select-basico>
        </div>
        <div id="seccionCliente" class="mt-4" style="display: none;">
            @include('clientes.form-create')
        </div>

        <div id="seccionEmpresa" class="mt-4" style="display: none;">
            @include('empresas.form-create')
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
        selectEntidad.addEventListener('change', actualizarSecciones);
        document.addEventListener('DOMContentLoaded', actualizarSecciones);
    </script>

</x-guest-layout>
