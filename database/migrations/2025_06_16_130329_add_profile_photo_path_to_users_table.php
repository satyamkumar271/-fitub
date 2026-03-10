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
        // 'user_type' column ke baad naya column add karein
        $table->string('profile_photo_path', 2048)->nullable()->after('user_type');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
   public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('profile_photo_path');
    });
}
};
