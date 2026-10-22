<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $guarded = [];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'billing_address_json' => 'array',
        'shipping_address_json' => 'array',
        'placed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function items(): HasMany
    {
        return $this->orderItems();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatuHistory::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(OrderReturn::class);
    }

    /* Address Relationships */

    public function orderAddresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function shippingAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)->where('type', 'shipping');
    }

    public function billingAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)->where('type', 'billing');
    }

    /**
     * Retrieve the effective shipping address model, falling back to a virtual instance from shipping_address_json.
     */
    public function getEffectiveShippingAddress(): ?OrderAddress
    {
        if ($this->relationLoaded('shippingAddress') && $this->shippingAddress) {
            return $this->shippingAddress;
        }

        $address = $this->shippingAddress()->first();
        if ($address) {
            return $address;
        }

        // Virtual fallback
        $json = $this->shipping_address_json;
        if (is_array($json) && !empty($json)) {
            return new OrderAddress([
                'order_id' => $this->id,
                'type' => 'shipping',
                'full_name' => $json['recipient_name'] ?? ($json['full_name'] ?? 'Customer'),
                'phone' => $json['phone'] ?? null,
                'email' => $json['email'] ?? null,
                'address_line1' => $json['address_line1'] ?? '',
                'address_line2' => $json['address_line2'] ?? null,
                'landmark' => $json['landmark'] ?? null,
                'area' => $json['area'] ?? null,
                'city' => $json['city'] ?? '',
                'district' => $json['district'] ?? null,
                'state' => $json['state'] ?? '',
                'postal_code' => $json['postal_code'] ?? '',
                'country' => $json['country'] ?? 'India',
                'delivery_instructions' => $json['delivery_instructions'] ?? null,
                'address_type' => $json['address_type'] ?? 'home',
                'is_verified' => !empty($json['is_verified']),
            ]);
        }

        return null;
    }
}
