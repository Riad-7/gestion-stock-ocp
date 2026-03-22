<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fournisseurs = Fournisseur::query()
            ->when($request->search, fn ($q, $s) => $q->where('nom', 'like', "%$s%")
                ->orWhere('entreprise', 'like', "%$s%")
            )
            ->withCount('commandes')
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();

        return view('fournisseurs.index', compact('fournisseurs'));
    }

    public function create()
    {
        return view('fournisseurs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:fournisseurs,email',
            'entreprise' => 'nullable|string|max:150',
        ]);

        Fournisseur::create($validated);

        return redirect()->route('fournisseurs.index')
            ->with('success', 'Fournisseur ajouté !');
    }

    public function show(Fournisseur $fournisseur)
    {
        $fournisseur->load(['commandes.article.produit']);

        $totalCommandes = $fournisseur->commandes->sum('prix');
        $nbCommandes = $fournisseur->commandes->count();

        return view('fournisseurs.show',
            compact('fournisseur', 'totalCommandes', 'nbCommandes')
        );
    }

    public function edit(Fournisseur $fournisseur)
    {
        return view('fournisseurs.edit', compact('fournisseur'));
    }

    public function update(Request $request, Fournisseur $fournisseur)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:fournisseurs,email,'.$fournisseur->id,
            'entreprise' => 'nullable|string|max:150',
        ]);

        $fournisseur->update($validated);

        return redirect()->route('fournisseurs.show', $fournisseur)
            ->with('success', 'Fournisseur mis à jour !');
    }

    public function destroy(Fournisseur $fournisseur)
    {
        if ($fournisseur->commandes()->count() > 0) {
            return back()->withErrors([
                'error' => 'Impossible : ce fournisseur a des commandes enregistrées.',
            ]);
        }

        $fournisseur->delete();

        return redirect()->route('fournisseurs.index')
            ->with('success', 'Fournisseur supprimé.');
    }
}
