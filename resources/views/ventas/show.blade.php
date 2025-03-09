@php use Carbon\Carbon; @endphp
@extends('main')

@section('content')
    <div class="container mx-auto mt-8">
        <h1 class="text-3xl font-bold text-center mb-8">Detalle Venta</h1>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                {{--<div class="flex items-center justify-between w-full mb-4">
                    <h4 class="text-2xl font-semibold">Venta: {{ $venta->guid }}</h4>
                    <p class="text-gray-600 text-base">Fecha: {{ Carbon::parse($venta->created_at)->format('d-m-Y') }}</p>
                </div>--}}
                <div class="flex items-center justify-between mb-4 w-full">
                    <div class="flex-2">
                        <h4 class="text-2xl font-semibold">Venta: {{ $venta->guid }}</h4>
                    </div>
                    <div class="flex-1 text-right">
                        <p class="text-gray-600 text-base font-marcellus">Fecha: {{ Carbon::parse($venta->created_at)->format('d-m-Y') }}</p>
                    </div>
                </div>
                <div class="overflow-x-auto font-marcellus">
                    <table class="table w-full">
                        <thead>
                        <tr class="font-black">
                            <th>Ticket</th>
                            <th>Precio adquisición</th>
                            <th>Evento</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Ciudad</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $total = 0; @endphp
                        @foreach($venta->lineasVenta as $lineaVenta)
                            @php
                                // Se asume que 'precioUnitario' es el precio de la línea
                                $total += $lineaVenta[1];
                            @endphp
                            <tr>
                                <td>{{ $lineaVenta[0] }}</td>
                                <td>{{ number_format($lineaVenta[1], 2, ',', '.') }} €</td>
                                <td>{{ $lineaVenta[2] ?? '' }}</td>
                                <td>{{ Carbon::parse($lineaVenta[3])->format('d-m-Y') }}</td>
                                <td>{{ Carbon::parse($lineaVenta[4])->format('H:i:s') }}</td>
                                <td>{{ $lineaVenta[5] ?? '' }}</td>
                            </tr>
                        @endforeach
                        <!-- Fila de total -->
                        <tr class="bg-customBeige font-bold">
                            <td colspan="5" class="text-right pr-4">Total:</td>
                            <td>{{ number_format($total, 2, ',', '.') }} €</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('ventas.index') }}" class="btn btn-info btn-sm">Volver</a>
                </div>
            </div>
        </div>
    </div>
@endsection
