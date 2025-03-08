<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validación de Ticket</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 90%;
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }
        h1 {
            color: #333;
            font-size: 32px;
            margin-bottom: 30px;
        }
        .valid-ticket {
            background-color: #28a745; /* Verde para validación */
            color: white;
            padding: 20px;
            border-radius: 8px;
            font-size: 18px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .valid-ticket p {
            margin: 5px 0;
        }
        .valid-ticket strong {
            font-weight: bold;
        }
        .ticket-details {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .ticket-details p {
            font-size: 16px;
            color: #555;
        }
        footer {
            text-align: center;
            color: #777;
            font-size: 14px;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Ticket Validado</h1>

    <!-- Sección de Validación -->
    <div class="valid-ticket">
        <p>¡Tu entrada es válida!</p>
        <p><strong>Evento:</strong> {{ $ticket->nombre }}</p>
        <p><strong>Fecha:</strong> {{ $ticket->fecha }}</p>
        <p><strong>Hora:</strong> {{ $ticket->hora }}</p>
        <p><strong>Ciudad:</strong> {{ $ticket->ciudad }}</p>
        <p><strong>Precio:</strong> €{{ number_format($ticket->price, 2) }}</p>
    </div>

    <!-- Detalles adicionales -->
    <div class="ticket-details">
        <p><strong>Recuerda:</strong> Este ticket es válido solo para el evento especificado. ¡Disfruta de la experiencia!</p>
        <p>Para más información, consulta nuestros términos y condiciones en el sitio web.</p>
    </div>

    <footer>
        <p>&copy; {{ date('Y') }} Tablao Flamenco | Todos los derechos reservados.</p>
    </footer>
</div>

</body>
</html>
