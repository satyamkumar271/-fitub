<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('base_amount', 10, 2)->nullable()->after('amount');
            $table->decimal('gst_rate', 5, 2)->nullable()->after('base_amount');
            $table->decimal('gst_amount', 10, 2)->nullable()->after('gst_rate');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['base_amount', 'gst_rate', 'gst_amount']);
        });
    }
};
