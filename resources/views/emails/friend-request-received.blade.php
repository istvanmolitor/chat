@component('emails.layout', ['title' => 'Új ismerősnek jelölés', 'appName' => $appName, 'logoUrl' => $logoUrl])
    <tr>
        <td style="padding: 4px 24px 0;">
            <p style="margin: 0; font-size: 15px; line-height: 1.7; color: #374151;">
                <strong>{{ $senderName }}</strong> megjelölt téged ismerősnek a(z) <strong>{{ $appName }}</strong> felületén.
            </p>
        </td>
    </tr>
    <tr>
        <td align="center" style="padding: 28px 24px 12px;">
            <a href="{{ $senderProfileUrl }}"
               style="display: inline-block; padding: 12px 24px; border-radius: 8px; background-color: #111827; color: #ffffff; text-decoration: none; font-weight: 700; font-size: 14px;">
                Profil megnyitása
            </a>
        </td>
    </tr>
    <tr>
        <td style="padding: 4px 24px 0;">
            <p style="margin: 0; font-size: 13px; line-height: 1.7; color: #6b7280;">
                Ha a gomb nem működik, másold be ezt a linket a böngésző címsorába:
            </p>
            <p style="margin: 6px 0 0; font-size: 12px; line-height: 1.6; word-break: break-all; color: #4b5563;">
                <a href="{{ $senderProfileUrl }}" style="color: #1f2937; text-decoration: underline;">
                    {{ $senderProfileUrl }}
                </a>
            </p>
        </td>
    </tr>
    <tr>
        <td style="padding: 20px 24px 26px;">
            <p style="margin: 0; font-size: 13px; line-height: 1.7; color: #6b7280;">
                Jelentkezz be, és kezeld az ismerősjelölést a profil oldalon.
            </p>
        </td>
    </tr>
@endcomponent
