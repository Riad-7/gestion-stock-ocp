<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f5f5f5;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; margin: 0; padding: 24px 0; width: 100%;">
        <tr>
            <td align="center">
                <table role="presentation" width="570" cellpadding="0" cellspacing="0" style="width: 570px; max-width: 570px; background-color: #ffffff; border-radius: 6px; overflow: hidden;">
                    <tr>
                        <td align="center" style="padding: 24px 32px; background-color: #f9fafb;">
                            @php
                                $logoPath = public_path('C:\Users\Gros Info\Desktop\gs-backend-ocp\gestion-stock-ocp\gs-laravel\public\logoocp-removebg-preview.png');
                            @endphp

                            @if (is_file($logoPath))
                                <img src="{{ $message->embed($logoPath) }}" alt="{{ config('app.name', 'GS OCP') }} Logo" style="max-width: 120px; max-height: 80px; width: auto; height: auto;">
                            @else
                                <div style="font-family: Arial, Helvetica, sans-serif; font-size: 20px; font-weight: 700; color: #111827;">
                                    {{ config('app.name', 'GS OCP') }}
                                </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 32px; font-family: Arial, Helvetica, sans-serif; color: #1f2937;">
                            <h1 style="margin: 0 0 24px; font-size: 28px; line-height: 1.2; color: #111827;">Bonjour Admin,</h1>
                            <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6;">{{ $intro }}</p>

                            @foreach ($details as $label => $value)
                                <p style="margin: 0 0 12px; font-size: 16px; line-height: 1.6;">
                                    <strong>{{ $label }}:</strong> {{ $value }}
                                </p>
                            @endforeach

                            <p style="margin: 28px 0 0; font-size: 14px; line-height: 1.6; color: #6b7280;">
                                Cet email a ete envoye automatiquement par le systeme de gestion de stock.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
