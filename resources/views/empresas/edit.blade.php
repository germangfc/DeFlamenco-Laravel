@php
    use Illuminate\Support\Str;
@endphp
@extends('main')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <!-- Columna Izquierda (Formulario) -->
            <div class="col-md-8 col-sm-12">
                <h1 class="mb-4">Editar Empresa</h1>
                <form method="POST" action="{{ route('empresas.update', $empresa->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <x-input-label for="name" :value="__('Nombre')" />
                        <x-text-input type="text" name="name" id="nameEmpresa" class="form-control field-validate" value="{{ old('name', $empresa->name) }}" required placeholder="Nombre de la Empresa" style="border: 2px solid #ccc; border-radius: 5px;" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div class="form-group mb-3">
                        <x-input-label for="cif" :value="__('CIF')" />
                        <x-text-input type="text" name="cif" id="cif" class="form-control field-validate" value="{{ old('cif', $empresa->cif) }}" required placeholder="CIF de la Empresa" style="border: 2px solid #ccc; border-radius: 5px;" />
                        <x-input-error class="mt-2" :messages="$errors->get('cif')" />
                    </div>

                    <div class="form-group mb-3">
                        <x-input-label for="direccion" :value="__('Dirección')" />
                        <x-text-input type="text" name="direccion" id="direccion" class="form-control field-validate" value="{{ old('direccion', $empresa->direccion) }}" required placeholder="Dirección de la Empresa" style="border: 2px solid #ccc; border-radius: 5px;" />
                        <x-input-error class="mt-2" :messages="$errors->get('direccion')" />
                    </div>

                    <div class="form-group mb-3">
                        <x-input-label for="telefono" :value="__('Teléfono')" />
                        <x-text-input type="text" name="telefono" id="telefono" class="form-control field-validate" value="{{ old('telefono', $empresa->telefono) }}" required placeholder="Número de Teléfono" style="border: 2px solid #ccc; border-radius: 5px;" />
                        <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
                    </div>

                    <div class="form-group mb-3">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input type="email" name="email" id="emailEmpresa" class="form-control field-validate" value="{{ old('email', $empresa->email) }}" required placeholder="Correo Electrónico" style="border: 2px solid #ccc; border-radius: 5px;" />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div class="form-group mb-3">
                        <x-input-label for="cuentaBancaria" :value="__('Cuenta Bancaria')" />
                        <x-text-input type="text" name="cuentaBancaria" id="cuentaBancaria" class="form-control field-validate" value="{{ old('cuentaBancaria', $empresa->cuentaBancaria) }}" required placeholder="Cuenta Bancaria" style="border: 2px solid #ccc; border-radius: 5px;" />
                        <x-input-error class="mt-2" :messages="$errors->get('cuentaBancaria')" />
                    </div>

                    <!-- Selección de Foto -->
                    <div class="form-group mb-3">
                        <x-input-label for="imagen" :value="__('Foto de Perfil')" />
                        <x-file-input-basico name="imagen" id="imagen" class="form-control text-center center-block well well-sm" accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml"/>
                        <x-input-error class="mt-2" :messages="$errors->get('imagen')" />
                    </div>

                    <!-- Botones -->
                    <div class="form-group mb-3">
                        <a href="{{ route('empresas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Cancelar</a>
                        <x-primary-button class="ml-4 btn-lg">
                            {{ __('Actualizar Empresa') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection



