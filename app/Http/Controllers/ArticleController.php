<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Produit;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $articles = Article::with('produit')
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

        return view('articles.index', compact('articles'));
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
            'id_produit' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:0',
            'prix_unitaire' => 'required|numeric|min:0',
            'seuil_minimum' => 'required|integer|min:0',
            'date_fabrication' => 'nullable|date',
            'date_expiration' => 'nullable|date|after_or_equal:date_fabrication',
        ]);

        Article::create($validated);

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
            'id_produit' => 'required|exists:produits,id',
            'prix_unitaire' => 'required|numeric|min:0',
            'seuil_minimum' => 'required|integer|min:0',
            'date_fabrication' => 'nullable|date',
            'date_expiration' => 'nullable|date|after_or_equal:date_fabrication',
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
