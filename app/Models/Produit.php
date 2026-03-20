<?php

namespace App\Models;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference', 'nom_produit', 'slug', 'description', 'marque', 'categorie', 'image', 'is_active'
    ];

    protected function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function getStockTotalAttribute(): int {
        return $this->articles->sum('quantite');
    }

}
