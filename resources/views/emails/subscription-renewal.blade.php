<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renew Your Plan - Fitub</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #111827; background: #f3f4f6; padding: 20px; }
        .card { max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg,#111827 0%,#4f46e5 100%); padding: 24px 22px; color: #fff; }
        .header h1 { margin: 0; font-size: 18px; font-weight: 800; letter-spacing: 0.02em; }
        .body { padding: 22px; }
        .muted { color: #6b7280; font-size: 14px; }
        .box { background: #f9fafb; border: 1px solid #e5e7eb; padding: 14px; border-radius: 12px; margin: 14px 0; }
        .cta { display: inline-block; background: #4f46e5; color: #fff !important; text-decoration: none; padding: 12px 18px; border-radius: 10px; font-weight: 800; }
        .footer { padding: 18px 22px; background: #111827; color: #d1d5db; font-size: 12px; text-align: center; }
        .pill { display:inline-block; padding: 4px 10px; border-radius:999px; background:#eef2ff; color:#3730a3; font-weight:700; font-size:12px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>Fitub subscription renewal</h1>
        </div>

        <div class="body">
            <p>Hi {{ $user->name }},</p>

            @if($isExpired)
                <p class="muted">Your <span class="pill">{{ ucfirst((string) $subscription->plan_type) }}</span> plan has expired.</p>
            @else
                <p class="muted">
                    Your <span class="pill">{{ ucfirst((string) $subscription->plan_type) }}</span> plan will expire in
                    <strong>{{ max(0, (int) $days) }}</strong> day(s).
                </p>
            @endif

            <div class="box">
                <div><strong>Expiry date:</strong> {{ $expiresAt->format('d M Y, h:i A') }}</div>
                <div class="muted" style="margin-top: 6px;">
                    Renew to continue uninterrupted access to lead and chat features.
                </div>
            </div>

            <p style="margin: 18px 0;">
                <a class="cta" href="{{ $renewUrl }}">Renew now</a>
            </p>

            <p class="muted">
                If the button doesn’t open, login and visit Billing → Plans in your dashboard.
            </p>
        </div>

        <div class="footer">
            Fitub • This is an automated email. Please do not reply.
        </div>
    </div>
</body>
</html>

