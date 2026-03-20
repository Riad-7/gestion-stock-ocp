<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')
                ->constrained('produits')->restrictOnDelete();               
            $table->unsignedInteger('quantite')->default(0);
            $table->unsignedInteger('seuil_minimum')->default(5); // alerte stock bas
            $table->decimal('prix_unitaire', 10, 2);
            $table->date('date_fabrication')->nullable();
            $table->date('date_expiration')->nullable();
            $table->enum('statut', ['actif', 'expire', 'epuise'])->default('actif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
