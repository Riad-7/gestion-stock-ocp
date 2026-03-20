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
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')
                ->constrained('articles')->restrictOnDelete();
            $table->foreignId('client_id')
                ->constrained('clients')->restrictOnDelete();
            $table->unsignedInteger('quantite');
            $table->decimal('prix_total', 10, 2);
            $table->decimal('prix_unitaire', 10, 2);
            $table->date('date_vente');
            $table->enum('mode_paiement', ['especes', 'carte', 'cheque', 'virement'])->default('especes');
            $table->string('reference_facture')->unique()->nullable();
            $table->enum('statut', ['payee', 'en_attente', 'annulee'])->default('payee');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
