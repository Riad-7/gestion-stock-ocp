<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'article_id',
        'client_id',
        'quantite',
        'prix_total',
        'prix_unitaire',
        'date_vente',
        'mode_paiement',
        'reference_facture',
        'statut'
    ];

    protected $casts = [
        'date_vente' => 'date'
    ];

    public function article(): BelongsTo
    {   
        return $this->belongsTo(Article::class); 
    }

    public function client(): BelongsTo
    {   
        return $this->belongsTo(Client::class); 
    }
}
