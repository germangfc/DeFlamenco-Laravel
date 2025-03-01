@php use Carbon\Carbon; @endphp
@extends('main')

@section('content')
    <div class="container mx-auto mt-8">
        <h1 class="text-3xl font-bold text-center mb-8">Listado de Ventas</h1>

        @if(empty($ventas))
            <p class="text-center text-gray-600">No hay ventas para mostrar.</p>
        @else
            @foreach($ventas as $venta)
                <div class="mb-8 font-marcellus">
                    <!-- Encabezado de la venta -->
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-2xl font-semibold">Venta: {{ $venta->guid }}</h4>
                        <p class="text-gray-600 text-base">Fecha: {{ Carbon::parse($venta->created_at)->format('d-m-Y') }}</p>
                    </div>
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <div class="overflow-x-auto">
                                <table class="table w-full">
                                    <thead>
                                    <tr class="font-black">
                                        <th>Id Ticket</th>
                                        <th>Precio</th>
                                        <th>Evento</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Ciudad</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $total = 0;
                                    @endphp
                                    @foreach($venta->lineasVenta as $lineaVenta)
                                        @php
                                            // Se asume que el índice 1 es el precio
                                            $total += $lineaVenta[1];
                                        @endphp
                                        <tr>
                                            <td>{{ $lineaVenta[0] }}</td>
                                            <td>{{ number_format($lineaVenta[1],2, ',', '.') }} €</td>
                                            <td>{{ $lineaVenta[2] }}</td>
                                            <td>{{ Carbon::parse($lineaVenta[3])->format('d-m-Y') }}</td>
                                            <td>{{ Carbon::parse($lineaVenta[4])->format('H:i:s') }}</td>
                                            <td>{{ $lineaVenta[5] }}</td>
                                        </tr>
                                    @endforeach
                                    <!-- Fila de total y botón "Detalle" -->
                                    <tr class="bg-customBeige  font-bold">
                                        <td colspan=5" class="text-right pr-4">Total: {{ number_format($total, 2, ',', '.') }} €</td>
                                        <td>
                                            <a href="{{ route('ventas.show', $venta->id) }}" class="btn btn-info btn-sm">
                                                Detalle
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Paginación general de ventas -->
            <div class="flex justify-center mt-8 mb-8 space-x-2">
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
        @endif

    </div>
@endsection


{{--
@extends('main')

@section('content')

    <div class="container mt-5">
        <h1 class="text-center mb-4">Listado de Ventas</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <x-table-basico :headers="[ 'Id','Nº Ticket', 'Precio', 'Evento', 'Fecha', 'Hora', 'Ciudad']" tableClass="table table-striped w-full">
                @foreach($ventas as $venta)
                    <tr class="hover">
                        <td>Venta: {{$venta->guid}}</td>
                        <td colspan="2"></td>
                        <td><a href="{{ route('ventas.show', $venta->id) }}" class="btn btn-info btn-sm">Ver</a></td>
                    </tr>

--}}
{{--                    $lineasVenta[]=[$ticket->_id, $ticket->price, $event->nombre, $event->fecha, $event->hora, $event->ciudad]; // se añade una línea de venta a la lista de lineas de venta--}}{{--

                    @foreach($venta->lineasVenta as $lineaVenta)
                        <tr class="hover">
                            <td></td>
                                <td>{{$lineaVenta[0]}}</td>
                                <td>{{$lineaVenta[1]}}</td>
                                <td>{{$lineaVenta[2]}}</td>
                                <td>{{$lineaVenta[3]}}</td>
                                <td>{{$lineaVenta[4]}}</td>
                                <td>{{$lineaVenta[5]}}</td>
--}}
{{--                            <td>{{$lineaVenta['idTicket']}}</td>
                            <td>{{$lineaVenta['precioUnitario']}}</td>--}}{{--

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
--}}
