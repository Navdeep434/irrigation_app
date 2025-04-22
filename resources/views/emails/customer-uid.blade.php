<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Customer UID</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            font-size: 24px;
            color: #4CAF50;
            margin: 0;
        }
        .content {
            font-size: 16px;
            line-height: 1.6;
            color: #333;
        }
        .content p {
            margin: 10px 0;
        }
        .uid-box {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
        }
        .uid-box h3 {
            font-size: 22px;
            color: #333;
            margin: 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
        .footer p {
            margin: 0;
        }
        .footer a {
            color: #4CAF50;
            text-decoration: none;
        }
        .btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Welcome to {{ $appName }}!</h2>
        </div>
        <div class="content">
            <p>Dear {{ $name }},</p>
            <p>Thank you for verifying your account. We’re excited to have you onboard with {{ $appName }}.</p>
            <p>Your unique Customer ID has been generated successfully:</p>
            <div class="uid-box"><h3>{{ $uid }}</h3></div>
            <p>Please save this ID securely. It will be required for account management and service access.</p>
            <a href="{{ $dashboardUrl }}" class="btn">Go to Dashboard</a>
        </div>
        <div class="footer">
            <p>Thank you for choosing {{ $appName }}.</p>
            <p>If you have any questions, feel free to <a href="mailto:{{ $supportEmail }}">contact us</a>.</p>
        </div>
    </div>
</body> 
</html>
