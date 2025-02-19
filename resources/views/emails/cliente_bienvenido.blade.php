<html>
<head>
    <title>Bienvenido a la magia del Flamenco, {{ $usuario->name }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 700px;
            margin: 20px auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .header {
            color: #e63946;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 36px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .header p {
            font-size: 20px;
            font-style: italic;
        }
        .content {
            font-size: 18px;
            line-height: 1.8;
            margin-top: 20px;
            text-align: left;
            padding: 0 15px;
            color: #333;
        }
        .cta-button {
            display: inline-block;
            padding: 14px 30px;
            margin-top: 25px;
            background-color: #e63946;
            color: #000;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 18px;
        }
        .cta-button:hover {
            background-color: #d62828;
        }
        .footer {
            margin-top: 40px;
            font-size: 16px;
            color: #777;
            font-style: italic;
        }
        .footer p {
            margin-top: 10px;
        }
        .highlight {
            color: #e63946;
            font-weight: bold;
        }
        .business-section {
            background-color: #f0f8ff;
            padding: 30px;
            border-radius: 10px;
            margin-top: 30px;
        }
        .business-section h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #e63946;
        }
        .business-section p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .business-section a {
            font-size: 18px;
            color: #e63946;
            text-decoration: none;
        }
        .business-section a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>¡Bienvenido a la magia del Flamenco, {{ $usuario->name }}!</h1>
        <p>Un arte que resuena en el alma y emociona hasta el último latido. Ahora eres parte de nuestra vibrante comunidad.</p>
    </div>

    <div class="content">
        <p>Es un honor tenerte con nosotros en <strong>TABLAOPASS</strong>, donde el flamenco no solo es música y baile, sino una experiencia llena de pasión, tradición y emoción. Al unirte, ahora puedes disfrutar de los mejores espectáculos flamencos, reservas exclusivas y vivir el arte de manera única.</p>

        <p>Te invitamos a descubrir lo que tenemos preparado para ti. Como miembro de nuestra familia flamenca, podrás acceder a eventos en vivo, espectáculos en directo, y disfrutar de todo lo que el mundo del flamenco tiene para ofrecerte.</p>

        <p><strong>¿Estás listo para vivir el flamenco como nunca antes?</strong> Te esperamos en nuestros eventos más exclusivos, donde podrás sumergirte en la cultura, la música y el baile más emocionantes de la temporada.</p>

        <a href="https://youtu.be/4a_thK5EdAU?si=3YJ4H8idVDoKqriG" class="cta-button">Descubre nuestros eventos exclusivos</a>
    </div>

    <div class="business-section">
        <h2>¡Anuncia tus eventos flamencos con nosotros!</h2>
        <p>Si eres una empresa o artista flamenco y quieres que tu evento esté en nuestra plataforma, tenemos una oportunidad única para ti. En <strong>TABLAOPASS</strong>, ofrecemos un espacio exclusivo donde las empresas pueden promover sus eventos y llegar a una audiencia apasionada por el flamenco.</p>
        <p>¡Es tu momento para brillar! No dejes pasar la oportunidad de que tu espectáculo sea parte de nuestra familia flamenca. <strong>Contáctanos</strong> y descubre cómo podemos ayudarte a llevar tu evento a un público más amplio.</p>
        <a href="http://localhost" class="cta-button">Anuncia tu evento con nosotros</a>
    </div>

    <div class="footer">
        <p>Si tienes alguna pregunta o necesitas asistencia, nuestro equipo estará encantado de ayudarte en todo lo que necesites. ¡Estamos aquí para ti!</p>
        <p class="highlight">Gracias por formar parte de la magia del flamenco con nosotros. ¡Nos encanta compartir esta pasión contigo!</p>
    </div>
</div>
</body>
</html>
