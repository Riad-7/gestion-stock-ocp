<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'produit_id',
        'quantite',
        'seuil_minimum',
        'prix_unitaire',
        'date_fabrication',
        'date_expiration',
        'statut'
    ];

    protected $casts = [
        'date_expiration'  => 'date',
        'date_fabrication' => 'date',
    ];
    
    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }

    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class);
    }

    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class);
    }

    public function getEstExpireAttribute(): bool {
        return $this->date_expiration &&
               $this->date_expiration->isPast();
    }

    public function getStockBasAttribute(): bool {
        return $this->quantite < $this->seuil_minimum;
    }

    public function getEstDisponibleAttribute(): bool {
        return $this->statut === 'actif'
            && $this->quantite > 0
            && !$this->est_expire;
    }

}
