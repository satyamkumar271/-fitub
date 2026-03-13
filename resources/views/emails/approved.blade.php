<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Approved - Fitub</title>
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
        .highlight-box {
            background-color: #f0fff4;
            border-left: 4px solid #48bb78;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .highlight-box p {
            margin: 0;
            color: #22543d;
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
        }
        .info-item strong {
            color: #2d3748;
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
            <h1>Account Approved! 🎉</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                Hello {{ $user->name }},
            </div>

            <div class="message">
                Great news! Your account has been reviewed and <strong>approved</strong> by our admin team. You can now access all features of Fitub and start your fitness journey!
            </div>

            <div class="highlight-box">
                <p>✅ Your account is now active and ready to use!</p>
            </div>

            <div class="message">
                You can now log in to your account and explore:
            </div>

            <div class="info-section">
                <h3>What's Next?</h3>
                <div class="info-item">
                    <strong>📱 Access Your Dashboard:</strong> Log in to view your personalized dashboard
                </div>
                <div class="info-item">
                    <strong>🔍 Browse Trainers & Gyms:</strong> Find the perfect fitness partner for your goals
                </div>
                <div class="info-item">
                    <strong>💬 Connect & Inquire:</strong> Reach out to trainers and gym owners directly
                </div>
                @if($user->user_type === 'trainer' || $user->user_type === 'gymowner')
                <div class="info-item">
                    <strong>📊 Manage Your Profile:</strong> Update your profile to attract more customers
                </div>
                @endif
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/auth/login') }}" class="cta-button">Login to Your Account</a>
            </div>

            <div class="divider"></div>

            <div class="message" style="font-size: 14px; color: #718096;">
                If you have any questions or need assistance, feel free to reach out to our support team. We're here to help you achieve your fitness goals!
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

