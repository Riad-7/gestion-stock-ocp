<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-950">Facture commande</h2>
            <p class="text-sm text-slate-500">Apercu imprimable de la commande fournisseur.</p>
        </div>
    </x-slot>

    @include('partials.print-ticket', [
        'documentTitle' => 'Facture commande',
        'documentDate' => $commande->date_commande,
        'partyLabel' => 'Fournisseur',
        'party' => $commande->fournisseur,
        'reference' => $commande->reference_commande,
        'statusLabel' => ucfirst(str_replace('_', ' ', $commande->statut ?? 'en_attente')),
        'paymentLabel' => null,
        'itemName' => $commande->article?->produit?->nom_produit ?? 'Article',
        'quantity' => $commande->quantite,
        'unitPrice' => $commande->prix_unitaire,
        'totalPrice' => $commande->prix_total,
        'backUrl' => route('commandes.index'),
        'pdfUrl' => route('commandes.pdf', $commande),
        'footerNote' => 'Document de commande',
    ])
</x-app-layout>
