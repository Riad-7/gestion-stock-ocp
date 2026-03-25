<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Produit;
use App\Notifications\ActionNotification;
use App\Notifications\StockBasNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $articles = Article::with('produit')
            ->withSum('commandes as quantite_commandee', 'quantite')
            ->when($request->search, fn ($q, $s) => $q->whereHas('produit', fn ($p) => $p->where('nom_produit', 'like', "%$s%")
            )
            )
            ->when($request->statut, fn ($q, $s) => $q->where('statut', $s)
            )
            // Filtre : afficher seulement les articles en stock bas
            ->when($request->stock_bas, fn ($q) => $q->whereColumn('quantite', '<', 'seuil_minimum')
            )
            ->orderByRaw('quantite < seuil_minimum DESC') // stock bas en premier
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
            'quantite_commandee' => (int) \App\Models\Commande::sum('quantite'),
        ];

        return view('articles.index', compact('articles', 'produits', 'stockBasArticles', 'stats'));
    }

    public function create()
    {
        // Passer la liste des produits pour le select
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
                ->withErrors(['nom_produit' => 'Le nom du produit est obligatoire si aucun produit n\'est selectionne.'])
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

        $request->user()?->notify(new ActionNotification(
            'Nouvel article ajoute au stock.',
            'success'
        ));

        $warning = $this->notifyLowStockIfNeeded($request, $article);

        return redirect()->route('articles.index')
            ->with('success', 'Article ajouté au stock !');
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
            // Note : la quantite est gérée par les Observers, pas manuellement
        ]);

        $article->update($validated);

        return redirect()->route('articles.show', $article)
            ->with('success', 'Article mis à jour !');
    }

    public function destroy(Article $article)
    {
        if ($article->ventes()->count() > 0) {
            return back()->withErrors([
                'error' => "Impossible : cet article a {$article->ventes()->count()} vente(s).",
            ]);
        }

        if ($article->commandes()->count() > 0) {
            return back()->withErrors([
                'error' => 'Impossible : cet article est lié à des commandes.',
            ]);
        }

        $article->delete();

        return redirect()->route('articles.index')
            ->with('success', 'Article supprimé du stock.');
    }
}
