<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // On modifie la table users existante
        Schema::table('users', function (Blueprint $table) {

            // On ajoute un champ is_admin (0 = user normal, 1 = admin)
            // default(false) => par défaut un utilisateur n’est pas admin
            $table->boolean('is_admin')->default(false)->after('password');

            // Index pour accélérer les requêtes si besoin (ex: filtre admin)
            $table->index('is_admin');
        });
    }

    public function down(): void
    {
        // On revient en arrière : on supprime l'index et la colonne
        Schema::table('users', function (Blueprint $table) {

            // Suppression de l'index (Laravel génère le nom automatiquement)
            // Si erreur sur le nom, on ajustera avec le nom exact
            $table->dropIndex(['is_admin']);

            // Suppression de la colonne
            $table->dropColumn('is_admin');
        });
    }
};
