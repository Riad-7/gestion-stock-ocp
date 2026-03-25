<?php

namespace App\Notifications;

use App\Models\Article;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StockBasNotification extends Notification
{
    public function __construct(public Article $article) {}

    // Canaux : stockage en BDD + email
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    // Données stockées en BDD (table notifications)
    public function toDatabase(object $notifiable): array
    {
        return [
            'message'         => "Stock bas : {$this->article->produit->nom_produit}",
            'quantite'        => $this->article->quantite,
            'seuil_minimum'   => $this->article->seuil_minimum,
            'article_id'      => $this->article->id,
            'type'            => 'stock_bas',
        ];
    }

    // Email envoyé à l'admin
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ Alerte Stock Bas')
            ->line("Le stock de **{$this->article->produit->nom_produit}** est bas.")
            ->line("Quantité actuelle : {$this->article->quantite}")
            ->line("Seuil minimum : {$this->article->seuil_minimum}")
            ->action('Voir les articles', route('articles.index'));
    }
}
