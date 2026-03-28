<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Commande;
use App\Models\Fournisseur;
use App\Models\Produit;
use App\Notifications\ActionNotification;
use App\Support\AdminActionMailer;
use App\Support\ActionLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommandeFlowController extends Controller
{
    public function index(Request $request)
    {
        $commandes = Commande::with(['article.produit', 'fournisseur'])
            ->when($request->search, function ($q, $s) {
                $q->where(function ($query) use ($s) {
                    $query->where('reference_commande', 'like', "%{$s}%")
                        ->orWhereHas('article.produit', fn ($p) => $p->where('nom_produit', 'like', "%{$s}%"))
                        ->orWhereHas('fournisseur', function ($f) use ($s) {
                            $f->where('nom', 'like', "%{$s}%")
                                ->orWhere('prenom', 'like', "%{$s}%");
                        });
                });
            })
            ->when($request->statut, fn ($q, $s) => $q->where('statut', $s))
            ->when($request->fournisseur_id, fn ($q, $id) => $q->where('fournisseur_id', $id))
            ->when($request->date_debut, fn ($q, $d) => $q->whereDate('date_commande', '>=', $d))
            ->when($request->date_fin, fn ($q, $d) => $q->whereDate('date_commande', '<=', $d))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $fournisseurs = Fournisseur::orderBy('nom')->get();
        $stats = [
            'en_attente' => Commande::where('statut', 'en_attente')->count(),
            'livree' => Commande::where('statut', 'livree')->count(),
            'annulee' => Commande::where('statut', 'annulee')->count(),
        ];

        return view('commandes.index', compact('commandes', 'fournisseurs', 'stats'));
    }

    public function create()
    {
        $fournisseurs = Fournisseur::orderBy('nom')->get();

        return view('commandes.create', compact('fournisseurs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_produit' => 'required|string|max:150',
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'quantite' => 'required|integer|min:1',
            'prix_unitaire' => 'required|numeric|min:0',
            'seuil_minimum' => 'required|integer|min:0',
            'date_commande' => 'required|date',
            'date_livraison' => 'nullable|date|after_or_equal:date_commande',
            'date_fabrication' => 'nullable|date',
            'date_expiration' => 'nullable|date|after_or_equal:date_fabrication',
            'reference_commande' => 'nullable|string|max:255',
        ]);

        $commande = DB::transaction(function () use ($validated) {
            $produit = Produit::firstOrCreate(
                ['nom_produit' => $validated['nom_produit']],
                [
                    'reference' => 'PRD-'.Str::upper(Str::random(8)),
                    'description' => null,
                    'marque' => null,
                    'categorie' => null,
                    'is_active' => true,
                ]
            );

            $article = Article::create([
                'produit_id' => $produit->id,
                'quantite' => 0,
                'seuil_minimum' => $validated['seuil_minimum'],
                'prix_unitaire' => $validated['prix_unitaire'],
                'date_fabrication' => $validated['date_fabrication'] ?? null,
                'date_expiration' => $validated['date_expiration'] ?? null,
                'statut' => 'epuise',
            ]);

            return Commande::create([
                'article_id' => $article->id,
                'fournisseur_id' => $validated['fournisseur_id'],
                'quantite' => $validated['quantite'],
                'prix_unitaire' => $validated['prix_unitaire'],
                'prix_total' => $validated['quantite'] * $validated['prix_unitaire'],
                'date_commande' => $validated['date_commande'],
                'date_livraison' => $validated['date_livraison'] ?? null,
                'reference_commande' => $validated['reference_commande'] ?: 'CMD-'.strtoupper(uniqid()),
                'statut' => 'en_attente',
            ]);
        });

        $commande->loadMissing(['article.produit', 'fournisseur']);

        ActionLogger::log(
            'commande.create',
            "Commande {$commande->reference_commande} ajoutee.",
            $commande,
            ['statut' => $commande->statut, 'total' => $commande->prix_total],
            $request,
        );

        AdminActionMailer::send(
            'Nouvelle commande enregistree',
            'Une nouvelle commande fournisseur a ete creee dans le systeme.',
            [
                'Reference' => $commande->reference_commande,
                'Article' => $commande->article?->produit?->nom_produit ?? 'Article',
                'Fournisseur' => trim(($commande->fournisseur?->nom ?? '').' '.($commande->fournisseur?->prenom ?? '')),
                'Quantite' => (string) $commande->quantite,
                'Prix unitaire' => number_format((float) $commande->prix_unitaire, 2).' DH',
                'Total' => number_format((float) $commande->prix_total, 2).' DH',
                'Statut' => (string) $commande->statut,
                'Date commande' => optional($commande->date_commande)->format('d/m/Y') ?? '--',
            ]
        );

        $request->user()?->notify(new ActionNotification(
            'Nouvelle commande ajoutee au suivi.',
            'success'
        ));

        return redirect()->route('commandes.index')
            ->with('success', 'Commande creee. L article apparait dans le stock avec quantite 0 jusqu a la livraison.');
    }

    public function show(Commande $commande)
    {
        $commande->load(['article.produit', 'fournisseur']);

        return view('commandes.show', compact('commande'));
    }

    public function pdf(Commande $commande)
    {
        $commande->load(['article.produit', 'fournisseur']);

        $pdf = Pdf::loadView('pdf.invoice', [
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
            'footerNote' => 'Document de commande',
        ])->setPaper('a4');

        return $pdf->download(($commande->reference_commande ?: 'facture-commande').'.pdf');
    }

    public function edit(Commande $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return back()->withErrors(['error' => 'Seules les commandes en attente sont modifiables.']);
        }

        $articles = Article::with('produit')->get();
        $fournisseurs = Fournisseur::orderBy('nom')->get();

        return view('commandes.edit', compact('commande', 'articles', 'fournisseurs'));
    }

    public function update(Request $request, Commande $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return back()->withErrors(['error' => 'Commande non modifiable.']);
        }

        $validated = $request->validate([
            'article_id' => 'required|exists:articles,id',
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'quantite' => 'required|integer|min:1',
            'prix_unitaire' => 'required|numeric|min:0',
            'date_commande' => 'required|date',
            'date_livraison' => 'nullable|date|after_or_equal:date_commande',
            'reference_commande' => 'nullable|string|max:255',
            'statut' => 'required|in:en_attente,livree,annulee',
        ]);

        $commande->update([
            ...$validated,
            'prix_total' => $validated['quantite'] * $validated['prix_unitaire'],
        ]);

        ActionLogger::log(
            'commande.update',
            "Commande {$commande->reference_commande} mise a jour.",
            $commande,
            ['statut' => $commande->statut, 'total' => $commande->prix_total],
            $request,
        );

        $request->user()?->notify(new ActionNotification(
            'Commande mise a jour.',
            'info'
        ));

        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Commande mise a jour.');
    }

    public function updateStatut(Request $request, Commande $commande)
    {
        $validated = $request->validate([
            'statut' => 'required|in:en_attente,livree,annulee',
        ]);

        $nouveauStatut = $validated['statut'];

        if ($commande->statut === 'livree' && $nouveauStatut !== 'livree') {
            return back()->withErrors([
                'error' => 'Une commande livree ne peut plus changer de statut.',
            ]);
        }

        if ($commande->statut === 'annulee' && $nouveauStatut !== 'annulee') {
            return back()->withErrors([
                'error' => 'Une commande annulee ne peut plus etre reactivee.',
            ]);
        }

        if ($commande->statut === $nouveauStatut) {
            return back()->with('success', 'Le statut de la commande est deja a jour.');
        }

        $payload = ['statut' => $nouveauStatut];

        if ($nouveauStatut === 'livree' && empty($commande->date_livraison)) {
            $payload['date_livraison'] = now()->toDateString();
        }

        $commande->update($payload);

        ActionLogger::log(
            'commande.status',
            "Commande {$commande->reference_commande} passe a {$nouveauStatut}.",
            $commande,
            ['statut' => $nouveauStatut],
            $request,
        );

        $request->user()?->notify(new ActionNotification(
            "Statut de commande change en {$nouveauStatut}.",
            $nouveauStatut === 'annulee' ? 'warning' : 'info'
        ));

        return back()->with('success', 'Statut de commande mis a jour.');
    }

    public function marquerLivree(Commande $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return back()->withErrors(['error' => 'Cette commande ne peut pas etre livree.']);
        }

        $commande->update([
            'statut' => 'livree',
            'date_livraison' => now()->toDateString(),
        ]);

        ActionLogger::log(
            'commande.delivery',
            "Commande {$commande->reference_commande} marquee comme livree.",
            $commande,
            ['date_livraison' => $commande->date_livraison],
            request(),
        );

        request()->user()?->notify(new ActionNotification(
            "Commande {$commande->reference_commande} marquee comme livree.",
            'success'
        ));

        return back()->with('success', "Commande livree. Stock ajoute: +{$commande->quantite} unites.");
    }

    public function destroy(Commande $commande)
    {
        if ($commande->statut === 'livree') {
            return back()->withErrors([
                'error' => 'Impossible: une commande livree ne peut pas etre annulee.',
            ]);
        }

        $reference = $commande->reference_commande;
        $request = request();
        $commande->delete();

        ActionLogger::log(
            'commande.delete',
            "Commande {$reference} supprimee.",
            null,
            ['reference_commande' => $reference],
            $request,
        );

        request()->user()?->notify(new ActionNotification(
            "Commande {$reference} supprimee du suivi.",
            'warning'
        ));

        return redirect()->route('commandes.index')
            ->with('success', 'Commande supprimee du suivi.');
    }
}
