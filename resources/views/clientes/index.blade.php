@php
    use Illuminate\Support\Str;
@endphp
@extends('main')

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">Lista de Clientes</h1>

        <div class="card">
            <div class="card-body">
                <div class="flex justify-end mb-4">
                    <x-cliente-search class="w-auto" />
                </div>

                <x-table-basico :headers="[ 'Nombre', 'Email', 'DNI', 'Acciones']" tableClass="table table-striped w-full">
                    @foreach ($clientes as $cliente)
                        <tr class="hover">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar">
                                        <div class="mask mask-squircle h-12 w-12">
                                            <img  src="{{ Str::startsWith($cliente->avatar, 'http') ? $cliente->avatar : asset('storage/images/' . $cliente->avatar) }}" alt="Avatar de {{ $cliente->user->name }}">
                                        </div>
                                    </div>
                                    <div class="font-bold">{{ $cliente->user->name }}</div>
                                </div>
                            </td>
                            <td>{{ $cliente->user->email }}</td>
                            <td>{{ $cliente->dni }}</td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="{{ route('clientes.show', $cliente->id) }}" id="verEmpresa" class="btn btn-info btn-sm">Ver</a>
                                    <a href="{{ route('clientes.edit', $cliente->id) }}" id="editarEmpresa" class="btn btn-primary btn-sm">Editar</a>
                                    <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" id="eliminarEmpresa" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-table-basico>

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
