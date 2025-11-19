<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'DigitalConnect')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f5f5f5;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        .header {
            background: linear-gradient(135deg, #8B5CF6 0%, #6366F1 100%);
            padding: 40px 20px;
            text-align: center;
        }

        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #ffffff;
            text-decoration: none;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 20px;
        }

        .message {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .info-box {
            background-color: #f7fafc;
            border-left: 4px solid #8B5CF6;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }

        .info-box h3 {
            color: #2d3748;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .info-item {
            margin: 10px 0;
            font-size: 14px;
            color: #4a5568;
        }

        .info-label {
            font-weight: 600;
            color: #2d3748;
            display: inline-block;
            min-width: 140px;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #8B5CF6 0%, #6366F1 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 25px 0;
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
        }

        .footer {
            background-color: #2d3748;
            color: #cbd5e0;
            padding: 30px 20px;
            text-align: center;
            font-size: 14px;
        }

        .footer-links {
            margin: 20px 0;
        }

        .footer-link {
            color: #a0aec0;
            text-decoration: none;
            margin: 0 10px;
        }

        .footer-link:hover {
            color: #ffffff;
        }

        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 30px 0;
        }

        .highlight {
            color: #8B5CF6;
            font-weight: 600;
        }

        @media only screen and (max-width: 600px) {
            .content {
                padding: 30px 20px;
            }

            .greeting {
                font-size: 20px;
            }

            .message {
                font-size: 14px;
            }

            .cta-button {
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <a href="{{ config('app.frontend_url', 'https://digitalconnects.live') }}" class="logo">
            ⚡ DigitalConnect
        </a>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} DigitalConnect. Tous droits réservés.</p>
        <div class="footer-links">
            <a href="{{ config('app.frontend_url') }}/support" class="footer-link">Support</a>
            <a href="{{ config('app.frontend_url') }}/privacy" class="footer-link">Confidentialité</a>
            <a href="{{ config('app.frontend_url') }}/terms" class="footer-link">Conditions</a>
        </div>
        <p style="margin-top: 20px; color: #a0aec0; font-size: 12px;">
            Vous recevez cet email car vous êtes inscrit sur DigitalConnect.<br>
            DigitalConnect - La plateforme qui vous connecte avec les opportunités
        </p>
    </div>
</div>
</body>
</html>
