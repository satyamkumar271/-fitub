<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiry_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blocker_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('blocked_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('reason', 100)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['inquiry_id', 'blocker_id', 'blocked_user_id'], 'inquiry_blocks_unique_pair');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiry_blocks');
    }
};

