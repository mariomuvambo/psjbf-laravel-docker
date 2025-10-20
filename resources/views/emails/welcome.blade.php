<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo à Igreja São João Baptista do Fomento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: #8b0000;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 20px;
            border-radius: 8px 8px 0 0;
        }
        .content {
            padding: 20px;
            text-align: center;
            color: #333;
        }
        .footer {
            background: #8b0000;
            color: white;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #8b0000;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .button:hover {
            background: #a52a2a;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Bem-vindo à Igreja Católica</div>
        <div class="content">
            <p>Olá, <strong>{{ $user->nome }}</strong>!</p>
            <p>Estamos felizes por você ter se juntado à nossa comunidade. Que sua jornada espiritual seja cheia de paz e bênçãos.</p>
            <a href="{{ url('https://psjbf.onrender.com') }}" class="button">Acessar a Igreja Online</a>
        </div>
        <div class="footer">
            &copy; 2025 Igreja Católica. Todos os direitos reservados.
        </div>
    </div>
</body>
</html>
