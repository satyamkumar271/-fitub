<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gyms', function (Blueprint $table) {
            if (Schema::hasColumn('gyms', 'trial_pass_label')) {
                $table->dropColumn('trial_pass_label');
            }
            if (Schema::hasColumn('gyms', 'allow_trial_pass')) {
                $table->dropColumn('allow_trial_pass');
            }
        });
    }

    public function down(): void
    {
        Schema::table('gyms', function (Blueprint $table) {
            if (!Schema::hasColumn('gyms', 'allow_trial_pass')) {
                $table->boolean('allow_trial_pass')->default(false)->after('allow_visit_booking');
            }
            if (!Schema::hasColumn('gyms', 'trial_pass_label')) {
                $table->string('trial_pass_label')->nullable()->after('allow_trial_pass');
            }
        });
    }
};

