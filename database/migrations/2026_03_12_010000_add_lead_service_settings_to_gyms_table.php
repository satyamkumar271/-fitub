<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gyms', function (Blueprint $table) {
            $table->boolean('allow_visit_booking')->default(false)->after('total_members');
            $table->boolean('allow_trial_pass')->default(false)->after('allow_visit_booking');
            $table->string('trial_pass_label')->nullable()->after('allow_trial_pass');
            $table->string('lead_services_note')->nullable()->after('trial_pass_label');
        });
    }

    public function down(): void
    {
        Schema::table('gyms', function (Blueprint $table) {
            $table->dropColumn([
                'allow_visit_booking',
                'allow_trial_pass',
                'trial_pass_label',
                'lead_services_note',
            ]);
        });
    }
};

