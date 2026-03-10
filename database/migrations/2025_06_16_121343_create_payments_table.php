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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Kis user ne pay kiya
        $table->string('razorpay_order_id');
        $table->string('razorpay_payment_id')->nullable();
        $table->string('razorpay_signature')->nullable();
        $table->decimal('amount', 8, 2); // e.g., 1499.00
        $table->string('currency', 3)->default('INR');
        $table->string('plan_name'); // e.g., 'monthly', 'weekly'
        $table->string('status')->default('created'); // e.g., created, paid, failed
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
