<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Certificat d\'Accomplissement') }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .container {
            width: 100%;
            height: 100%;
            padding: 20px;
            box-sizing: border-box;
        }
        .border {
            border: 15px solid #2c3e50;
            padding: 10px;
            height: 90%;
            text-align: center;
        }
        .inner-border {
            border: 5px solid #18bc9c;
            height: 100%;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .title {
            font-size: 50px;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 5px;
            margin-bottom: 20px;
        }
        .subtitle {
            font-size: 24px;
            color: #7f8c8d;
            margin-bottom: 40px;
        }
        .name {
            font-size: 40px;
            font-style: italic;
            color: #e74c3c;
            border-bottom: 2px solid #bdc3c7;
            display: inline-block;
            padding-bottom: 10px;
            margin-bottom: 40px;
        }
        .reason {
            font-size: 20px;
            color: #34495e;
            margin-bottom: 50px;
        }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .date {
            font-size: 18px;
            color: #7f8c8d;
        }
        .signature {
            border-top: 2px solid #2c3e50;
            width: 300px;
            padding-top: 10px;
            font-size: 18px;
            color: #2c3e50;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="border">
            <div class="inner-border">
                <div class="title">{{ __('Certificat de Réussite') }}</div>
                <div class="subtitle">{{ __('Décerné à') }}</div>
                <div class="name">{{ $studentName }}</div>
                <div class="reason">
                    {{ __('Pour la réalisation exceptionnelle suivante :') }}<br>
                    <strong>{{ $certificateTitle }}</strong>
                </div>
                <table style="width: 100%; margin-top: 50px;">
                    <tr>
                        <td style="text-align: left; width: 50%;">
                            <div class="date">{{ __('Date :') }} {{ $date }}</div>
                        </td>
                        <td style="text-align: right; width: 50%;">
                            <div class="signature">
                                {{ __('L\'Équipe Pédagogique') }}<br>
                                {{ $schoolName }}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
