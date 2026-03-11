<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'context_type')) {
                $table->string('context_type')->nullable()->after('status'); // e.g. subscription, lead_unlock
            }
            if (!Schema::hasColumn('payments', 'context_id')) {
                $table->unsignedBigInteger('context_id')->nullable()->after('context_type'); // e.g. inquiry_id
            }
            if (!Schema::hasColumn('payments', 'meta')) {
                $table->json('meta')->nullable()->after('context_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'meta')) {
                $table->dropColumn('meta');
            }
            if (Schema::hasColumn('payments', 'context_id')) {
                $table->dropColumn('context_id');
            }
            if (Schema::hasColumn('payments', 'context_type')) {
                $table->dropColumn('context_type');
            }
        });
    }
};


