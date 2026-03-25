<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $produits = Produit::query()
            ->when($request->search, fn ($q, $s) => $q->where('nom_produit', 'like', "%$s%")
                ->orWhere('marque', 'like', "%$s%")
                ->orWhere('categorie', 'like', "%$s%")
            )
            ->when($request->categorie, fn ($q, $c) => $q->where('categorie', $c)
            )
            ->withCount('articles')          // nb de lots
            ->withSum('articles', 'quantite') // stock total
            ->orderBy('nom_produit')
            ->paginate(20)
            ->withQueryString();

        // Liste des catégories pour le filtre
        $categories = Produit::distinct()->pluck('categorie')->filter();

        return view('produits.index', compact('produits', 'categories'));
    }

    public function create()
    {
        return view('produits.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_produit' => 'required|string|max:150',
            'description' => 'nullable|string',
            'marque' => 'nullable|string|max:100',
            'categorie' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpg,png,webp|max:2048',
        ]);

        // Gérer l'upload de l'image si fournie
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('produits', 'public');
        }

        Produit::create($validated);

        return redirect()->route('produits.index')
            ->with('success', 'Produit créé !');
    }

    public function show(Produit $produit)
    {
        // Charger tous les articles de ce produit
        $produit->load('articles');

        $stockTotal = $produit->articles->sum('quantite');
        $nbExpires = $produit->articles->where('statut', 'expire')->count();
        $nbActifs = $produit->articles->where('statut', 'actif')->count();

        return view('produits.show',
            compact('produit', 'stockTotal', 'nbExpires', 'nbActifs')
        );
    }

    public function edit(Produit $produit)
    {
        return view('produits.edit', compact('produit'));
    }

    public function update(Request $request, Produit $produit)
    {
        $validated = $request->validate([
            'nom_produit' => 'required|string|max:150',
            'description' => 'nullable|string',
            'marque' => 'nullable|string|max:100',
            'categorie' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($produit->image) {
                \Storage::disk('public')->delete($produit->image);
            }
            $validated['image'] = $request->file('image')
                ->store('produits', 'public');
        }

        $produit->update($validated);

        return redirect()->route('produits.show', $produit)
            ->with('success', 'Produit mis à jour !');
    }

    public function destroy(Produit $produit)
    {
        // Vérifier si des articles sont liés
        if ($produit->articles()->count() > 0) {
            return back()->withErrors([
                'error' => 'Impossible : ce produit a des articles en stock.',
            ]);
        }

        // Supprimer l'image associée
        if ($produit->image) {
            \Storage::disk('public')->delete($produit->image);
        }

        $produit->delete();

        return redirect()->route('produits.index')
            ->with('success', 'Produit supprimé.');
    }
}
