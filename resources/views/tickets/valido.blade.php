<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Validación de Ticket</title>
</head>
<body>
<h1>Ticket Validado</h1>

<p><strong>Evento:</strong> {{ $ticket->nombre }}</p>
<p><strong>Fecha:</strong> {{ $ticket->fecha }}</p>
<p><strong>Hora:</strong> {{ $ticket->hora }}</p>
<p><strong>Ciudad:</strong> {{ $ticket->ciudad }}</p>
<p><strong>Precio:</strong> €{{ number_format($ticket->precio, 2) }}</p>

</body>
</html>
