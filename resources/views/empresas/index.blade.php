@extends("main")

@section('content')
    <div class="container">
        <h1>Listado de Empresas</h1>

        <a href="{{ route('empresas.create') }}" class="btn btn-success mb-3">Crear Nueva Empresa</a>

        <table class="table table-striped">
            <thead>
            <tr>
                <th>CIF</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($empresas as $empresa)
                <tr>
                    <td>{{ $empresa->cif }}</td>
                    <td>{{ $empresa->nombre }}</td>
                    <td>{{ $empresa->email }}</td>
                    <td>{{ $empresa->telefono }}</td>
                    <td>
                        <a href="{{ route('empresas.show', $empresa->id) }}" class="btn btn-info btn-sm">Ver</a>

                        <a href="{{ route('empresas.edit', $empresa->id) }}" class="btn btn-warning btn-sm">Modificar</a>

                        <form action="{{ route('empresas.destroy', $empresa->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar esta empresa?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="flex justify-center mt-8 space-x-2">
            @if (!$empresas->onFirstPage())
                <a href="{{ $empresas->previousPageUrl() }}" class="btn btn-square">«</a>
            @endif

            @for ($i = max(1, $empresas->currentPage() - 1); $i <= min($empresas->lastPage(), $empresas->currentPage() + 1); $i++)
                <a href="{{ $empresas->url($i) }}" class="btn btn-square {{ $i == $empresas->currentPage() ? 'btn-active' : '' }}">
                    {{ $i }}
                </a>
            @endfor

            @if ($empresas->hasMorePages())
                <a href="{{ $empresas->nextPageUrl() }}" class="btn btn-square">»</a>
            @endif
        </div>
    </div>
@endsection
