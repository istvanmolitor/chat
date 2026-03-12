<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-mail cím megerősítése</title>
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
                            E-mail cím megerősítése
                        </h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px 24px 0;">
                        <p style="margin: 0; font-size: 15px; line-height: 1.7; color: #374151;">
                            Köszönjük a regisztrációt a(z) <strong>{{ $appName }}</strong> felületén.
                            A fiókod aktiválásához kérjük, erősítsd meg az e-mail címedet az alábbi gombra kattintva.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding: 28px 24px 12px;">
                        <a href="{{ $verificationUrl }}"
                           style="display: inline-block; padding: 12px 24px; border-radius: 8px; background-color: #111827; color: #ffffff; text-decoration: none; font-weight: 700; font-size: 14px;">
                            E-mail cím megerősítése
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px 24px 0;">
                        <p style="margin: 0; font-size: 13px; line-height: 1.7; color: #6b7280;">
                            Ha a gomb nem működik, másold be ezt a linket a böngésző címsorába:
                        </p>
                        <p style="margin: 6px 0 0; font-size: 12px; line-height: 1.6; word-break: break-all; color: #4b5563;">
                            <a href="{{ $verificationUrl }}" style="color: #1f2937; text-decoration: underline;">
                                {{ $verificationUrl }}
                            </a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 20px 24px 26px;">
                        <p style="margin: 0; font-size: 13px; line-height: 1.7; color: #6b7280;">
                            Ha nem te hoztad létre a fiókot, ezt az üzenetet figyelmen kívül hagyhatod.
                        </p>
                    </td>
                </tr>
            </table>
            <p style="margin: 16px 0 0; font-size: 12px; color: #9ca3af;">
                © {{ now()->year }} {{ $appName }}. Minden jog fenntartva.
            </p>
        </td>
    </tr>
</table>
</body>
</html>
