<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';
    protected $guarded = [];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_cart_value' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'free_shipping' => 'boolean',
        'allow_stacking' => 'boolean',
        'customer_ids' => 'array',
        'eligible_categories' => 'array',
        'usage_limit' => 'integer',
        'per_customer_limit' => 'integer',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'coupon_products', 'coupon_id', 'product_id');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class)->latest('id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest('created_at');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Compute dynamic operational status:
     * active, scheduled, paused, expired, draft
     */
    public function getCalculatedStatusAttribute(): string
    {
        if ($this->status === 'draft') {
            return 'draft';
        }

        if ($this->status === 'paused' || !$this->is_active) {
            return 'paused';
        }

        if ($this->expires_at && Carbon::now()->gt($this->expires_at)) {
            return 'expired';
        }

        if ($this->usage_limit && $this->usages()->count() >= $this->usage_limit) {
            return 'expired';
        }

        if ($this->starts_at && Carbon::now()->lt($this->starts_at)) {
            return 'scheduled';
        }

        return 'active';
    }

    /**
     * Human readable discount description
     */
    public function getFormattedDiscountAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return rtrim(rtrim(number_format($this->discount_value, 2), '0'), '.') . '% OFF';
        }

        if ($this->discount_type === 'free_shipping') {
            return 'Free Shipping';
        }

        if ($this->discount_type === 'bogo') {
            return 'Buy 1 Get 1 Free';
        }

        if ($this->discount_type === 'bundle') {
            return 'Bundle Savings (₹' . number_format($this->discount_value, 2) . ')';
        }

        return '₹' . number_format($this->discount_value, 2) . ' OFF';
    }

    /**
     * Total redemptions count
     */
    public function getTotalRedemptionsAttribute(): int
    {
        return $this->usages()->count();
    }

    /**
     * Total revenue generated from orders using this coupon
     */
    public function getTotalRevenueAttribute(): float
    {
        return (float) $this->orders()
            ->where('payment_status', 'paid')
            ->sum('grand_total');
    }

    /**
     * Total discount provided across all redemptions
     */
    public function getTotalDiscountGivenAttribute(): float
    {
        return (float) $this->usages()->sum('discount_amount');
    }

    /**
     * Average discount per redemption
     */
    public function getAverageDiscountAttribute(): float
    {
        $count = $this->total_redemptions;
        return $count > 0 ? round($this->total_discount_given / $count, 2) : 0.0;
    }
}
