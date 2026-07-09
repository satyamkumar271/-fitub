<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'registration_cancelled_at')) {
                $table->timestamp('registration_cancelled_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('users', 'registration_cancellation_reason')) {
                $table->string('registration_cancellation_reason', 1000)->nullable()->after('registration_cancelled_at');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'registration_cancelled_at')) {
                $table->dropColumn('registration_cancelled_at');
            }
            if (Schema::hasColumn('users', 'registration_cancellation_reason')) {
                $table->dropColumn('registration_cancellation_reason');
            }
        });
    }
};

