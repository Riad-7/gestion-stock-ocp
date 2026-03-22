<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Article;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class VenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $ventes = Vente::with(['article.produit', 'client'])
            ->when($request->client_id, fn ($q, $id) => $q->where('id_client', $id)
            )
            ->when($request->date_debut, fn ($q, $d) => $q->whereDate('date_vente', '>=', $d)
            )
            ->when($request->date_fin, fn ($q, $d) => $q->whereDate('date_vente', '<=', $d)
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $clients = Client::orderBy('nom')->get();
        $totalCA = Vente::sum('prix');
        $ventesJour = Vente::whereDate('date_vente', today())->sum('prix');

        return view('ventes.index',
            compact('ventes', 'clients', 'totalCA', 'ventesJour')
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
            'id_article' => 'required|exists:articles,id',
            'id_client' => 'required|exists:clients,id',
            'quantite' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                Vente::create([
                    ...$validated,
                    'date_vente' => now(),
                ]);
            });

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

    // Les ventes ne s'éditent pas (elles s'annulent)
    // destroy() : annuler une vente → l'Observer remet le stock
    public function destroy(Vente $vente)
    {
        DB::transaction(function () use ($vente) {
            $vente->delete(); // l'Observer VenteObserver::deleted() remet le stock
        });

        return redirect()->route('ventes.index')
            ->with('success', 'Vente annulée. Stock restauré.');
    }
}
