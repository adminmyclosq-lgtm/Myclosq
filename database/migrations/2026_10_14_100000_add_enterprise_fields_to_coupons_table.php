<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'status')) {
                $table->string('status', 30)->default('active')->after('is_active');
            }
            if (!Schema::hasColumn('coupons', 'customer_eligibility')) {
                $table->string('customer_eligibility', 50)->default('all')->after('per_customer_limit');
            }
            if (!Schema::hasColumn('coupons', 'customer_ids')) {
                $table->json('customer_ids')->nullable()->after('customer_eligibility');
            }
            if (!Schema::hasColumn('coupons', 'eligible_categories')) {
                $table->json('eligible_categories')->nullable()->after('customer_ids');
            }
            if (!Schema::hasColumn('coupons', 'free_shipping')) {
                $table->boolean('free_shipping')->default(false)->after('eligible_categories');
            }
            if (!Schema::hasColumn('coupons', 'allow_stacking')) {
                $table->boolean('allow_stacking')->default(false)->after('free_shipping');
            }
            if (!Schema::hasColumn('coupons', 'campaign_name')) {
                $table->string('campaign_name', 120)->nullable()->after('allow_stacking');
            }
            if (!Schema::hasColumn('coupons', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('campaign_name');
            }
            if (!Schema::hasColumn('coupons', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('internal_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'customer_eligibility',
                'customer_ids',
                'eligible_categories',
                'free_shipping',
                'allow_stacking',
                'campaign_name',
                'internal_notes',
                'created_by',
            ]);
        });
    }
};
