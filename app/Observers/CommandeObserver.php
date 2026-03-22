<?php
// app/Observers/CommandeObserver.php
namespace App\Observers;

use App\Models\Commande;

class CommandeObserver
{
    public function updated(Commande $commande): void
    {
        // ✅ Stock augmente uniquement quand la commande est marquée "livrée"
        if ($commande->isDirty('statut')
            && $commande->statut === 'livree'
            && $commande->getOriginal('statut') !== 'livree')
        {
            $commande->article->increment('quantite', $commande->quantite);

            // Remettre le statut "actif" si l'article était épuisé
            $article = $commande->article;
            if ($article->statut === 'epuise') {
                $article->update(['statut' => 'actif']);
            }
        }
    }
}

