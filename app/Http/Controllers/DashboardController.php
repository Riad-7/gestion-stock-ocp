<?php

namespace App\Http\Controllers;

use App\Models\ActionLog;
use App\Models\Article;
use App\Models\Commande;
use App\Models\Vente;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'stock_total' => Article::where('statut', 'actif')->sum('quantite'),
            'nb_stock_bas' => Article::whereColumn('quantite', '<', 'seuil_minimum')
                ->where('statut', 'actif')
                ->count(),
            'nb_expires' => Article::where('statut', 'expire')->count(),
            'ventes_jour' => Vente::whereDate('date_vente', today())->count(),
            'ca_jour' => Vente::whereDate('date_vente', today())->sum('prix_total'),
            'commandes_attente' => Commande::where('statut', 'en_attente')->count(),
            'operations_today' => ActionLog::whereDate('created_at', today())->count(),
        ];

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

        $notifications = auth()->user()->unreadNotifications->take(5);
        $latestBackup = $this->latestBackup();

        return view('dashboard-modern', compact(
            'data',
            'articles_stock_bas',
            'articles_expires',
            'ventes_recentes',
            'notifications',
            'latestBackup',
        ));
    }

    private function latestBackup(): ?array
    {
        $backupDirectory = storage_path('app/backups');

        if (! File::isDirectory($backupDirectory)) {
            return null;
        }

        $latestFile = collect(File::files($backupDirectory))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->first();

        if (! $latestFile) {
            return null;
        }

        return [
            'name' => $latestFile->getFilename(),
            'size_kb' => round($latestFile->getSize() / 1024, 2),
            'updated_at' => Carbon::createFromTimestamp($latestFile->getMTime()),
        ];
    }
}
