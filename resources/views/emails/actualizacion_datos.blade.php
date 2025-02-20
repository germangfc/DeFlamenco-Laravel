<html>
<head>
    <title>Actualización de tus datos en TABLAOPASS</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            color: #e63946;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 18px;
        }
        .content {
            font-size: 16px;
            line-height: 1.6;
            margin-top: 20px;
        }
        .cta-button {
            display: inline-block;
            padding: 12px 25px;
            margin-top: 20px;
            background-color: #e63946;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
        }
        .cta-button:hover {
            background-color: #d62828;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>¡Tus datos han sido actualizados correctamente, {{ $usuario->name }}!</h1>
        <p>Estamos emocionados de seguir acompañándote en tu viaje flamenco.</p>
    </div>

    <div class="content">
        <p>Tu información ha sido actualizada con éxito en nuestra plataforma.</p>
        <p>Gracias por seguir siendo parte de la familia flamenca de <strong>TABLAOPASS</strong>.</p>
        <p>Recuerda que en cualquier momento puedes revisar tus datos y hacer nuevas actualizaciones. Si necesitas alguna asistencia, no dudes en contactarnos.</p>

        <a href="http://localhost/" class="cta-button">Ver mis datos</a>
    </div>

    <div class="footer">
        <p>Si tienes alguna pregunta o necesitas asistencia, estamos aquí para ayudarte.</p>
        <p>¡Que la magia del flamenco nunca te falte!</p>
    </div>
</div>
</body>
</html>
