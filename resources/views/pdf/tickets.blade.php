<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets de Compra</title>
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
            max-width: 800px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
            font-size: 28px;
            margin-bottom: 30px;
        }
        .logo {
            display: block;
            margin: 0 auto;
            max-width: 200px;
            margin-bottom: 20px;
        }
        .ticket {
            page-break-after: always;
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .ticket:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .ticket img {
            max-width: 200px;
            margin: 20px 0;
            border-radius: 10px;
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
        .ticket .disclaimer {
            margin-top: 30px;
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 8px;
            font-size: 14px;
            color: #444;
            text-align: left;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
    <img src="{{ asset('images/LogoTablao.jpg') }}" alt="Logo Tablao" class="logo">
    <h1>Tickets de Compra</h1>

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

    <footer>
        <p>&copy; {{ date('Y') }} Tablao Flamenco | Todos los derechos reservados.</p>
    </footer>
</div>

</body>
</html>
