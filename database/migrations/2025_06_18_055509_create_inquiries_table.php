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
    Schema::create('inquiries', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Lead kisne bheja (agar logged in hai)
        $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade'); // Lead kiske liye hai (trainer/gym)

        // Agar user logged in nahi hai to uski info
        $table->string('guest_name')->nullable();
        $table->string('guest_email')->nullable();
        $table->string('guest_phone')->nullable();

        // Lead ki details
        $table->string('service_needed'); // e.g., 'Personal Training', 'Diet Plan'
        $table->text('message');
        $table->enum('status', ['pending', 'forwarded', 'viewed', 'closed'])->default('pending');

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
        Schema::dropIfExists('inquiries');
    }
};
