<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Sıfırlama</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7fafc;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        .header h1 {
            color: #2d3748;
            font-size: 28px;
            margin: 0;
        }
        .content {
            padding: 20px 0;
        }
        .content p {
            color: #4a5568;
            font-size: 16px;
            line-height: 1.6;
            margin: 10px 0;
        }
        .code-container {
            text-align: center;
            margin: 30px 0;
        }
        .code {
            display: inline-block;
            background-color: #4299e1;
            color: #ffffff;
            padding: 15px 30px;
            font-size: 24px;
            font-weight: bold;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
        }
        .footer a {
            color: #4299e1;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Şifre Sıfırlama İsteği</h1>
        </div>
        <div class="content">
            <p>Merhaba {{ $user->firstname }},</p>
            <p>Şifrenizi sıfırlamak için aşağıdaki kodu kullanabilirsiniz:</p>
            <div class="code-container">
                <div class="code">{{ $resetCode }}</div>
            </div>
            <p>Bu kod <strong>10 dakika</strong> boyunca geçerlidir. Eğer bu isteği siz yapmadıysanız, lütfen bu e-postayı dikkate almayın.</p>
        </div>
        <div class="footer">
            <p>Teşekkürler,</p>
            <p>{{ env('MAIL_FROM_NAME', 'NobetX') }} Ekibi</p>
            <p><a href="{{ config('app.url') }}">{{ config('app.url') }}</a></p>
        </div>
    </div>
</body>
</html>