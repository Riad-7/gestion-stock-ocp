<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Client;
use App\Models\Vente;
use App\Notifications\ActionNotification;
use App\Support\AdminActionMailer;
use App\Support\ActionLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $ventes = Vente::with(['article.produit', 'client'])
            ->when($request->search, function ($q, $s) {
                $q->where(function ($query) use ($s) {
                    $query->where('reference_facture', 'like', "%{$s}%")
                        ->orWhereHas('article.produit', fn ($p) => $p->where('nom_produit', 'like', "%{$s}%"))
                        ->orWhereHas('client', function ($c) use ($s) {
                            $c->where('nom', 'like', "%{$s}%")
                                ->orWhere('prenom', 'like', "%{$s}%");
                        });
                });
            })
            ->when($request->client_id, fn ($q, $id) => $q->where('client_id', $id)
            )
            ->when($request->mode_paiement, fn ($q, $mode) => $q->where('mode_paiement', $mode)
            )
            ->when($request->date_debut, fn ($q, $d) => $q->whereDate('date_vente', '>=', $d)
            )
            ->when($request->date_fin, fn ($q, $d) => $q->whereDate('date_vente', '<=', $d)
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $clients = Client::orderBy('nom')->get();
        $articles = Article::with('produit')
            ->where('statut', 'actif')
            ->where('quantite', '>', 0)
            ->orderBy('id')
            ->get();
        $totalCA = Vente::sum('prix_total');
        $ventesJour = Vente::whereDate('date_vente', today())->sum('prix_total');

        return view('ventes.index',
            compact('ventes', 'clients', 'articles', 'totalCA', 'ventesJour')
        );
    }

    public function create()
    {
        // Uniquement les articles actifs avec stock > 0
        $articles = Article::with('produit')
            ->where('statut', 'actif')
            ->where('quantite', '>', 0)
            ->get();

        $clients = Client::orderBy('nom')->get();

        return view('ventes.create', compact('articles', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'article_id' => 'required|exists:articles,id',
            'client_id' => 'required|exists:clients,id',
            'quantite' => 'required|integer|min:1',
            'mode_paiement' => 'required|in:especes,carte,cheque,virement',
        ]);

        try {
            $vente = null;

            DB::transaction(function () use ($validated, &$vente) {
                $article = Article::findOrFail($validated['article_id']);

                $vente = Vente::create([
                    'article_id' => $validated['article_id'],
                    'client_id' => $validated['client_id'],
                    'quantite' => $validated['quantite'],
                    'prix_unitaire' => $article->prix_unitaire,
                    'prix_total' => $article->prix_unitaire * $validated['quantite'],
                    'date_vente' => now(),
                    'mode_paiement' => $validated['mode_paiement'],
                    'reference_facture' => 'VNT-'.strtoupper(uniqid()),
                    'statut' => 'payee',
                ]);
            });

            $vente?->loadMissing(['article.produit', 'client']);

            if ($vente) {
                ActionLogger::log(
                    'vente.create',
                    "Vente {$vente->reference_facture} enregistree.",
                    $vente,
                    ['total' => $vente->prix_total, 'mode_paiement' => $vente->mode_paiement],
                    $request,
                );

                AdminActionMailer::send(
                    'Nouvelle vente enregistree',
                    'Une nouvelle vente a ete enregistree dans le systeme.',
                    [
                        'Reference' => $vente->reference_facture,
                        'Article' => $vente->article?->produit?->nom_produit ?? 'Article',
                        'Client' => trim(($vente->client?->nom ?? '').' '.($vente->client?->prenom ?? '')),
                        'Quantite' => (string) $vente->quantite,
                        'Prix unitaire' => number_format((float) $vente->prix_unitaire, 2).' DH',
                        'Total' => number_format((float) $vente->prix_total, 2).' DH',
                        'Paiement' => (string) $vente->mode_paiement,
                        'Date vente' => optional($vente->date_vente)->format('d/m/Y H:i') ?? '--',
                    ]
                );
            }

            $request->user()?->notify(new ActionNotification(
                'Nouvelle vente enregistree avec succes.',
                'success'
            ));

            return redirect()->route('ventes.index')
                ->with('success', 'Vente enregistrée avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Vente $vente)
    {
        $vente->load(['article.produit', 'client']);

        return view('ventes.show', compact('vente'));
    }

    public function pdf(Vente $vente)
    {
        $vente->load(['article.produit', 'client']);

        $pdf = Pdf::loadView('pdf.invoice', [
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
            'footerNote' => 'Merci pour votre visite',
        ])->setPaper('a4');

        return $pdf->download(($vente->reference_facture ?: 'facture-vente').'.pdf');
    }

    // Les ventes ne s'éditent pas (elles s'annulent)
    // destroy() : annuler une vente → l'Observer remet le stock
    public function destroy(Vente $vente)
    {
        $vente->loadMissing(['article.produit', 'client']);
        $reference = $vente->reference_facture;

        DB::transaction(function () use ($vente) {
            $vente->delete(); // l'Observer VenteObserver::deleted() remet le stock
        });

        ActionLogger::log(
            'vente.delete',
            "Vente {$reference} annulee.",
            null,
            ['reference_facture' => $reference],
            request(),
        );

        request()->user()?->notify(new ActionNotification(
            'Vente annulee et stock restaure.',
            'warning'
        ));

        return redirect()->route('ventes.index')
            ->with('success', 'Vente annulée. Stock restauré.');
    }
}
