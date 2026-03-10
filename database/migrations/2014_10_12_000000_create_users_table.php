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
        Schema::create('users', function (Blueprint $table) {
            // Basic User Info
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('user_type'); // e.g., 'customer', 'trainer', 'gym_owner'

            // Customer Fields
            $table->integer('age')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('customer_city')->nullable();      // <<< NAYA FIELD
            $table->string('customer_state')->nullable();     // <<< NAYA FIELD
            $table->float('weight')->nullable();
            $table->float('height')->nullable();
            $table->text('goal')->nullable();

            // Trainer Fields
            $table->string('trainer_phone_number')->nullable();
            $table->string('trainer_city')->nullable();       // <<< NAYA FIELD
            $table->string('trainer_state')->nullable();      // <<< NAYA FIELD
            $table->string('trainer_website_url')->nullable();
            $table->string('specialization')->nullable();
            $table->integer('experience')->nullable();
            $table->json('certifications')->nullable();

            // Gym Owner Fields
            $table->string('gym_name')->nullable();           // <<< NAYA FIELD
            $table->string('gym_phone_number')->nullable();
            $table->string('gym_email')->nullable();
            $table->string('gym_website_url')->nullable();
            $table->string('address_street')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_pincode')->nullable();
            $table->string('gym_age')->nullable();
            $table->integer('total_members')->nullable();

            // Shared Field
            $table->json('social_links')->nullable();

            // Laravel Defaults
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
