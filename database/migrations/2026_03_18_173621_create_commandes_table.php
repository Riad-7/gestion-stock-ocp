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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')
                ->constrained('articles')->restrictOnDelete();
            $table->foreignId('fournisseur_id')
                ->constrained('fournisseurs')->restrictOnDelete();
            $table->unsignedInteger('quantite');
            $table->decimal('prix_unitaire', 10, 2); // Unit cost from supplier
            $table->decimal('prix_total', 10, 2);    // Total cost of the order
            $table->date('date_commande');
            $table->date('date_livraison')->nullable(); // Track when it actually arrived
            $table->string('reference_commande')->unique()->nullable(); // Supplier invoice / PO number
            $table->enum('statut', ['en_attente', 'livree', 'annulee'])->default('en_attente');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
