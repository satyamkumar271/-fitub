<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            if (!Schema::hasColumn('blogs', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('excerpt');
            }
            if (!Schema::hasColumn('blogs', 'meta_description')) {
                $table->string('meta_description', 320)->nullable()->after('meta_title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            if (Schema::hasColumn('blogs', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
            if (Schema::hasColumn('blogs', 'meta_title')) {
                $table->dropColumn('meta_title');
            }
        });
    }
};
