<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Commande;
use App\Models\Produit;
use App\Notifications\ActionNotification;
use App\Notifications\StockBasNotification;
use App\Support\ActionLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleFlowController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::with('produit')
            ->withSum('commandes as quantite_commandee', 'quantite')
            ->when($request->search, function ($q, $s) {
                $q->whereHas('produit', function ($p) use ($s) {
                    $p->where('nom_produit', 'like', "%{$s}%")
                        ->orWhere('reference', 'like', "%{$s}%");
                });
            })
            ->when($request->statut, fn ($q, $s) => $q->where('statut', $s))
            ->when($request->stock_bas, fn ($q) => $q->whereColumn('quantite', '<', 'seuil_minimum'))
            ->orderByRaw('quantite < seuil_minimum DESC')
            ->orderBy('date_expiration')
            ->paginate(20)
            ->withQueryString();

        $produits = Produit::orderBy('nom_produit')->get();
        $stockBasArticles = Article::with('produit')
            ->whereColumn('quantite', '<', 'seuil_minimum')
            ->orderByRaw('(seuil_minimum - quantite) DESC')
            ->take(5)
            ->get();
        $stats = [
            'stock_bas' => Article::whereColumn('quantite', '<', 'seuil_minimum')->count(),
            'expires' => Article::where('statut', 'expire')->count(),
            'quantite_commandee' => (int) Commande::sum('quantite'),
        ];

        return view('articles.index', compact('articles', 'produits', 'stockBasArticles', 'stats'));
    }

    public function create()
    {
        $produits = Produit::orderBy('nom_produit')->get();

        return view('articles.create', compact('produits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'produit_id' => 'nullable|exists:produits,id',
            'nom_produit' => 'nullable|string|max:150',
            'quantite' => 'required|integer|min:0',
            'prix_unitaire' => 'required|numeric|min:0',
            'seuil_minimum' => 'required|integer|min:0',
            'date_fabrication' => 'nullable|date',
            'date_expiration' => 'nullable|date|after_or_equal:date_fabrication',
            'statut' => 'required|in:actif,expire,epuise',
        ]);

        if (empty($validated['produit_id']) && empty($validated['nom_produit'])) {
            return back()
                ->withErrors(['nom_produit' => 'Le nom du produit est obligatoire si aucun produit n est selectionne.'])
                ->withInput();
        }

        if (! empty($validated['produit_id'])) {
            $validated['produit_id'] = (int) $validated['produit_id'];
        } else {
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

            $validated['produit_id'] = $produit->id;
        }

        unset($validated['nom_produit']);

        $article = Article::create($validated);

        ActionLogger::log(
            'article.create',
            "Article {$article->produit?->nom_produit} ajoute au stock.",
            $article,
            ['statut' => $article->statut, 'quantite' => $article->quantite],
            $request,
        );

        $request->user()?->notify(new ActionNotification(
            'Nouvel article ajoute au stock.',
            'success'
        ));

        $warning = $this->notifyLowStockIfNeeded($request, $article);

        return redirect()->route('articles.index')
            ->with('success', 'Article ajoute au stock.')
            ->with('warning', $warning);
    }

    public function show(Article $article)
    {
        $article->load([
            'produit',
            'ventes.client',
            'commandes.fournisseur',
        ]);

        return view('articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        $produits = Produit::orderBy('nom_produit')->get();

        return view('articles.edit', compact('article', 'produits'));
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'prix_unitaire' => 'required|numeric|min:0',
            'seuil_minimum' => 'required|integer|min:0',
            'date_fabrication' => 'nullable|date',
            'date_expiration' => 'nullable|date|after_or_equal:date_fabrication',
            'statut' => 'required|in:actif,expire,epuise',
        ]);

        $article->update($validated);

        ActionLogger::log(
            'article.update',
            "Article {$article->produit?->nom_produit} mis a jour.",
            $article,
            ['statut' => $article->statut, 'prix_unitaire' => $article->prix_unitaire],
            $request,
        );

        $request->user()?->notify(new ActionNotification(
            'Article mis a jour.',
            'info'
        ));

        $warning = $this->notifyLowStockIfNeeded($request, $article->fresh());

        return redirect()->route('articles.show', $article)
            ->with('success', 'Article mis a jour.')
            ->with('warning', $warning);
    }

    public function updateStatut(Request $request, Article $article)
    {
        $validated = $request->validate([
            'statut' => 'required|in:actif,expire,epuise',
        ]);

        if ($validated['statut'] === 'actif' && $article->est_expire) {
            return back()->withErrors([
                'error' => 'Impossible d activer un article deja expire.',
            ]);
        }

        if ($validated['statut'] === 'actif' && $article->quantite === 0) {
            return back()->withErrors([
                'error' => 'Impossible d activer un article avec un stock nul.',
            ]);
        }

        $article->update(['statut' => $validated['statut']]);

        ActionLogger::log(
            'article.status',
            "Statut de l article {$article->produit?->nom_produit} change en {$validated['statut']}.",
            $article,
            ['statut' => $validated['statut']],
            $request,
        );

        $request->user()?->notify(new ActionNotification(
            "Statut de l article change en {$validated['statut']}.",
            $validated['statut'] === 'actif' ? 'success' : 'warning'
        ));

        $warning = $this->notifyLowStockIfNeeded($request, $article->fresh());

        return redirect()->route('articles.index')
            ->with('success', 'Statut de l article mis a jour.')
            ->with('warning', $warning);
    }

    public function restock(Request $request, Article $article)
    {
        $validated = $request->validate([
            'quantite_ajout' => 'required|integer|min:1|max:100000',
        ]);

        DB::transaction(function () use ($article, $validated) {
            $article->increment('quantite', $validated['quantite_ajout']);
            $article->refresh();

            if ($article->statut === 'epuise' && ! $article->est_expire && $article->quantite > 0) {
                $article->update(['statut' => 'actif']);
            }
        });

        $article->refresh();

        ActionLogger::log(
            'article.restock',
            "Stock ajoute pour {$article->produit?->nom_produit}.",
            $article,
            ['quantite_ajout' => $validated['quantite_ajout'], 'quantite_totale' => $article->quantite],
            $request,
        );

        $request->user()?->notify(new ActionNotification(
            "Stock ajoute pour {$article->produit?->nom_produit}: +{$validated['quantite_ajout']} unites.",
            'success'
        ));

        $warning = $this->notifyLowStockIfNeeded($request, $article);

        return redirect()->route('articles.index')
            ->with('success', "Stock mis a jour: +{$validated['quantite_ajout']} unites ajoutees.")
            ->with('warning', $warning);
    }

    public function destroy(Article $article)
    {
        $article->loadMissing('produit');

        if ($article->ventes()->count() > 0) {
            $message = "Impossible : cet article a {$article->ventes()->count()} vente(s).";

            return request()->expectsJson()
                ? response()->json(['message' => $message], 422)
                : back()->withErrors(['error' => $message]);
        }

        if ($article->commandes()->count() > 0) {
            $message = 'Impossible : cet article est lie a des commandes.';

            return request()->expectsJson()
                ? response()->json(['message' => $message], 422)
                : back()->withErrors(['error' => $message]);
        }

        $articleName = $article->produit?->nom_produit ?? 'Article';
        $request = request();

        $article->forceDelete();

        ActionLogger::log(
            'article.delete',
            "Article {$articleName} supprime du stock.",
            null,
            ['article_id' => $article->id, 'nom_produit' => $articleName],
            $request,
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Article supprime du stock.',
                'article_id' => $article->id,
            ]);
        }

        return redirect()->route('articles.index')
            ->with('success', 'Article supprime du stock.');
    }

    private function notifyLowStockIfNeeded(Request $request, Article $article): ?string
    {
        if (! $article->stock_bas) {
            return null;
        }

        $request->user()?->notify(new StockBasNotification($article));

        return "Quantite faible pour {$article->produit?->nom_produit}: {$article->quantite} restante(s), seuil minimum {$article->seuil_minimum}.";
    }
}
