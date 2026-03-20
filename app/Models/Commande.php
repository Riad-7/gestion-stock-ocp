<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commande extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'article_id',
        'fournisseur_id',
        'quantite',
        'prix_unitaire',
        'prix_total',
        'date_commande',
        'date_livraison',
        'reference_commande',
        'statut'
    ];

    protected $casts = [
        'date_commande' => 'date',
        'date_livraison' => 'date'
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }
}
