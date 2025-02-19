@extends('main')

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">Detalles del Cliente</h1>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <h5 class="card-title">Nombre:</h5>
                            </div>
                            <div class="col-md-8">
                                <p class="card-text">{{ $cliente->user->name }}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <h5 class="card-title">Email:</h5>
                            </div>
                            <div class="col-md-8">
                                <p class="card-text">{{ $cliente->user->email }}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <h5 class="card-title">DNI:</h5>
                            </div>
                            <div class="col-md-8">
                                <p class="card-text">{{ $cliente->dni }}</p>
                            </div>
                        </div>
                    </div>
                    @if ($cliente->foto_dni)
                        <div class="col-md-4 text-center">
                            <h5 class="card-title">Foto DNI:</h5>
                            <img src="{{ asset('storage/' . $cliente->foto_dni) }}" alt="Foto DNI" class="img-fluid rounded">
                        </div>
                    @else
                        <div class="col-md-4 text-center">
                            <h5 class="card-title">Foto DNI:</h5>
                            <p class="card-text">No disponible</p>
                        </div>
                    @endif
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('clientes.index') }}" class="btn btn-primary">Volver</a>
                </div>
            </div>
        </div>
    </div>
@endsection
