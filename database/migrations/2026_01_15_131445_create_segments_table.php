<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Création de la table "segments"
        Schema::create('segments', function (Blueprint $table) {

            // Clé primaire
            $table->id();

            // Nom du segment (ex: VIP, At Risk, Winback)
            $table->string('name');

            // Description du segment (facultatif)
            $table->text('description')->nullable();

            // Colonnes created_at et updated_at
            $table->timestamps();

            // Un segment doit avoir un nom unique
            $table->unique('name');
        });
    }

    public function down(): void
    {
        // Suppression de la table "segments"
        Schema::dropIfExists('segments');
    }
};
