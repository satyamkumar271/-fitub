<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - Fitub</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 25px;
            line-height: 1.8;
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .warning-box p {
            margin: 0;
            color: #856404;
            font-weight: 600;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 15px 35px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 25px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);
        }
        .cta-button:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .info-section {
            background-color: #f7fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        .info-section h3 {
            margin-top: 0;
            color: #2d3748;
            font-size: 18px;
        }
        .info-item {
            margin: 10px 0;
            color: #4a5568;
            font-size: 14px;
        }
        .email-footer {
            background-color: #2d3748;
            color: #cbd5e0;
            padding: 30px;
            text-align: center;
            font-size: 14px;
        }
        .email-footer a {
            color: #90cdf4;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 30px 0;
        }
        .token-info {
            background-color: #e6fffa;
            border: 1px solid #81e6d9;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            word-break: break-all;
            font-size: 12px;
            color: #234e52;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-body {
                padding: 30px 20px;
            }
            .email-header {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">FITUB</div>
            <h1>Reset Your Password</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                Hello {{ $user->name }},
            </div>

            <div class="message">
                We received a request to reset your password for your Fitub account. Click the button below to reset your password:
            </div>

            <div style="text-align: center;">
                <a href="{{ $url }}" class="cta-button">Reset Password</a>
            </div>

            <div class="warning-box">
                <p>⚠️ This link will expire in 60 minutes for security reasons.</p>
            </div>

            <div class="info-section">
                <h3>Didn't request this?</h3>
                <div class="info-item">
                    If you didn't request a password reset, you can safely ignore this email. Your password will remain unchanged.
                </div>
            </div>

            <div class="divider"></div>

            <div class="message" style="font-size: 14px; color: #718096;">
                If you're having trouble clicking the button, copy and paste the URL below into your web browser:
            </div>

            <div class="token-info">
                {{ $url }}
            </div>

            <div class="message" style="font-size: 14px; color: #718096;">
                If you have any questions or need assistance, feel free to reach out to our support team.
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p style="margin: 0 0 10px 0;">
                <strong>Fitub</strong> - Your Fitness Partner
            </p>
            <p style="margin: 0 0 10px 0;">
                Connect with trainers, gyms, and fitness enthusiasts
            </p>
            <p style="margin: 0; font-size: 12px;">
                This is an automated email. Please do not reply to this message.
            </p>
        </div>
    </div>
</body>
</html>

