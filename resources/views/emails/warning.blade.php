<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warning Notice - Fitub</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .email-header .logo {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body h2 {
            color: #d97706;
            font-size: 20px;
            margin-top: 0;
        }
        .message-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .message-box p {
            margin: 0;
            color: #92400e;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
        }
        .contact-link {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">⚠️ FITUB</div>
            <h1>Warning Notice</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p>Hello <strong>{{ $user->name }}</strong>,</p>

            <p>We are writing to inform you that your account has received a warning due to a reported issue on our platform.</p>

            <div class="message-box">
                <p><strong>Warning Message:</strong></p>
                <p>{{ $warningMessage }}</p>
            </div>

            <p>Your account has been temporarily flagged for review. If you receive multiple warnings, your account may be suspended or permanently banned from the platform.</p>

            <h2>What You Should Do:</h2>
            <ul>
                <li>Review our community guidelines and terms of service</li>
                <li>Ensure all future interactions comply with platform rules</li>
                <li>Contact support if you believe this is a mistake</li>
            </ul>

            <p>We take the safety and integrity of our community seriously. We appreciate your cooperation in maintaining a positive environment for all users.</p>

            <p>If you have any questions or concerns, please contact our support team.</p>

            <p>Regards,<br><strong>The Fitub Team</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2024 Fitub. All rights reserved.</p>
            <p>
                <a href="#" class="contact-link">Contact Support</a> | 
                <a href="#" class="contact-link">Community Guidelines</a>
            </p>
        </div>
    </div>
</body>
</html>
