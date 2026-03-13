<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('status');
            }
        });

        Schema::table('gyms', function (Blueprint $table) {
            if (!Schema::hasColumn('gyms', 'business_doc_path')) {
                $table->string('business_doc_path')->nullable()->after('gym_website_url');
            }
        });

        Schema::table('trainers', function (Blueprint $table) {
            if (!Schema::hasColumn('trainers', 'certificate_proof_paths')) {
                $table->json('certificate_proof_paths')->nullable()->after('certifications');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            if (Schema::hasColumn('trainers', 'certificate_proof_paths')) {
                $table->dropColumn('certificate_proof_paths');
            }
        });

        Schema::table('gyms', function (Blueprint $table) {
            if (Schema::hasColumn('gyms', 'business_doc_path')) {
                $table->dropColumn('business_doc_path');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_verified')) {
                $table->dropColumn('is_verified');
            }
        });
    }
};
