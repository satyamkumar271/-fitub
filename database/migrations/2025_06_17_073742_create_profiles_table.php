<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 'profiles' naam ka naya table banayein
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();

            // Yeh user_id ko users table ke id se jorega.
            // Agar user delete hota hai, to uska profile bhi delete ho jayega.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('city')->nullable(); // Trainer/Gym ka sheher
            $table->string('state')->nullable(); // Trainer/Gym ka state
            $table->string('specialization')->nullable(); // Sirf Trainers ke liye (e.g., Yoga, Zumba)

            // Aap yahan aur bhi columns add kar sakte hain jaise address, phone_number, etc.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
