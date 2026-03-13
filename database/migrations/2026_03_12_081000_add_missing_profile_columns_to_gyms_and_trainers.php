<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            if (!Schema::hasColumn('trainers', 'social_links')) {
                $table->json('social_links')->nullable()->after('certifications');
            }
        });

        Schema::table('gyms', function (Blueprint $table) {
            if (!Schema::hasColumn('gyms', 'address_street')) {
                $table->string('address_street')->nullable()->after('gym_website_url');
            }
            if (!Schema::hasColumn('gyms', 'gym_age')) {
                $table->integer('gym_age')->nullable()->after('address_pincode');
            }
            if (!Schema::hasColumn('gyms', 'social_links')) {
                $table->json('social_links')->nullable()->after('total_members');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            if (Schema::hasColumn('trainers', 'social_links')) {
                $table->dropColumn('social_links');
            }
        });

        Schema::table('gyms', function (Blueprint $table) {
            if (Schema::hasColumn('gyms', 'social_links')) {
                $table->dropColumn('social_links');
            }
            if (Schema::hasColumn('gyms', 'gym_age')) {
                $table->dropColumn('gym_age');
            }
            if (Schema::hasColumn('gyms', 'address_street')) {
                $table->dropColumn('address_street');
            }
        });
    }
};
