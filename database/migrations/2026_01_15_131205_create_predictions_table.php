<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Création de la table "predictions"
        Schema::create('predictions', function (Blueprint $table) {

            // Clé primaire
            $table->id();

            // Clé étrangère vers le client concerné par la prédiction
            $table->unsignedBigInteger('customer_id');

            // Type de prédiction : churn, sales ou engagement
            $table->string('type');

            // Score de prédiction (entre 0 et 1)
            $table->decimal('score', 5, 4)->default(0);

            // Label lisible (ex: low, medium, high, at_risk)
            $table->string('label')->nullable();

            // Version du modèle utilisé (utile plus tard avec FastAPI)
            $table->string('model_version')->nullable();

            // Date à laquelle la prédiction a été calculée
            $table->dateTime('predicted_at');

            // Colonnes created_at et updated_at
            $table->timestamps();

            // Index pour les requêtes fréquentes par client
            $table->index('customer_id');

            // Index pour filtrer par type de prédiction
            $table->index('type');

            // Index pour trier ou filtrer par score
            $table->index('score');

            // Index composite pour récupérer la dernière prédiction d’un type donné
            $table->index(['customer_id', 'type', 'predicted_at']);

            // Contrainte FK: customer_id référence customers.id
            // onDelete('cascade'): si on supprime un client, ses prédictions sont supprimées
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Suppression de la table "predictions"
        Schema::dropIfExists('predictions');
    }
};
