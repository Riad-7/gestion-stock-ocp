<?php
// app/Observers/VenteObserver.php
namespace App\Observers;

use App\Models\Vente;
use App\Models\Article;
use App\Notifications\StockBasNotification;
use App\Notifications\ArticleExpireNotification;
use Illuminate\Support\Facades\DB;

class VenteObserver
{
    public function creating(Vente $vente): void
    {
        $article = Article::findOrFail($vente->id_article);

        // ✅ Vérification 1 : article expiré ?
        if ($article->est_expire) {
            throw new \Exception("Cet article est expiré. Vente impossible.");
        }

        // ✅ Vérification 2 : stock suffisant ?
        if ($article->quantite < $vente->quantite) {
            throw new \Exception(
                "Stock insuffisant. Disponible : {$article->quantite}, Demandé : {$vente->quantite}"
            );
        }

        // ✅ Calcul du prix total automatique
        $vente->prix = $article->prix_unitaire * $vente->quantite;
    }

    public function created(Vente $vente): void
    {
        $article = $vente->article;

        // ✅ Décrémenter le stock
        $article->decrement('quantite', $vente->quantite);
        $article->refresh();

        // ✅ Vérifier si stock bas → envoyer notification
        if ($article->stock_bas) {
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new StockBasNotification($article));
            }
        }

        // ✅ Marquer comme épuisé si quantite = 0
        if ($article->quantite === 0) {
            $article->update(['statut' => 'epuise']);
        }
    }

    public function deleted(Vente $vente): void
    {
        // ✅ Si la vente est annulée, remettre le stock
        $vente->article->increment('quantite', $vente->quantite);
    }
}

