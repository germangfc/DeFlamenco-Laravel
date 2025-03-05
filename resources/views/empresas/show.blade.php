@extends("main")


@section('content')
<div class="container">
        <h1>Detalles de la Empresa</h1>
        <div class="card">
            <div class="card-body">
                <img class="object-cover h-64 w-full transition-transform duration-300 hover:scale-110 rounded-lg"
                     src='{{ Str::startsWith($empresa->imagen, 'http') ? $empresa->imagen : asset("storage/empresas/" . $empresa->imagen) }}'
                     alt="Empresa {{ $empresa->id }}" />
                <h2>{{ $empresa->name }}</h2>
                <p><strong>CIF:</strong> {{ $empresa->cif }}</p>
                @can('admin')
                    <p><strong>Email:</strong> {{ $empresa->email }}</p>
                    <p><strong>Teléfono:</strong> {{ $empresa->telefono }}</p>
                @endcan
                <p><strong>Dirección:</strong> {{ $empresa->direccion }}</p>
            </div>
        </div>
        <a href="{{ route('empresas.index') }}" class="btn btn-secondary mt-3">Volver al listado</a>
    </div>
@endsection

