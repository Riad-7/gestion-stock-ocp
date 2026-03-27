@php
    $partyName = trim(($party->nom ?? '').' '.($party->prenom ?? ''));
    $companyName = $party->entreprise ?? null;
    $documentDate = $documentDate ?? null;
    $reference = $reference ?? null;
    $statusLabel = $statusLabel ?? null;
    $paymentLabel = $paymentLabel ?? null;
    $footerNote = $footerNote ?? 'Merci pour votre visite';
@endphp

<style>
    @page {
        size: A4 portrait;
        margin: 10mm;
    }

    @media print {
        body {
            background: #fff !important;
        }

        .no-print {
            display: none !important;
        }

        .print-shell {
            padding: 0 !important;
            max-width: none !important;
        }

        .ticket-paper {
            box-shadow: none !important;
            border: 1px solid #d4d4d8 !important;
            width: 100% !important;
            max-width: 190mm !important;
            margin: 0 auto !important;
            padding: 14px !important;
            border-radius: 0 !important;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .ticket-paper table {
            font-size: 12px !important;
        }

        .ticket-paper th,
        .ticket-paper td {
            padding: 8px !important;
        }

        .ticket-paper .print-tight {
            margin: 10px 0 !important;
        }

        .ticket-paper .print-text-sm {
            font-size: 12px !important;
            line-height: 1.35 !important;
        }
    }
</style>

<div class="print-shell mx-auto max-w-5xl px-4 pb-10">
    <div class="no-print mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ $backUrl }}" class="btn-secondary">Retour</a>
            @if(!empty($pdfUrl))
                <a href="{{ $pdfUrl }}" class="btn-secondary">Telecharger PDF</a>
            @endif
        </div>
        <button type="button" onclick="window.print()" class="btn-primary">Imprimer</button>
    </div>

    <div class="grid gap-6 lg:grid-cols-[22rem_1fr]">
        <section class="ticket-paper mx-auto w-full max-w-[22rem] rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_24px_80px_-32px_rgba(15,23,42,0.45)]">
            <div class="flex flex-col items-center text-center">
                <img src="{{ asset('logoocp-removebg-preview.png') }}" alt="Logo" class="h-16 w-16 object-contain print:h-12 print:w-12">
                <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.35em] text-slate-500">Gestion de stock</p>
                <h1 class="mt-2 text-lg font-semibold text-slate-950">{{ $documentTitle }}</h1>
                <p class="mt-1 text-xs text-slate-500">{{ $documentDate?->format('d/m/Y H:i') ?? '--' }}</p>
            </div>

            <div class="print-tight my-5 border-t border-dashed border-slate-300"></div>

            <div class="print-text-sm grid gap-4 text-sm text-slate-700 print:gap-3">
                <div class="grid gap-1">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-slate-500">{{ $partyLabel }}</span>
                        <span class="text-right font-medium text-slate-950">{{ $partyName ?: '--' }}</span>
                    </div>
                    @if($companyName)
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-slate-500">Entreprise</span>
                            <span class="text-right">{{ $companyName }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-slate-500">Telephone</span>
                        <span class="text-right">{{ $party->telephone ?? '--' }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-slate-500">Email</span>
                        <span class="text-right break-all">{{ $party->email ?? '--' }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-slate-500">Adresse</span>
                        <span class="text-right">{{ $party->adresse ?? '--' }}</span>
                    </div>
                </div>

                <div class="print-tight my-1 border-t border-dashed border-slate-300"></div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-slate-500">Reference</span>
                        <span class="text-right font-medium text-slate-950">{{ $reference ?: '--' }}</span>
                    </div>
                    @if($statusLabel)
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-slate-500">Statut</span>
                            <span class="text-right">{{ $statusLabel }}</span>
                        </div>
                    @endif
                    @if($paymentLabel)
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-slate-500">Paiement</span>
                            <span class="text-right">{{ $paymentLabel }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="print-tight my-5 border-t border-dashed border-slate-300"></div>

            <div class="overflow-hidden rounded-2xl border border-slate-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-3 py-3 font-medium">Produit</th>
                            <th class="px-3 py-3 font-medium">Qte</th>
                            <th class="px-3 py-3 font-medium">Prix</th>
                            <th class="px-3 py-3 text-right font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="px-3 py-3 font-medium text-slate-950">{{ $itemName }}</td>
                            <td class="px-3 py-3">{{ $quantity }}</td>
                            <td class="px-3 py-3">{{ number_format($unitPrice, 2) }}</td>
                            <td class="px-3 py-3 text-right">{{ number_format($totalPrice, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="print-text-sm mt-5 space-y-2 text-sm print:mt-4 print:space-y-1">
                <div class="flex items-center justify-between font-semibold text-slate-950">
                    <span>Total</span>
                    <span>{{ number_format($totalPrice, 2) }} DH</span>
                </div>
                <div class="flex items-center justify-between text-slate-500">
                    <span>Prix unitaire</span>
                    <span>{{ number_format($unitPrice, 2) }} DH</span>
                </div>
                <div class="flex items-center justify-between text-slate-500">
                    <span>Quantite</span>
                    <span>{{ $quantity }}</span>
                </div>
            </div>

            <div class="print-tight my-5 border-t border-dashed border-slate-300"></div>

            <div class="print-text-sm text-center text-xs leading-6 text-slate-500 print:leading-5">
                <p class="font-semibold uppercase tracking-[0.22em] text-slate-700">GS OCP</p>
                <p>Gestion des ventes et commandes</p>
                <p>{{ strtoupper($footerNote) }}</p>
            </div>
        </section>

        <section class="no-print rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_24px_80px_-32px_rgba(15,23,42,0.35)]">
            <h2 class="text-xl font-semibold text-slate-950">Apercu du document</h2>
            <p class="mt-2 text-sm text-slate-500">
                Cette page est prete pour l'impression. Clique sur <span class="font-medium text-slate-700">Imprimer</span> pour sortir la facture en format ticket, comme dans ton exemple.
            </p>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div class="rounded-3xl bg-slate-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Document</p>
                    <p class="mt-3 text-lg font-semibold text-slate-950">{{ $documentTitle }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $reference ?: '--' }}</p>
                    <p class="mt-4 text-sm text-slate-600">Article: {{ $itemName }}</p>
                </div>

                <div class="rounded-3xl bg-slate-950 p-5 text-white">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Montant</p>
                    <p class="mt-3 text-3xl font-semibold">{{ number_format($totalPrice, 2) }} DH</p>
                    <p class="mt-2 text-sm text-slate-300">Quantite {{ $quantity }} x {{ number_format($unitPrice, 2) }} DH</p>
                </div>
            </div>
        </section>
    </div>
</div>
