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
   public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Yeh command 'user_type' column ko update karke 'admin' option add kar degi
        DB::statement("ALTER TABLE users CHANGE user_type user_type ENUM('customer', 'gymowner', 'trainer', 'admin') NOT NULL");
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
