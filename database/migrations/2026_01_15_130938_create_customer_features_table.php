<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Création de la table "customer_features"
        Schema::create('customer_features', function (Blueprint $table) {

            // Clé primaire
            $table->id();

            // Clé étrangère vers le client
            $table->unsignedBigInteger('customer_id');

            // Date de la dernière commande du client
            $table->dateTime('last_order_at')->nullable();

            // Nombre de jours depuis la dernière commande
            $table->unsignedInteger('recency_days')->nullable();

            // Nombre de commandes sur les 90 derniers jours
            $table->unsignedInteger('frequency_90d')->default(0);

            // Montant total dépensé sur les 90 derniers jours
            $table->decimal('monetary_90d', 10, 2)->default(0);

            // Panier moyen sur les 90 derniers jours
            $table->decimal('avg_order_value_90d', 10, 2)->default(0);

            // Nombre total de commandes du client
            $table->unsignedInteger('orders_count_total')->default(0);

            // Montant total dépensé depuis le début
            $table->decimal('total_spent', 10, 2)->default(0);

            // Colonnes created_at et updated_at
            $table->timestamps();

            // Un client ne doit avoir qu'une seule ligne de features
            $table->unique('customer_id');

            // Index utile pour trouver rapidement les clients à risque
            $table->index('recency_days');

            // Contrainte FK: customer_id référence customers.id
            // onDelete('cascade'): si on supprime un client, ses features sont supprimées
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Suppression de la table "customer_features"
        Schema::dropIfExists('customer_features');
    }
};
