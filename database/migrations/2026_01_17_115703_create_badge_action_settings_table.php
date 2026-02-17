<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badge_action_settings', function (Blueprint $table) {
            $table->id();

            $table->enum('badge', ['NORMAL', 'VIP', 'RISK'])->unique();

            $table->enum('action_type', ['none', 'email', 'discount', 'notify'])
                ->default('none');

            $table->string('message')->nullable();
            $table->decimal('discount_percent', 5, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_action_settings');
    }
};
