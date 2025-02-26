@extends('main')

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">Lista de Eventos</h1>
        <div class="card">
            <div class="card-body">
                <x-table-basico :headers="[ 'Nombre', 'Fecha', 'Hora', 'Ciudad','Precio', 'Acciones']" tableClass="table table-striped w-full">
                    @foreach ($eventos as $evento)
                        <tr class="hover">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar">
                                        <div class="mask mask-squircle h-12 w-12">
                                            <img src="{{ asset('storage/images/' . $evento->foto)}}" alt="Avatar de {{ $evento->nombre }}">
                                        </div>
                                    </div>
                                    <div class="font-bold">{{ $evento->nombre }}</div>
                                </div>
                            </td>
                            <td>{{ $evento->fecha }}</td>
                            <td>{{ $evento->hora }}</td>
                            <td>{{ $evento->ciudad }}</td>
                            <td>{{$evento->precio}}</td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="{{ route('eventos.show', $evento->id) }}" class="btn btn-info btn-sm">Ver</a>
                                    <a href="{{ route('eventos.edit', $evento->id) }}" class="btn btn-primary btn-sm">Editar</a>
                                    <form action="{{ route('eventos.destroy', $evento->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-table-basico>

                <div class="flex justify-center mt-8 space-x-2">
                    @if ($eventos->onFirstPage())
                    @else
                        <a href="{{ $eventos->previousPageUrl() }}" class="btn btn-square">«</a>
                    @endif

                    @foreach ($eventos->getUrlRange(1, $eventos->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="btn btn-square {{ $page == $eventos->currentPage() ? 'btn-active' : '' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    @if ($eventos->hasMorePages())
                        <a href="{{ $eventos->nextPageUrl() }}" class="btn btn-square">»</a>
                    @else
                        <span class="btn btn-disabled">»</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
