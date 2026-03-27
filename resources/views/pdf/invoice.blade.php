@php
    $partyName = trim(($party->nom ?? '').' '.($party->prenom ?? ''));
    $companyName = $party->entreprise ?? null;
    $documentDate = $documentDate ?? null;
    $reference = $reference ?? null;
    $statusLabel = $statusLabel ?? null;
    $paymentLabel = $paymentLabel ?? null;
    $footerNote = $footerNote ?? 'Merci pour votre visite';
    $logoPath = public_path('logoocp-removebg-preview.png');
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentTitle }}</title>
    <style>
        @page {
            margin: 18mm 14mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
            font-size: 12px;
            margin: 0;
        }

        .page {
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 18px;
        }

        .logo {
            height: 56px;
            margin-bottom: 8px;
        }

        .eyebrow {
            color: #64748b;
            font-size: 10px;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin: 0 0 6px;
        }

        h1 {
            margin: 0;
            font-size: 22px;
        }

        .muted {
            color: #64748b;
        }

        .divider {
            border-top: 1px dashed #cbd5e1;
            margin: 14px 0;
        }

        .meta-table,
        .items-table,
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 5px 0;
            vertical-align: top;
        }

        .meta-table td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .items-wrap {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            overflow: hidden;
        }

        .items-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-align: left;
            padding: 10px 12px;
        }

        .items-table td {
            padding: 10px 12px;
            border-top: 1px solid #e2e8f0;
        }

        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }

        .summary {
            margin-top: 14px;
        }

        .summary-table td {
            padding: 4px 0;
        }

        .summary-table td:last-child {
            text-align: right;
        }

        .summary-total {
            font-size: 15px;
            font-weight: 700;
        }

        .footer {
            margin-top: 18px;
            text-align: center;
            font-size: 11px;
            color: #64748b;
            line-height: 1.6;
        }

        .footer strong {
            color: #334155;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            @if(file_exists($logoPath))
                <img src="{{ $logoPath }}" alt="Logo" class="logo">
            @endif
            <p class="eyebrow">Gestion de stock</p>
            <h1>{{ $documentTitle }}</h1>
            <p class="muted">{{ $documentDate?->format('d/m/Y H:i') ?? '--' }}</p>
        </div>

        <div class="divider"></div>

        <table class="meta-table">
            <tbody>
                <tr>
                    <td class="muted">{{ $partyLabel }}</td>
                    <td>{{ $partyName ?: '--' }}</td>
                </tr>
                @if($companyName)
                    <tr>
                        <td class="muted">Entreprise</td>
                        <td>{{ $companyName }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="muted">Telephone</td>
                    <td>{{ $party->telephone ?? '--' }}</td>
                </tr>
                <tr>
                    <td class="muted">Email</td>
                    <td>{{ $party->email ?? '--' }}</td>
                </tr>
                <tr>
                    <td class="muted">Adresse</td>
                    <td>{{ $party->adresse ?? '--' }}</td>
                </tr>
                <tr>
                    <td class="muted">Reference</td>
                    <td>{{ $reference ?: '--' }}</td>
                </tr>
                @if($statusLabel)
                    <tr>
                        <td class="muted">Statut</td>
                        <td>{{ $statusLabel }}</td>
                    </tr>
                @endif
                @if($paymentLabel)
                    <tr>
                        <td class="muted">Paiement</td>
                        <td>{{ $paymentLabel }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="divider"></div>

        <div class="items-wrap">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Qte</th>
                        <th>Prix</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $itemName }}</td>
                        <td>{{ $quantity }}</td>
                        <td>{{ number_format($unitPrice, 2) }} DH</td>
                        <td>{{ number_format($totalPrice, 2) }} DH</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="summary">
            <table class="summary-table">
                <tbody>
                    <tr class="summary-total">
                        <td>Total</td>
                        <td>{{ number_format($totalPrice, 2) }} DH</td>
                    </tr>
                    <tr>
                        <td class="muted">Prix unitaire</td>
                        <td>{{ number_format($unitPrice, 2) }} DH</td>
                    </tr>
                    <tr>
                        <td class="muted">Quantite</td>
                        <td>{{ $quantity }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="divider"></div>

        <div class="footer">
            <strong>GS OCP</strong><br>
            Gestion des ventes et commandes<br>
            {{ strtoupper($footerNote) }}
        </div>
    </div>
</body>
</html>
