<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviso Paroquial - Igreja São João Baptista do Fomento</title>
    <style>
        /* Geral */
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
        .section {
            padding: 15px;
            border-bottom: 1px solid #f1f1f1;
        }
        .label {
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        .content {
            color: #555;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .footer {
            background: #8b0000;
            color: white;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            border-radius: 0 0 8px 8px;
            margin-top: 20px;
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
        /* Responsividade */
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                padding: 10px;
            }
            .header {
                font-size: 18px;
            }
            .label {
                font-size: 14px;
            }
            .content {
                font-size: 14px;
            }
            .footer {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Cabeçalho -->
        <div class="header">
            Bem-vindo à Igreja São João Baptista do Fomento
        </div>

        <!-- Título -->
        <div class="section">
            <p class="content"> 📌 {{ $title }}</p>
        </div>

        <!-- Data de Notificação -->
        <div class="section">
            <p class="label">🗓️ Data de Notificação:</p>
            <p class="content">{{ $date_notify }}</p>
        </div>

        <!-- Data do Evento -->
        <div class="section">
            <p class="label">📅 Data do Evento:</p>
            <p class="content">{{ $date_realize }}</p>
        </div>

        <!-- Hora do Evento -->
        <div class="section">
            <p class="label">🕒 Hora do Evento:</p>
            <p class="content">{{ $hora }}</p>
        </div>

        <!-- Local -->
        <div class="section">
            <p class="label">📍 Local:</p>
            <p class="content">{{ $address }}</p>
        </div>

        <!-- Descrição do Aviso -->
        <div class="section">
            <p class="label">📝 Descrição:</p>
            <p class="content">{{ $description }}</p>
        </div>
    </div>

    <!-- Rodapé Fora do Box -->
    <div class="footer">
        <p>“Alegrei-me quando me disseram: Vamos à casa do Senhor.” - Salmos 122:1</p>
        <p>Paróquia São João Baptista • Todos os direitos reservados</p>
    </div>
</body>
</html>
