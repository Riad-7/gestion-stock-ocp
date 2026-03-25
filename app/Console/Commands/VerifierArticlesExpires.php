<?php
namespace App\Console\Commands;

use App\Models\Article;
use App\Models\User;
use App\Notifications\ArticleExpireNotification;
use Illuminate\Console\Command;

class VerifierArticlesExpires extends Command
{
    protected $signature   = 'stock:verifier-expirations';
    protected $description = 'Marque les articles expirés et notifie les admins';

    public function handle(): void
    {
        $this->info('Vérification des articles expirés...');

        // Trouver tous les articles actifs dont la date est dépassée
        $articlesExpires = Article::where('statut', 'actif')
            ->where('date_expiration', '<', now())
            ->whereNotNull('date_expiration')
            ->with('produit')
            ->get();

        if ($articlesExpires->isEmpty()) {
            $this->info('Aucun article expiré trouvé.');
            return;
        }

        $admins = User::where('role', 'admin')->get();

        foreach ($articlesExpires as $article) {
            // Mettre à jour le statut
            $article->update(['statut' => 'expire']);

            // Notifier chaque admin
            foreach ($admins as $admin) {
                $admin->notify(new ArticleExpireNotification($article));
            }

            $this->line("  → Expiré : {$article->produit->nom_produit}");
        }

        $this->info("Total : {$articlesExpires->count()} article(s) marqués expirés.");
    }
}
