<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('badge_computed', ['NORMAL', 'VIP', 'RISK'])
                ->default('NORMAL')
                ->after('status');

            $table->enum('badge_override', ['NORMAL', 'VIP', 'RISK'])
                ->nullable()
                ->after('badge_computed');

            $table->timestamp('badge_updated_at')
                ->nullable()
                ->after('badge_override');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('badge_updated_at');
            $table->dropColumn('badge_override');
            $table->dropColumn('badge_computed');
        });
    }
};
