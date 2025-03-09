
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets de Compra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
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

        .ticket {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .ticket:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .ticket h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 10px;
        }

        .ticket p {
            font-size: 16px;
            color: #555;
            margin: 5px 0;
        }

        .ticket .qr {
            margin-top: 20px;
            background-color: #e8e8e8;
            padding: 15px;
            border-radius: 10px;
            display: inline-block;
        }

        .ticket .qr p {
            font-size: 14px;
            color: #333;
            margin-bottom: 10px;
        }

        .ticket .qr img {
            margin-top: 10px;
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

        .disclaimer {
            margin-top: 30px;
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 8px;
            font-size: 14px;
            color: #444;
            text-align: left;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Tickets de Compra</h1>
    </div>

    @foreach($lineasVenta as $linea)
        <div class="ticket">
            <h2>Entrada para {{ $linea[2] }}</h2>
            <p><strong>Fecha:</strong> {{ $linea[3] }}</p>
            <p><strong>Hora:</strong> {{ $linea[4] }}</p>
            <p><strong>Ciudad:</strong> {{ $linea[5] }}</p>
            <p><strong>Precio:</strong> €{{ number_format($linea[1], 2) }}</p>

            <div class="qr">
                <p><strong>Ruta del ticket:</strong> {{ route('ticket.validar', ['id' => $linea[0]]) }}</p>
                {!! QrCode::size(150)->generate(route('ticket.validar', ['id' => $linea[0]])) !!}
            </div>

            <!-- Disclaimer debajo de la entrada -->
            <div class="disclaimer">
                <p><strong>Política de devolución:</strong> Lamentablemente, no se aceptan devoluciones de entradas una vez compradas. Asegúrate de que la fecha, hora y lugar sean correctos antes de proceder con la compra.</p>
                <p><strong>Condiciones:</strong> Esta entrada es válida solo para el evento especificado. El acceso al evento puede estar sujeto a condiciones adicionales, por lo que te recomendamos que leas cuidadosamente los términos de uso en nuestra página web.</p>
            </div>
        </div>
    @endforeach

    <div class="footer">
        <p>&copy; {{ date('Y') }} Tablao Flamenco | Todos los derechos reservados.</p>
    </div>
</div>

</body>
</html>
