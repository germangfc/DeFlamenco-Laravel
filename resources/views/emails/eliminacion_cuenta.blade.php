<html>
<head>
    <title>Confirmación de baja en TABLAOPASS</title>
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
        <h1>¡Lo sentimos, {{ $usuario->name }}!</h1>
        <p>Tu cuenta en TABLAOPASS ha sido eliminada con éxito.</p>
    </div>

    <div class="content">
        <p>Queremos agradecerte por haber sido parte de nuestra comunidad flamenca. Lamentamos que hayas decidido dar de baja tu cuenta.</p>
        <p>Si alguna vez decides regresar, recuerda que nuestra plataforma siempre tendrá lo mejor del flamenco para ti.</p>
        <p>Si tienes alguna pregunta o necesitas más información, no dudes en contactarnos. Estaremos encantados de ayudarte en lo que necesites.</p>

        <p>Gracias de nuevo y que el arte del flamenco te acompañe siempre.</p>

        <a href="http://localhost/" class="cta-button">Visita TABLAOPASS</a>
    </div>

    <div class="footer">
        <p>Si tienes alguna pregunta o necesitas asistencia, estamos aquí para ayudarte.</p>
        <p>¡La magia del flamenco siempre será parte de ti!</p>
    </div>
</div>
</body>
</html>
