<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }
        .content {
            padding: 20px 0;
            text-align: center;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #333333;
            letter-spacing: 5px;
            margin: 20px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            display: inline-block;
        }
        .footer {
            text-align: center;
            color: #999999;
            font-size: 12px;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ config('app.name') }}</h2>
        </div>
        <div class="content">
            <p>{{ __('Bonjour,') }}</p>
            <p>{{ __('Vous avez demandé un code de vérification pour votre inscription sur :app_name. Voici votre code :', ['app_name' => config('app.name')]) }}</p>
            <div class="code">{{ $code }}</div>
            <p>{{ __('Ce code est valide pour les 10 prochaines minutes.') }}</p>
            <p>{{ __('Si vous n\'avez pas demandé ce code, vous pouvez ignorer cet email.') }}</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('Tous droits réservés.') }}</p>
        </div>
    </div>
</body>
</html>
