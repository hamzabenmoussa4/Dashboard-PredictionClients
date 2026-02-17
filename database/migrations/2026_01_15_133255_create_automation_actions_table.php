<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_actions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('customer_id');

            $table->enum('customer_badge', ['NORMAL', 'VIP', 'RISK']);

            $table->enum('action_type', ['email', 'notify', 'discount']);

            $table->string('message')->nullable();

            $table->decimal('discount_percent', 5, 2)->nullable();

            $table->enum('status', ['done', 'failed'])->default('done');

            $table->timestamp('executed_at')->nullable();

            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_actions');
    }
};
