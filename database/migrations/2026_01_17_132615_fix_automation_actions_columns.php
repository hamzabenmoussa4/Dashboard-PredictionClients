<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automation_actions', function (Blueprint $table) {

            // Si action_type n'existe pas, on l'ajoute
            if (!Schema::hasColumn('automation_actions', 'action_type')) {
                $table->enum('action_type', ['email', 'notify', 'discount'])
                    ->nullable()
                    ->after('customer_badge');
            }

            // Si message n'existe pas, on l'ajoute
            if (!Schema::hasColumn('automation_actions', 'message')) {
                $table->string('message')->nullable()->after('action_type');
            }

            // Si discount_percent n'existe pas, on l'ajoute
            if (!Schema::hasColumn('automation_actions', 'discount_percent')) {
                $table->decimal('discount_percent', 5, 2)->nullable()->after('message');
            }

            // Si status n'existe pas, on l'ajoute
            if (!Schema::hasColumn('automation_actions', 'status')) {
                $table->enum('status', ['done', 'failed'])->default('done')->after('discount_percent');
            }

            // Si executed_at n'existe pas, on l'ajoute
            if (!Schema::hasColumn('automation_actions', 'executed_at')) {
                $table->timestamp('executed_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('automation_actions', function (Blueprint $table) {

            if (Schema::hasColumn('automation_actions', 'executed_at')) {
                $table->dropColumn('executed_at');
            }

            if (Schema::hasColumn('automation_actions', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('automation_actions', 'discount_percent')) {
                $table->dropColumn('discount_percent');
            }

            if (Schema::hasColumn('automation_actions', 'message')) {
                $table->dropColumn('message');
            }

            if (Schema::hasColumn('automation_actions', 'action_type')) {
                $table->dropColumn('action_type');
            }
        });
    }
};
