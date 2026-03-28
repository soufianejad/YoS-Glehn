<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #334155;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f8fafc;
            padding-bottom: 40px;
        }
        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-spacing: 0;
            font-family: sans-serif;
            color: #4a4a4a;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background-color: #1e40af;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
            background-color: #ffffff;
        }
        .content h2 {
            color: #1e293b;
            margin-top: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .content p {
            margin-bottom: 20px;
            font-size: 16px;
            color: #475569;
        }
        .button-container {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .button {
            background-color: #2563eb;
            color: #ffffff !important;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #94a3b8;
        }
        .footer p {
            margin: 5px 0;
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 20px 0;
        }
        @media screen and (max-width: 600px) {
            .content {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main" width="100%">
            <!-- Header -->
            <tr>
                <td class="header">
                    <h1>{{ config('plateform.name', config('app.name')) }}</h1>
                </td>
            </tr>
            <!-- Body -->
            <tr>
                <td class="content">
                    <h2>{{ $title }}</h2>
                    <p>{!! nl2br(e($messageBody)) !!}</p>
                    
                    @if ($link)
                        <div class="button-container">
                            <a href="{{ $link }}" class="button">
                                {{ __('Voir les détails') }}
                            </a>
                        </div>
                    @endif

                    <div class="divider"></div>
                    <p style="font-size: 14px;">
                        {{ __('Si vous avez des questions, n\'hésitez pas à nous contacter à') }} 
                        <a href="mailto:{{ config('plateform.contact_email') }}" style="color: #2563eb; text-decoration: none;">
                            {{ config('plateform.contact_email') }}
                        </a>.
                    </p>
                </td>
            </tr>
            <!-- Footer -->
            <tr>
                <td class="footer">
                    <p>&copy; {{ date('Y') }} {{ config('plateform.name', config('app.name')) }}. {{ __('Tous droits réservés.') }}</p>
                    <p>{{ config('plateform.address') }}</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
