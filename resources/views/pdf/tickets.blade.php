<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tickets de Compra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        .ticket {
            page-break-after: always;
            border: 2px solid #333;
            padding: 20px;
            margin: 20px;
            text-align: center;
        }
        .ticket img {
            width: 100%; /* Ajusta el tamaño de la imagen */
            max-width: 300px; /* Limita el tamaño de la imagen */
            margin-bottom: 15px;
        }
        .qr {
            margin-top: 10px;
        }
    </style>
</head>
<body>

@foreach($lineasVenta as $linea)
    <div class="ticket">
        <h2>Entrada para {{ $linea[2] }}</h2>
        <p><strong>Fecha:</strong> {{ $linea[3] }}</p>
        <p><strong>Hora:</strong> {{ $linea[4] }}</p>
        <p><strong>Ciudad:</strong> {{ $linea[5] }}</p>
        <p><strong>Precio:</strong> €{{ number_format($linea[1], 2) }}</p>

        <div class="qr">
            <p>Ruta del ticket: {{ route('ticket.validar', ['id' => $linea[0]]) }}</p>
            {!! QrCode::size(150)->generate(route('ticket.validar', ['id' => $linea[0]])) !!}
        </div>

    </div>
@endforeach

</body>
</html>
