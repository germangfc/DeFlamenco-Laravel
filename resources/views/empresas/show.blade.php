@extends("main")


@section('content')
<div class="container">
        <h1>Detalles de la Empresa</h1>
        <div class="card">
            <div class="card-body">
                <h2>{{ $empresa->nombre }}</h2>
                <p><strong>Email:</strong> {{ $empresa->email }}</p>
                <p><strong>Teléfono:</strong> {{ $empresa->telefono }}</p>
                <p><strong>Dirección:</strong> {{ $empresa->direccion }}</p>
            </div>
        </div>
        <a href="{{ route('empresas.index') }}" class="btn btn-secondary mt-3">Volver al listado</a>
    </div>
@endsection

