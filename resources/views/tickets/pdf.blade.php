@php
    use Illuminate\Support\Str;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket - {{ $evento->nombre }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            margin: 0;
            background-color: #f4f4f9;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: #3b82f6;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }

        .header h1 {
            margin: 0;
        }

        .logo {
            width: 100px; /* Ajusta según el tamaño que desees para el logo */
            margin-bottom: 10px;
        }

        .ticket-info {
            padding: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            border-bottom: 1px solid #ddd;
        }

        .ticket-info div {
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .ticket-info div strong {
            font-size: 16px;
        }

        .ticket-info div p {
            font-size: 14px;
            color: #4b5563;
            margin: 5px 0;
        }

        .footer {
            padding: 20px;
            background-color: #3b82f6;
            color: white;
            text-align: center;
            font-size: 14px;
            border-radius: 0 0 8px 8px;
        }

        .footer p {
            margin: 5px 0;
        }

        .price {
            background-color: #10b981;
            color: white;
            padding: 8px;
            border-radius: 50px;
            font-size: 20px;
            font-weight: bold;
        }

        .status {
            font-size: 16px;
            margin-top: 10px;
        }

        .status-valid {
            color: green;
            font-weight: bold;
        }

        .status-expired {
            color: red;
            font-weight: bold;
        }

        .qr-code {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>{{ $evento->nombre }}</h1>
        <p>{{ \Carbon\Carbon::parse($evento->fecha)->translatedFormat('d M Y') }} - {{ $evento->hora }}</p>
    </div>


    <!-- Ticket Information Section -->
    <div class="ticket-info">
        <div>
            <strong>Evento:</strong>
            <p>{{ $evento->nombre }}</p>
        </div>
        <div>
            <strong>Ubicación:</strong>
            <p>{{ $evento->ciudad }}</p>
            <p>{{ $evento->direccion }}</p>
        </div>
        <div>
            <strong>Fecha y Hora:</strong>
            <p>{{ \Carbon\Carbon::parse($evento->fecha)->translatedFormat('d M Y') }}</p>
            <p>{{ $evento->hora }}</p>
        </div>
        <div>
            <strong>Precio:</strong>
            <p class="price">{{ number_format($ticket->price, 2, ',', '.') }}€</p>
        </div>
    </div>

    <!-- QR Code Section -->
    <div class="qr-code">
        <p><strong>Escanea el QR para validar tu ticket</strong></p>
        {!! QrCode::size(150)->generate(route('ticket.validar', ['id' => $ticket->id])) !!}
        <p><strong>URL del Ticket:</strong> {{ route('ticket.validar', ['id' => $ticket->id]) }}</p>
    </div>

    <!-- Footer Section -->
    <div class="footer">
        <p>&copy; {{ date('Y') }} Tablao Flamenco | Todos los derechos reservados.</p>
    </div>
</div>

</body>
</html>
