<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->string('issue_type', 50)->nullable()->after('subject');
            $table->string('priority', 20)->default('normal')->after('issue_type');
            $table->string('attachment_path')->nullable()->after('message');
            $table->string('related_page', 255)->nullable()->after('attachment_path');
            $table->string('contact_phone', 30)->nullable()->after('related_page');
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn([
                'issue_type',
                'priority',
                'attachment_path',
                'related_page',
                'contact_phone',
            ]);
        });
    }
};

