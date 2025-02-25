@extends('main')

@section('content')

    <div class="container mt-5">
        <h1 class="text-center mb-4">Detalles de la Venta</h1>
        <div class="card">
            <div class="card-body">
                <h2>Venta: {{ $venta->guid }}</h2>

                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th style="width: 20%;">Ticket</th>
                        <th style="width: 20%;">Precio adquisición</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($venta->lineasVenta as $lineaVenta)
                        <tr>
                            <td style="padding-left: 40px;"> <!-- Indentación visual -->
                                {{ $lineaVenta['idTicket'] }}
                            </td>
                            <td>
                                {{ $lineaVenta['precioUnitario'] }} €
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="text-center mt-4">
                    <a href="{{ route('ventas.index') }}" class="btn btn-primary">Volver</a>
                </div>
            </div>
        </div>
    </div>


@endsection
