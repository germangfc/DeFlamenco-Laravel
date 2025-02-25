@extends('main')

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">Editar Empresa</h1>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('empresas.update', $empresa->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" id="name"
                               value="{{ $empresa->name }}" required>
                        @error('name')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email"
                               value="{{ $empresa->email }}" required>
                        @error('email')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="cif" class="form-label">CIF</label>
                        <input type="text" name="cif" class="form-control" id="cif"
                               value="{{ $empresa->cif }}" required>
                        @error('cif')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control" id="direccion"
                               value="{{ $empresa->direccion }}" required>
                        @error('direccion')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" id="telefono"
                               value="{{ $empresa->telefono }}" required>
                        @error('telefono')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="cuentaBancaria" class="form-label">Cuenta Bancaria</label>
                        <input type="text" name="cuentaBancaria" class="form-control" id="cuentaBancaria"
                               value="{{ $empresa->cuentaBancaria }}" required>
                        @error('cuentaBancaria')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="imagen" class="form-label">Imagen</label>
                        <input type="file" name="imagen" class="form-control" id="imagen">
                        @if ($empresa->imagen)
                            <img src="{{ asset('storage/' . $empresa->imagen) }}" alt="Imagen Empresa" class="img-fluid mt-2">
                        @endif
                        @error('imagen')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('empresas.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@endsection
