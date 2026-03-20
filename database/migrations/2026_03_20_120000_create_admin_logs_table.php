<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('admin_logs')) {
            Schema::create('admin_logs', function (Blueprint $table) {
                $table->id();

                $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
                $table->string('action', 60);

                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('block_id')->nullable();
                $table->text('reason')->nullable();

                $table->timestamps();

                $table->index(['action', 'created_at']);
                $table->index(['user_id', 'created_at']);
                $table->index(['block_id', 'created_at']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('admin_logs');
    }
};

