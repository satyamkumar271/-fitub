<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'customer_city')) {
                $table->string('customer_city')->nullable()->after('phone_number');
            }

            if (!Schema::hasColumn('users', 'customer_state')) {
                $table->string('customer_state')->nullable()->after('customer_city');
            }

            if (!Schema::hasColumn('users', 'trainer_city')) {
                $table->string('trainer_city')->nullable()->after('trainer_phone_number');
            }

            if (!Schema::hasColumn('users', 'trainer_state')) {
                $table->string('trainer_state')->nullable()->after('trainer_city');
            }

            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('user_type');
            }

            if (!Schema::hasColumn('users', 'gym_name')) {
                $table->string('gym_name')->nullable()->after('user_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'customer_city')) {
                $table->dropColumn('customer_city');
            }

            if (Schema::hasColumn('users', 'customer_state')) {
                $table->dropColumn('customer_state');
            }

            if (Schema::hasColumn('users', 'trainer_city')) {
                $table->dropColumn('trainer_city');
            }

            if (Schema::hasColumn('users', 'trainer_state')) {
                $table->dropColumn('trainer_state');
            }

            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('users', 'gym_name')) {
                $table->dropColumn('gym_name');
            }
        });
    }
};
