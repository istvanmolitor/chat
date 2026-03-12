<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, sans-serif; color: #111827;">
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f3f4f6; padding: 24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 14px; overflow: hidden; border: 1px solid #e5e7eb;">
                <tr>
                    <td style="padding: 28px 24px 16px; text-align: center; background: linear-gradient(135deg, #f8fafc, #eef2ff);">
                        <img src="{{ $logoUrl }}" alt="{{ $appName }} logó" width="96" style="max-width: 96px; height: auto; display: inline-block;">
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 24px 8px;">
                        <h1 style="margin: 0; font-size: 24px; line-height: 1.25; text-align: center; color: #111827;">
                            {{ $title }}
                        </h1>
                    </td>
                </tr>
                {{ $slot }}
            </table>
            <p style="margin: 16px 0 0; font-size: 12px; color: #9ca3af;">
                © {{ now()->year }} {{ $appName }}. Minden jog fenntartva.
            </p>
        </td>
    </tr>
</table>
</body>
</html>
