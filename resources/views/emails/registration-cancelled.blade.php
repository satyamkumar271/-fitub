<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Cancelled - Fitub</title>
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
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
            color: #dc2626;
            font-size: 20px;
            margin-top: 0;
        }
        .reason-box {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .reason-box p {
            margin: 0;
            color: #7f1d1d;
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
            <div class="logo">❌ FITUB</div>
            <h1>Registration Cancelled</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p>Hello <strong>{{ $user->name }}</strong>,</p>

            <p>We regret to inform you that your registration on the Fitub platform has been cancelled by our administration team.</p>

            <div class="reason-box">
                <p><strong>Reason for Cancellation:</strong></p>
                <p>{{ $reason }}</p>
            </div>

            <h2>What This Means:</h2>
            <ul>
                <li>Your account has been deactivated and is no longer accessible</li>
                <li>All active leads and inquiries have been suspended</li>
                <li>You will not be able to log in or access any platform features</li>
                <li>Any pending payments have been frozen</li>
            </ul>

            <h2>What You Can Do:</h2>
            <p>If you believe this decision was made in error, or if you have any concerns or appeals, please contact our support team immediately. You may submit an appeal within 30 days of this notice.</p>

            <p>We appreciate your understanding. If you have any questions, our support team is here to help.</p>

            <p>Regards,<br><strong>The Fitub Team</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2024 Fitub. All rights reserved.</p>
            <p>
                <a href="#" class="contact-link">Contact Support</a> | 
                <a href="#" class="contact-link">Appeal Decision</a>
            </p>
        </div>
    </div>
</body>
</html>
