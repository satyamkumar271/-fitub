<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enable Chat - Fitub</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #111827; background: #f3f4f6; padding: 20px; }
        .card { max-width: 620px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); padding: 26px 22px; color: #fff; }
        .header h1 { margin: 0; font-size: 18px; font-weight: 700; letter-spacing: 0.02em; }
        .body { padding: 22px; }
        .muted { color: #6b7280; font-size: 14px; }
        .box { background: #eef2ff; border: 1px solid #c7d2fe; padding: 14px; border-radius: 10px; margin: 14px 0; }
        .cta { display: inline-block; background: #4f46e5; color: #fff !important; text-decoration: none; padding: 12px 18px; border-radius: 10px; font-weight: 700; }
        .footer { padding: 18px 22px; background: #111827; color: #d1d5db; font-size: 12px; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>Fitub: Enable chat & track your inquiry</h1>
        </div>
        <div class="body">
            <p>Hi {{ $inquiry->guest_name ?? 'there' }},</p>
            <p class="muted">
                We received your inquiry. To chat with the trainer/gym and track updates, please verify your email by creating an account (or reset password if you already have one).
            </p>

            <div class="box">
                <div><strong>Inquiry #{{ $inquiry->id }}</strong></div>
                <div class="muted">Service: {{ $inquiry->service_needed }}</div>
            </div>

            <p style="margin: 18px 0;">
                <a class="cta" href="{{ $claimUrl }}">Verify email & enable chat</a>
            </p>

            <p class="muted">
                This link will expire in 48 hours for security.
            </p>
        </div>
        <div class="footer">
            Fitub • This is an automated email. Please do not reply.
        </div>
    </div>
</body>
</html>
