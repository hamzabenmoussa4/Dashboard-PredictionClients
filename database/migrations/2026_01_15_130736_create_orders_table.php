<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Création de la table "orders"
        Schema::create('orders', function (Blueprint $table) {

            // Clé primaire
            $table->id();

            // Clé étrangère vers le client qui a passé la commande
            $table->unsignedBigInteger('customer_id');

            // Numéro de commande (utile pour affichage, suivi, etc.)
            $table->string('order_number')->nullable();

            // Statut de la commande (ex: pending, paid, cancelled, refunded)
            $table->string('status')->default('pending');

            // Montant total de la commande
            $table->decimal('total_amount', 10, 2)->default(0);

            // Devise (ex: EUR, MAD) - facultatif
            $table->string('currency', 3)->nullable();

            // Date réelle de la commande (très important pour récence / features)
            $table->dateTime('ordered_at')->nullable();

            // Colonnes created_at et updated_at
            $table->timestamps();

            // Index pour accélérer les recherches par client
            $table->index('customer_id');

            // Index pour accélérer les recherches par date
            $table->index('ordered_at');

            // Index pour accélérer les filtres par statut
            $table->index('status');

            // Contrainte d'unicité sur order_number si on l'utilise
            // Attention: comme order_number est nullable, MySQL autorise plusieurs NULL
            $table->unique('order_number');

            // Contrainte FK: customer_id référence customers.id
            // onDelete('cascade'): si on supprime un client, ses commandes sont supprimées
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Suppression de la table "orders"
        Schema::dropIfExists('orders');
    }
};
