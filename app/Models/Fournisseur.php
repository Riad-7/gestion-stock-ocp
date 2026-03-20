<?php

namespace App\Models;

use App\Models\Commande;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fournisseur extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'nom', 'prenom', 'telephone', 'adresse', 'email', 'entreprise',
    ];

    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class);
    }
}
