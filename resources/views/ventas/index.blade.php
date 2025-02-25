@extends('main')

@section('content')

    <div class="container mt-5">
        <h1 class="text-center mb-4">Listado de Ventas</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <x-table-basico :headers="[ 'Nombre', 'Email', 'DNI', 'Acciones']" tableClass="table table-striped w-full">
                @foreach($ventas as $venta)
                    <tr class="hover">
                        <td>Venta: {{$venta->guid}}</td>
                        <td colspan="2"></td>
                        <td><a href="{{ route('ventas.show', $venta->id) }}" class="btn btn-info btn-sm">Ver</a></td>
                    </tr>

                    @foreach($venta->lineasVenta as $lineaVenta)
                        <tr class="hover">
                            <td></td>
                            <td>{{$lineaVenta['idTicket']}}</td>
                            <td>{{$lineaVenta['precioUnitario']}}</td>
                        </tr>
                    @endforeach
                @endforeach
            </x-table-basico>

            <div class="flex justify-center mt-8 space-x-2">
                @if ($ventas->onFirstPage())
                    <span class="btn btn-disabled">«</span>
                @else
                    <a href="{{ $ventas->previousPageUrl() }}" class="btn btn-square">«</a>
                @endif

                @foreach ($ventas->getUrlRange(1, $ventas->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="btn btn-square {{ $page == $ventas->currentPage() ? 'btn-active' : '' }}">
                        {{ $page }}
                    </a>
                @endforeach

                @if ($ventas->hasMorePages())
                    <a href="{{ $ventas->nextPageUrl() }}" class="btn btn-square">»</a>
                @else
                    <span class="btn btn-disabled">»</span>
                @endif
            </div>

        </div>
    </div>

@endsection
