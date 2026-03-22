<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $clients = Client::query()
            ->when($request->search, fn ($q, $s) => $q->where('nom', 'like', "%$s%")
                ->orWhere('prenom', 'like', "%$s%")
                ->orWhere('telephone', 'like', "%$s%")
            )
            ->withCount('ventes')           // nombre de ventes par client
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();

        return view('clients.index', compact('clients'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:clients,email',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Client créé avec succès !');

    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        // Eager loading : charger les ventes avec les articles et produits
        $client->load(['ventes.article.produit']);

        $totalAchats = $client->ventes->sum('prix_total');
        $nbVentes = $client->ventes->count();

        return view('clients.show', compact('client', 'totalAchats', 'nbVentes'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:clients,email,'.$client->id,
        ]);

        $client->update($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Client mis à jour !');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        if ($client->ventes()->count() > 0) {
            return back()->withErrors([
                'error' => "Impossible de supprimer : ce client a {$client->ventes()->count()} vente(s).",
            ]);
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client supprimé.');
    }
}
