<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Rejected</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f4f6;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="background:#0f172a;padding:20px 24px;color:#ffffff;font-size:22px;font-weight:700;">
                            FITUB
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 14px;font-size:16px;color:#111827;">Hi {{ $user->name }},</p>
                            <p style="margin:0 0 14px;font-size:15px;line-height:1.6;color:#374151;">
                                Your account verification request has been rejected after review.
                            </p>
                            <p style="margin:0 0 8px;font-size:14px;color:#111827;font-weight:700;">Reason</p>
                            <div style="margin:0 0 18px;padding:12px;border:1px solid #fecaca;background:#fef2f2;border-radius:8px;font-size:14px;color:#991b1b;">
                                {{ $reason }}
                            </div>
                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#374151;">
                                You can register again with correct documents using the button below.
                            </p>
                            <p style="margin:0 0 20px;">
                                <a href="{{ $registerUrl }}" style="display:inline-block;padding:10px 16px;background:#1d4ed8;color:#ffffff;text-decoration:none;border-radius:8px;font-size:14px;font-weight:700;">
                                    Register Again
                                </a>
                            </p>
                            <p style="margin:0;font-size:12px;color:#6b7280;">
                                If you need help, contact Fitub Support.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
