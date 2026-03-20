<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'featured_source')) {
                $table->string('featured_source', 20)->nullable()->after('featured_until'); // subscription|promo
            }

            if (!Schema::hasColumn('users', 'promo_featured_days_used')) {
                $table->unsignedSmallInteger('promo_featured_days_used')->default(0)->after('featured_source');
            }

            if (!Schema::hasColumn('users', 'promo_featured_grants')) {
                $table->unsignedSmallInteger('promo_featured_grants')->default(0)->after('promo_featured_days_used');
            }

            if (!Schema::hasColumn('users', 'promo_featured_last_ended_at')) {
                $table->timestamp('promo_featured_last_ended_at')->nullable()->after('promo_featured_grants');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'promo_featured_last_ended_at')) {
                $table->dropColumn('promo_featured_last_ended_at');
            }
            if (Schema::hasColumn('users', 'promo_featured_grants')) {
                $table->dropColumn('promo_featured_grants');
            }
            if (Schema::hasColumn('users', 'promo_featured_days_used')) {
                $table->dropColumn('promo_featured_days_used');
            }
            if (Schema::hasColumn('users', 'featured_source')) {
                $table->dropColumn('featured_source');
            }
        });
    }
};

