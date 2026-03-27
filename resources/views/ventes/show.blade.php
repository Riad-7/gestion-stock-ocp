<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-950">Facture vente</h2>
            <p class="text-sm text-slate-500">Imprimez rapidement la facture de cette vente en format ticket.</p>
        </div>
    </x-slot>

    @include('partials.print-ticket', [
        'documentTitle' => 'Facture vente',
        'documentDate' => $vente->date_vente,
        'partyLabel' => 'Client',
        'party' => $vente->client,
        'reference' => $vente->reference_facture,
        'statusLabel' => ucfirst($vente->statut ?? 'payee'),
        'paymentLabel' => ucfirst($vente->mode_paiement ?? 'especes'),
        'itemName' => $vente->article?->produit?->nom_produit ?? 'Article',
        'quantity' => $vente->quantite,
        'unitPrice' => $vente->prix_unitaire,
        'totalPrice' => $vente->prix_total,
        'backUrl' => route('ventes.index'),
        'pdfUrl' => route('ventes.pdf', $vente),
        'footerNote' => 'Merci pour votre visite',
    ])
</x-app-layout>
