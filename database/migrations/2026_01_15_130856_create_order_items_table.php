<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Création de la table "order_items"
        Schema::create('order_items', function (Blueprint $table) {

            // Clé primaire
            $table->id();

            // Clé étrangère vers la commande
            $table->unsignedBigInteger('order_id');

            // Nom du produit (simple, sans gérer un catalogue complet)
            $table->string('product_name');

            // Quantité achetée
            $table->unsignedInteger('quantity')->default(1);

            // Prix unitaire du produit
            $table->decimal('unit_price', 10, 2)->default(0);

            // Total de la ligne (quantity * unit_price)
            $table->decimal('line_total', 10, 2)->default(0);

            // Colonnes created_at et updated_at
            $table->timestamps();

            // Index pour accélérer les requêtes par commande
            $table->index('order_id');

            // Contrainte FK: order_id référence orders.id
            // onDelete('cascade'): si on supprime une commande, ses items sont supprimés
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Suppression de la table "order_items"
        Schema::dropIfExists('order_items');
    }
};
