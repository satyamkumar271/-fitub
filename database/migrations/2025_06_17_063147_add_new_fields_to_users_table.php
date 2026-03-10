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
        $table->string('customer_city')->nullable()->after('phone_number');
        $table->string('customer_state')->nullable()->after('customer_city');
        $table->string('trainer_city')->nullable()->after('trainer_phone_number');
        $table->string('trainer_state')->nullable()->after('trainer_city');
        $table->string('gym_name')->nullable()->after('user_type');
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
            //
        });
    }
};
