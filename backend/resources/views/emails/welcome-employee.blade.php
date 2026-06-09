<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #1a1a1a; }
        .container { max-width: 560px; margin: 0 auto; padding: 32px 24px; }
        .header { text-align: center; margin-bottom: 32px; }
        .logo { font-size: 24px; font-weight: 800; color: #2563eb; }
        .card { background: #f8fafc; border: 2px solid #2563eb; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
        .label { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 4px; }
        .value { font-size: 16px; font-weight: 600; color: #1a1a1a; margin-bottom: 16px; }
        .btn { display: inline-block; padding: 12px 24px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 999px; font-weight: 600; font-size: 14px; }
        .footer { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 32px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">iSuggest</div>
            <p style="color: #64748b; margin-top: 4px;">Innovation starts with you.</p>
        </div>

        <div class="card">
            <h2 style="margin-top: 0;">Welcome, {{ $user->name }}!</h2>
            <p>Your employee account has been created. You can sign in using the credentials provided by your administrator.</p>

            <div class="label">Email</div>
            <div class="value">{{ $user->email }}</div>

            <div style="text-align: center; margin-top: 24px;">
                <a href="{{ config('app.frontend_url') }}" class="btn">Sign In to iSuggest</a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} iSuggest. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
