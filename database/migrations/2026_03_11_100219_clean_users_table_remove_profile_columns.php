<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Customer fields
            $table->dropColumn([
                'age',
                'weight',
                'height',
                'goal',
                'customer_city',
                'customer_state',
            ]);

            // Trainer fields
            $table->dropColumn([
                'trainer_phone_number',
                'trainer_website_url',
                'specialization',
                'experience',
                'certifications',
                'trainer_city',
                'trainer_state',
            ]);

            // Gym fields
            $table->dropColumn([
                'gym_name',
                'gym_phone_number',
                'gym_email',
                'gym_website_url',
                'address_street',
                'address_city',
                'address_state',
                'address_pincode',
                'gym_age',
                'total_members',
            ]);

            // Shared social field
            $table->dropColumn([
                'social_links'
            ]);

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Customer fields
            $table->integer('age')->nullable();
            $table->double('weight')->nullable();
            $table->double('height')->nullable();
            $table->text('goal')->nullable();
            $table->string('customer_city')->nullable();
            $table->string('customer_state')->nullable();

            // Trainer fields
            $table->string('trainer_phone_number')->nullable();
            $table->string('trainer_website_url')->nullable();
            $table->string('specialization')->nullable();
            $table->integer('experience')->nullable();
            $table->longText('certifications')->nullable();
            $table->string('trainer_city')->nullable();
            $table->string('trainer_state')->nullable();

            // Gym fields
            $table->string('gym_name')->nullable();
            $table->string('gym_phone_number')->nullable();
            $table->string('gym_email')->nullable();
            $table->string('gym_website_url')->nullable();
            $table->string('address_street')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_pincode')->nullable();
            $table->string('gym_age')->nullable();
            $table->integer('total_members')->nullable();

            $table->longText('social_links')->nullable();
        });
    }
};