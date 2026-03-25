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
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // Recommanded
            $table->string('nom_produit');
            $table->text('description')->nullable();
            $table->string('marque')->nullable();
            $table->string('categorie')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true); // Recommanded
            $table->timestamps();
            $table->softDeletes(); // Highly recommanded for data integrity
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
