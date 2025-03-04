<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmación de Pago</title>
</head>
<body>
<h2>Gracias por tu compra</h2>
<p>Tu pago ha sido confirmado. A continuación, encontrarás los detalles de tu compra.</p>

<p>Detalles de la venta:</p>
<ul>
    @foreach ($venta->lineasVenta as $linea)
        <li>Evento: {{ $linea[2] }} | Fecha: {{ $linea[3] }} | Hora: {{ $linea[4] }} | Ciudad: {{ $linea[5] }} | Precio: €{{ number_format($linea[1], 2) }}</li>
    @endforeach
</ul>

<p>Adjunto encontrarás tu PDF con los tickets.</p>
</body>
</html>
