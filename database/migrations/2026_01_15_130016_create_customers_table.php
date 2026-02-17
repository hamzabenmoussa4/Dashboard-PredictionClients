<?php

// Import de la classe de base pour les migrations
use Illuminate\Database\Migrations\Migration;

// Import de la classe qui permet de définir les colonnes
use Illuminate\Database\Schema\Blueprint;

// Import de la façade Schema pour créer/supprimer des tables
use Illuminate\Support\Facades\Schema;

// Classe anonyme de migration
return new class extends Migration
{
    // Méthode appelée lors de "php artisan migrate"
    public function up(): void
    {
        // Création de la table "customers"
        Schema::create('customers', function (Blueprint $table) {

            // Colonne "id" : clé primaire auto-incrémentée
            $table->id();

            // Prénom du client (facultatif)
            $table->string('first_name')->nullable();

            // Nom du client (facultatif)
            $table->string('last_name')->nullable();

            // Email du client (obligatoire)
            $table->string('email');

            // Numéro de téléphone (facultatif)
            $table->string('phone')->nullable();

            // Statut du client (ex: active, inactive)
            $table->string('status')->nullable();

            // Colonnes created_at et updated_at gérées par Laravel
            $table->timestamps();

            // Contrainte : l'email doit être unique
            $table->unique('email');
        });
    }

    // Méthode appelée lors de "php artisan migrate:rollback"
    public function down(): void
    {
        // Suppression de la table "customers" si elle existe
        Schema::dropIfExists('customers');
    }
};
