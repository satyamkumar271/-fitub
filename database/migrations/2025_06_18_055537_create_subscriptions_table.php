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
   // ...
public function up()
{
    Schema::create('subscriptions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Kis trainer/gym ne kharida hai
        $table->string('plan_type'); // 'single_lead', 'monthly', 'yearly'
        $table->integer('leads_remaining')->nullable(); // Monthly plan ke liye
        $table->timestamp('expires_at')->nullable(); // Plan kab expire hoga
        $table->timestamps();
    });
}
// ...

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
};
