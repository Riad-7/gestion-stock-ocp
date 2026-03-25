<?php

namespace App\Notifications;


use App\Models\Article;
use Illuminate\Notifications\Notification;

class ArticleExpireNotification extends Notification
{
    public function __construct(public Article $article) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message'       => "Article expiré : {$this->article->produit->nom_produit}",
            'date_expiration' => $this->article->date_expiration->format('d/m/Y'),
            'article_id'    => $this->article->id,
            'type'          => 'article_expire',
        ];
    }
}
