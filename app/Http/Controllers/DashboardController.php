<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Vente;
use App\Models\Commande;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── KPIs principaux ────────────────────────────
        $data = [
            // Total du stock actif
            'stock_total' => Article::where('statut', 'actif')->sum('quantite'),

            // Articles dont quantite < seuil_minimum
            'nb_stock_bas' => Article::whereColumn('quantite', '<', 'seuil_minimum')
                                     ->where('statut', 'actif')
                                     ->count(),

            // Articles expirés
            'nb_expires' => Article::where('statut', 'expire')->count(),

            // Ventes du jour
            'ventes_jour' => Vente::whereDate('date_vente', today())->count(),

            // Chiffre d'affaires du jour
            'ca_jour' => Vente::whereDate('date_vente', today())->sum('prix_total'),

            // Commandes en attente
            'commandes_attente' => Commande::where('statut', 'en_attente')->count(),
        ];

        // ── Listes pour les tableaux du dashboard ──────
        $articles_stock_bas = Article::with('produit')
            ->whereColumn('quantite', '<', 'seuil_minimum')
            ->where('statut', 'actif')
            ->orderBy('quantite')
            ->take(10)
            ->get();

        $articles_expires = Article::with('produit')
            ->where('statut', 'expire')
            ->orderBy('date_expiration', 'desc')
            ->take(10)
            ->get();

        $ventes_recentes = Vente::with(['article.produit', 'client'])
            ->latest()
            ->take(5)
            ->get();

        // ── Notifications non lues ─────────────────────
        $notifications = auth()->user()->unreadNotifications->take(5);

        return view('dashboard-modern', compact(
            'data', 'articles_stock_bas', 'articles_expires',
            'ventes_recentes', 'notifications'
        ));
    }
}
