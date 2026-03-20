<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('featured_professional_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('featured_professional_logs', 'source')) {
                $table->string('source', 20)->nullable()->after('action'); // subscription|promo
                $table->index(['source', 'created_at']);
            }
        });
    }

    public function down()
    {
        Schema::table('featured_professional_logs', function (Blueprint $table) {
            if (Schema::hasColumn('featured_professional_logs', 'source')) {
                $table->dropIndex(['source', 'created_at']);
                $table->dropColumn('source');
            }
        });
    }
};

