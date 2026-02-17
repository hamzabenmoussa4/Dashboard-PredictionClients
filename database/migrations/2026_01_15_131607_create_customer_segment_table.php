<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Création de la table pivot "customer_segment"
        Schema::create('customer_segment', function (Blueprint $table) {

            // Clé étrangère vers le client
            $table->unsignedBigInteger('customer_id');

            // Clé étrangère vers le segment
            $table->unsignedBigInteger('segment_id');

            // Date d'ajout du client au segment (utile pour historique)
            $table->timestamps();

            // Empêche les doublons : un client ne peut pas être deux fois dans le même segment
            $table->unique(['customer_id', 'segment_id']);

            // Index pour lister rapidement tous les clients d'un segment
            $table->index('segment_id');

            // Index pour lister rapidement tous les segments d'un client
            $table->index('customer_id');

            // Contrainte FK vers customers
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');

            // Contrainte FK vers segments
            $table->foreign('segment_id')
                ->references('id')
                ->on('segments')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Suppression de la table pivot
        Schema::dropIfExists('customer_segment');
    }
};
