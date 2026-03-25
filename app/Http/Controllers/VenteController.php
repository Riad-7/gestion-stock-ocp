<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Client;
use App\Models\Vente;
use App\Notifications\ActionNotification;
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
            ->when($request->client_id, fn ($q, $id) => $q->where('client_id', $id)
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
            DB::transaction(function () use ($validated) {
                $article = Article::findOrFail($validated['article_id']);

                Vente::create([
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

    // Les ventes ne s'éditent pas (elles s'annulent)
    // destroy() : annuler une vente → l'Observer remet le stock
    public function destroy(Vente $vente)
    {
        DB::transaction(function () use ($vente) {
            $vente->delete(); // l'Observer VenteObserver::deleted() remet le stock
        });

        request()->user()?->notify(new ActionNotification(
            'Vente annulee et stock restaure.',
            'warning'
        ));

        return redirect()->route('ventes.index')
            ->with('success', 'Vente annulée. Stock restauré.');
    }
}
