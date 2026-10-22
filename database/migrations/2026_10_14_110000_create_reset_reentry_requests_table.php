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
        if (! Schema::hasTable('reset_reentry_requests')) {
            Schema::create('reset_reentry_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
                $table->foreignId('previous_reset_profile_id')->constrained('reset_profiles')->onUpdate('cascade')->onDelete('cascade');
                $table->string('status', 30)->default('pending')->index();
                $table->string('request_reason', 1000)->nullable();
                $table->dateTime('requested_at')->useCurrent();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->onUpdate('cascade')->nullOnDelete();
                $table->dateTime('reviewed_at')->nullable();
                $table->string('review_notes', 1000)->nullable();
                $table->foreignId('new_reset_profile_id')->nullable()->constrained('reset_profiles')->onUpdate('cascade')->nullOnDelete();
                $table->timestamps();

                $table->index(['user_id', 'status']);
                $table->index('previous_reset_profile_id');
                $table->index('new_reset_profile_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reset_reentry_requests');
    }
};
