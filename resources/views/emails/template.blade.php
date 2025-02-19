<html>
<head>
    <title>{{ $subject }}</title>
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
        <h1>{{ $subject }}</h1>
        <p>{{ $greeting }}</p>
    </div>

    <div class="content">
        <p>{{ $messageContent }}</p>
    </div>

    <div class="footer">
        <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
        <p>¡Gracias por formar parte de nuestra comunidad!</p>
    </div>
</div>
</body>
</html>
