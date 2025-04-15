<!DOCTYPE html>
<html>
<head>
    <title>OTP for Superadmin Approval</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 30px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        p {
            color: #555;
            line-height: 1.6;
            font-size: 15px;
        }

        ul {
            list-style: none;
            padding-left: 0;
            margin: 15px 0;
        }

        ul li {
            margin-bottom: 10px;
            color: #444;
            font-size: 14px;
        }

        ul li span {
            font-weight: bold;
            color: #222;
            display: inline-block;
            width: 130px;
        }

        .otp-section {
            font-size: 16px;
            margin-top: 25px;
        }

        .otp {
            background: #e8f0fe;
            padding: 8px 14px;
            font-size: 18px;
            font-weight: bold;
            color: #1a73e8;
            border-radius: 6px;
            display: inline-block;
            margin-left: 10px;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }

    </style>
</head>
<body>
    <div class="email-container">
        <h2>🔐 Superadmin Signup Request</h2>

        <p>Hello {{$ownerName}} ,</p>

        <p>A new <strong>Superadmin</strong> signup request has been made with the following details:</p>

        <div class="details">
            <ul>
                <li><span>Name:</span> {{ $user['first_name'] }} {{ $user['last_name'] }}</li>
                <li><span>Email:</span> {{ $user['email'] }}</li>
                <li><span>Gender:</span> {{ ucfirst($user['gender']) }}</li>
                <li><span>Date of Birth:</span> {{ \Carbon\Carbon::parse($user['dob'])->format('F j, Y') }}</li>
            </ul>
        </div>

        <p class="otp-section">
            <strong>OTP for Approval:</strong> <span class="otp">{{ $otp }}</span>
        </p>

        <p>Please enter this OTP in the system to verify and approve the new superadmin account.</p>

        <p class="footer">
            Regards,<br>
            <strong>{{ config('app.name') }}</strong>
        </p>
    </div>
</body>
</html>
