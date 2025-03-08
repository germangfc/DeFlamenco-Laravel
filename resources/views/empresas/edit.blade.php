@php
    use Illuminate\Support\Str;
@endphp
@extends('main')

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">Editar Empresa</h1>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('empresas.update', $empresa->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Nombre -->
                    <div class="mb-3">
                        <x-input-label for="name" :value="__('Nombre')" />
                        <x-text-input type="text" name="name" id="name" class="form-control" value="{{ old('name', $empresa->name) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input type="email" name="email" id="email" class="form-control" value="{{ old('email', $empresa->email) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <!-- CIF -->
                    <div class="mb-3">
                        <x-input-label for="cif" :value="__('CIF')" />
                        <x-text-input type="text" name="cif" id="cif" class="form-control" value="{{ old('cif', $empresa->cif) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('cif')" />
                    </div>

                    <!-- Dirección -->
                    <div class="mb-3">
                        <x-input-label for="direccion" :value="__('Dirección')" />
                        <x-text-input type="text" name="direccion" id="direccion" class="form-control" value="{{ old('direccion', $empresa->direccion) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('direccion')" />
                    </div>

                    <!-- Teléfono -->
                    <div class="mb-3">
                        <x-input-label for="telefono" :value="__('Teléfono')" />
                        <x-text-input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono', $empresa->telefono) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
                    </div>

                    <!-- Cuenta Bancaria -->
                    <div class="mb-3">
                        <x-input-label for="cuentaBancaria" :value="__('Cuenta Bancaria')" />
                        <x-text-input type="text" name="cuentaBancaria" id="cuentaBancaria" class="form-control" value="{{ old('cuentaBancaria', $empresa->cuentaBancaria) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('cuentaBancaria')" />
                    </div>

                    <!-- Imagen -->
                    <div class="mb-3">
                        <x-input-label for="imagen" :value="__('Imagen')" />
                        <x-file-input-basico name="imagen" id="imagen" class="form-control" accept="image/*"/>
                        <x-input-error class="mt-2" :messages="$errors->get('imagen')" />

                        @if ($empresa->imagen)
                            <img src="{{ Str::startsWith($empresa->imagen, 'http') ? $empresa->imagen : asset('storage/empresas/' . $empresa->imagen) }}" alt="Imagen Empresa" class="img-fluid mt-2">
                        @endif
                    </div>

                    <!-- Botones -->
                    <div class="mb-3">
                        <a href="{{ route('empresas.index') }}" class="btn btn-secondary">Cancelar</a>
                        <x-primary-button class="ml-4">
                            {{ __('Actualizar') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
