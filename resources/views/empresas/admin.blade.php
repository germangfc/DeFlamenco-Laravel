@php
    use Illuminate\Support\Str;
@endphp
@extends('main')

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">Listado de Empresas</h1>

        <div class="card">
            <div class="card-body">
                <div class="flex justify-between mb-4">
                    <a href="{{ route('empresas.create-admin') }}" id="empresacrear" class="btn bg-primary mr-2">Crear Nueva Empresa</a>
                    <x-empresa-search class="w-auto" />
                </div>

                <x-table-basico :headers="['CIF', 'Nombre', 'Email', 'Teléfono', 'Acciones']" tableClass="table table-striped w-full">
                    @foreach ($empresas as $empresa)
                        <tr class="hover">
                            <td>{{ $empresa->cif }}</td>
                            <td>
                                <div class="flex items-center gap-3">
                                    @if($empresa->imagen)
                                        <div class="avatar">
                                            <div class="mask mask-squircle h-12 w-12">
                                                <img src="{{ Str::startsWith($empresa->imagen, 'http') ? $empresa->imagen : asset('storage/empresas/' . $empresa->imagen) }}" alt="Logo de {{ $empresa->name }}">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="font-bold">{{ $empresa->name }}</div>
                                </div>
                            </td>
                            <td>{{ $empresa->email }}</td>
                            <td>{{ $empresa->telefono }}</td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="{{ route('empresas.show', $empresa->id) }}" class="btn btn-info btn-sm">Ver</a>
                                    <a href="{{ route('empresas.edit', $empresa->id) }}" class="btn btn-primary btn-sm">Editar</a>
                                    <form action="{{ route('empresas.destroy', $empresa->id) }}" method="POST" style="display:inline;" id="deleteForm">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Seguro que deseas eliminar esta empresa?')">
                                            Eliminar
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-table-basico>

                <div class="flex justify-center mt-8 space-x-2">
                    @if ($empresas->onFirstPage())
                        <span class="btn btn-disabled">«</span>
                    @else
                        <a href="{{ $empresas->previousPageUrl() }}" class="btn btn-square">«</a>
                    @endif

                    @foreach ($empresas->getUrlRange(1, $empresas->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="btn btn-square {{ $page == $empresas->currentPage() ? 'btn-active' : '' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    @if ($empresas->hasMorePages())
                        <a href="{{ $empresas->nextPageUrl() }}" class="btn btn-square">»</a>
                    @else
                        <span class="btn btn-disabled">»</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection



