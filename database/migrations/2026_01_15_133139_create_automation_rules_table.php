<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Création de la table "automation_rules"
        Schema::create('automation_rules', function (Blueprint $table) {

            // Clé primaire
            $table->id();

            // Nom lisible de la règle
            $table->string('name');

            // Permet d'activer ou désactiver une règle
            $table->boolean('is_active')->default(true);

            // Type de déclencheur : prediction, feature, segment
            $table->string('trigger_type');

            // Type de prédiction concernée (ex: churn)
            $table->string('prediction_type')->nullable();

            // Opérateur de comparaison (>, <, >=, ==)
            $table->string('operator')->nullable();

            // Valeur seuil à comparer
            $table->decimal('threshold', 10, 4)->nullable();

            // Seuil de récence (en jours) si la règle est basée sur la récence
            $table->unsignedInteger('recency_days_threshold')->nullable();

            // Type d'action à déclencher (add_segment, create_email_action, etc.)
            $table->string('action_type');

            // Données de l'action (JSON)
            $table->json('action_payload')->nullable();

            // Colonnes created_at et updated_at
            $table->timestamps();

            // Index pour filtrer rapidement les règles actives
            $table->index('is_active');

            // Index pour filtrer par type de déclencheur
            $table->index('trigger_type');

            // Index pour filtrer par type de prédiction
            $table->index('prediction_type');
        });
    }

    public function down(): void
    {
        // Suppression de la table "automation_rules"
        Schema::dropIfExists('automation_rules');
    }
};
