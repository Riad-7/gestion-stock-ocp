<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Article;
use App\Models\Fournisseur;
use Illuminate\Http\Request;

class CommandeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $commandes = Commande::with(['article.produit', 'fournisseur'])
            ->when($request->statut, fn ($q, $s) => $q->where('statut', $s))
            ->when($request->fournisseur_id, fn ($q, $id) => $q->where('id_fournisseur', $id)
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $fournisseurs = Fournisseur::orderBy('nom')->get();

        return view('commandes.index', compact('commandes', 'fournisseurs'));
    }

    public function create()
    {
        $articles = Article::with('produit')->orderBy('id')->get();
        $fournisseurs = Fournisseur::orderBy('nom')->get();

        return view('commandes.create', compact('articles', 'fournisseurs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_article' => 'required|exists:articles,id',
            'id_fournisseur' => 'required|exists:fournisseurs,id',
            'quantite' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0',
            'date_commande' => 'required|date',
        ]);

        Commande::create($validated); // statut = "en_attente" par défaut

        return redirect()->route('commandes.index')
            ->with('success', 'Commande créée. En attente de livraison.');
    }

    public function show(Commande $commande)
    {
        $commande->load(['article.produit', 'fournisseur']);

        return view('commandes.show', compact('commande'));
    }

    public function edit(Commande $commande)
    {
        // On ne peut éditer que les commandes en attente
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
            'id_article' => 'required|exists:articles,id',
            'id_fournisseur' => 'required|exists:fournisseurs,id',
            'quantite' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0',
            'date_commande' => 'required|date',
        ]);

        $commande->update($validated);

        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Commande mise à jour !');
    }

    // ── Action personnalisée : Marquer comme livrée ────────────────────
    // Route : PATCH /commandes/{commande}/livrer
    public function marquerLivree(Commande $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return back()->withErrors(['error' => 'Cette commande ne peut pas être livrée.']);
        }

        // L'Observer CommandeObserver::updated() va incrémenter le stock
        $commande->update(['statut' => 'livree']);

        return back()->with('success',
            "Commande livrée ! Stock mis à jour : +{$commande->quantite} unités."
        );
    }

    public function destroy(Commande $commande)
    {
        if ($commande->statut === 'livree') {
            return back()->withErrors([
                'error' => 'Impossible : une commande livrée ne peut pas être supprimée.',
            ]);
        }

        $commande->update(['statut' => 'annulee']);
        // ou $commande->delete() si tu veux vraiment supprimer

        return redirect()->route('commandes.index')
            ->with('success', 'Commande annulée.');
    }
}
