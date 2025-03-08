@extends('main')

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">Editar Cliente</h1>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('clientes.update', $cliente->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ $cliente->user->name }}" required>
                        @error('name')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" value="{{ $cliente->user->email }}" required>
                        @error('email')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Foto Perfil</label>
                        <input type="file" name="avatar" class="form-control" id="avatar">
                        @if ($cliente->avatar)
                            <img src="{{ asset('storage/images' . $cliente->avatar) }}" alt="Foto Perfil" class="img-fluid mt-2">
                        @endif
                        @error('avatar')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@endsection
