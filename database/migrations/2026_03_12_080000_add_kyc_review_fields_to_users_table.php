<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'kyc_status')) {
                $table->string('kyc_status', 30)->default('not_required')->after('is_verified');
            }
            if (!Schema::hasColumn('users', 'kyc_rejection_reason')) {
                $table->text('kyc_rejection_reason')->nullable()->after('kyc_status');
            }
            if (!Schema::hasColumn('users', 'kyc_reviewed_by')) {
                $table->unsignedBigInteger('kyc_reviewed_by')->nullable()->after('kyc_rejection_reason');
            }
            if (!Schema::hasColumn('users', 'kyc_reviewed_at')) {
                $table->timestamp('kyc_reviewed_at')->nullable()->after('kyc_reviewed_by');
            }
        });

        DB::table('users')
            ->whereIn('user_type', ['trainer', 'gymowner'])
            ->where('status', 'pending')
            ->update(['kyc_status' => 'pending']);

        DB::table('users')
            ->whereIn('user_type', ['trainer', 'gymowner'])
            ->where('is_verified', true)
            ->update(['kyc_status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'kyc_reviewed_at')) {
                $table->dropColumn('kyc_reviewed_at');
            }
            if (Schema::hasColumn('users', 'kyc_reviewed_by')) {
                $table->dropColumn('kyc_reviewed_by');
            }
            if (Schema::hasColumn('users', 'kyc_rejection_reason')) {
                $table->dropColumn('kyc_rejection_reason');
            }
            if (Schema::hasColumn('users', 'kyc_status')) {
                $table->dropColumn('kyc_status');
            }
        });
    }
};
