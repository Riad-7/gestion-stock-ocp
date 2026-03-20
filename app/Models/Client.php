<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom', 'prenom', 'telephone', 'adresse', 'email',
    ];

    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class);
    }
}
