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
    // File: database/migrations/xxxx_xx_xx_xxxxxx_add_featured_status_to_users_table.php

public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // 'is_featured' column, by default 'false' (0)
        $table->boolean('is_featured')->default(false)->after('user_type');

        // 'featured_until' column, kab tak feature active hai
        $table->timestamp('featured_until')->nullable()->after('is_featured');
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
        $table->dropColumn('is_featured');
        $table->dropColumn('featured_until');
    });
}
};
