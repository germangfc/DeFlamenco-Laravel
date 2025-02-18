@extends("main")


@section('content')
    <div class="container">
        <h1>Listado de Empresas</h1>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>CIF</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
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
                        <a href="{{ route('empresas.show', $empresa->id) }}" class="btn btn-info">Ver</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $empresas->links() }}
    </div>
@endsection

