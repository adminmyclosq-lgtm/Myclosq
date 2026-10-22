<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('customer_profiles')) {
            Schema::table('customer_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('customer_profiles', 'tags')) {
                    $table->text('tags')->nullable()->after('source_channel');
                }
                if (!Schema::hasColumn('customer_profiles', 'is_vip')) {
                    $table->boolean('is_vip')->default(false)->after('tags');
                }
            });
        }

        if (!Schema::hasTable('customer_notes')) {
            Schema::create('customer_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('note');
                $table->string('category', 50)->default('general'); // general, call, email, support, urgent
                $table->boolean('is_pinned')->default(false);
                $table->timestamps();

                $table->index(['customer_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_notes');

        if (Schema::hasTable('customer_profiles')) {
            Schema::table('customer_profiles', function (Blueprint $table) {
                if (Schema::hasColumn('customer_profiles', 'tags')) {
                    $table->dropColumn('tags');
                }
                if (Schema::hasColumn('customer_profiles', 'is_vip')) {
                    $table->dropColumn('is_vip');
                }
            });
        }
    }
};
