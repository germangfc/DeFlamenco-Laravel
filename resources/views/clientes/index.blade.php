@extends('main')

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">Lista de Clientes</h1>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>DNI</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($clientes as $cliente)
                        <tr>
                            <td>{{ $cliente->user->name}}</td>
                            <td>{{ $cliente->user->email }}</td>
                            <td>{{ $cliente->dni }}</td>
                            <td>
                                <a href="{{ route('clientes.show', $cliente->id) }}" class="btn btn-info">Ver</a>
                                <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-primary btn-sm">Editar</a>
                                <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="flex justify-center mt-8 space-x-2">
                    @if ($clientes->onFirstPage())
                        <span class="btn btn-disabled">«</span>
                    @else
                        <a href="{{ $clientes->previousPageUrl() }}" class="btn btn-square">«</a>
                    @endif

                    @foreach ($clientes->getUrlRange(1, $clientes->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="btn btn-square {{ $page == $clientes->currentPage() ? 'btn-active' : '' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    @if ($clientes->hasMorePages())
                        <a href="{{ $clientes->nextPageUrl() }}" class="btn btn-square">»</a>
                    @else
                        <span class="btn btn-disabled">»</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
