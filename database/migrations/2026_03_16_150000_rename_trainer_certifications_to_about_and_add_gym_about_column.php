<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            // Add a new about column for trainers. We are not renaming the old
            // certifications column to avoid SQL compatibility issues.
            // Old certifications data (if any) can be migrated manually if needed.
            if (!Schema::hasColumn('trainers', 'about')) {
                $table->longText('about')->nullable()->after('experience');
            }
        });

        Schema::table('gyms', function (Blueprint $table) {
            if (!Schema::hasColumn('gyms', 'about')) {
                $table->longText('about')->nullable()->after('total_members');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            if (Schema::hasColumn('trainers', 'about')) {
                $table->dropColumn('about');
            }
        });

        Schema::table('gyms', function (Blueprint $table) {
            if (Schema::hasColumn('gyms', 'about')) {
                $table->dropColumn('about');
            }
        });
    }
};

