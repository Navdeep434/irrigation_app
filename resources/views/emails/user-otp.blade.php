<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }
        p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }
        .otp {
            font-size: 28px;
            font-weight: bold;
            color: #2a7ae2;
            padding: 15px;
            background-color: #f1f7ff;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #999;
            text-align: center;
        }
        .footer a {
            color: #2a7ae2;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello {{ $user['first_name'] }} {{ $user['last_name'] }},</h2>

        <p>Thank you for signing up on <strong>{{ config('app.name') }}</strong>.</p>

        <p>To complete your registration, please verify your email by entering the OTP below:</p>

        <div class="otp">{{ $otp }}</div>

        <p>This OTP is valid for a limited time and is valid for one-time use only. Please do not share it with anyone.</p>

        <p>If you did not request this verification, you can ignore this email.</p>

        <div class="footer">
            <p>Regards,<br><strong>{{ config('app.name') }}</strong></p>
            <p>If you have any questions, feel free to <a href="mailto:support@{{ env('MAIL_DOMAIN') }}">contact us</a>.</p>
        </div>
    </div>
</body>
</html>
