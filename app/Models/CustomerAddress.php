<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $table = 'customer_addresses';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_verified' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderAddresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class, 'customer_address_id');
    }

    /* Scopes */

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeShipping($query)
    {
        return $query->whereIn('address_type', ['shipping', 'home', 'office', 'other']);
    }

    public function scopeBilling($query)
    {
        return $query->where('address_type', 'billing');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('address_type', $type);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /* Accessors & Mutators */

    public function getFullNameAttribute(): string
    {
        return $this->recipient_name;
    }

    public function setFullNameAttribute($value): void
    {
        $this->attributes['recipient_name'] = $value;
    }

    public function getFormattedAddressAttribute(): string
    {
        $lines = array_filter([
            $this->recipient_name,
            $this->phone ? 'Phone: ' . $this->phone : null,
            $this->address_line1,
            $this->address_line2,
            $this->landmark ? 'Landmark: ' . $this->landmark : null,
            $this->area,
            trim(($this->city ?? '') . ', ' . ($this->district ? $this->district . ', ' : '') . ($this->state ?? '') . ' ' . ($this->postal_code ?? ''), ', '),
            $this->country ?? 'India',
        ]);

        return implode("\n", $lines);
    }

    public function getInlineAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line1,
            $this->address_line2,
            $this->landmark ? 'Near ' . $this->landmark : null,
            $this->area,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    public function getHasCoordinatesAttribute(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function getCoordinatesAttribute(): ?array
    {
        if (!$this->has_coordinates) {
            return null;
        }

        return [
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude,
        ];
    }

    public function getGoogleMapsUrlAttribute(): string
    {
        if ($this->has_coordinates) {
            return "https://www.google.com/maps/search/?api=1&query={$this->latitude},{$this->longitude}";
        }

        $query = urlencode($this->inline_address);
        return "https://www.google.com/maps/search/?api=1&query={$query}";
    }

    /**
     * Atomically promote this address as default for the user.
     */
    public function makeDefault(): self
    {
        DB::transaction(function () {
            static::where('user_id', $this->user_id)
                ->where('id', '!=', $this->id)
                ->update(['is_default' => false]);

            $this->update(['is_default' => true]);
        });

        return $this;
    }

    /**
     * Convert to array snapshot for order placement.
     */
    public function toSnapshotArray(): array
    {
        return [
            'recipient_name' => $this->recipient_name,
            'full_name' => $this->recipient_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2,
            'landmark' => $this->landmark,
            'area' => $this->area,
            'city' => $this->city,
            'district' => $this->district,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country ?? 'India',
            'latitude' => $this->latitude ? (float)$this->latitude : null,
            'longitude' => $this->longitude ? (float)$this->longitude : null,
            'delivery_instructions' => $this->delivery_instructions,
            'address_type' => $this->address_type ?? 'home',
            'is_verified' => (bool)$this->is_verified,
        ];
    }

    /**
     * GeoJSON Feature for Map rendering.
     */
    public function toGeoJsonFeature(): array
    {
        return [
            'type' => 'Feature',
            'geometry' => $this->has_coordinates ? [
                'type' => 'Point',
                'coordinates' => [(float) $this->longitude, (float) $this->latitude],
            ] : null,
            'properties' => [
                'id' => $this->id,
                'user_id' => $this->user_id,
                'recipient_name' => $this->recipient_name,
                'phone' => $this->phone,
                'city' => $this->city,
                'state' => $this->state,
                'postal_code' => $this->postal_code,
                'country' => $this->country,
                'is_default' => $this->is_default,
                'is_verified' => $this->is_verified,
                'inline_address' => $this->inline_address,
            ],
        ];
    }
}
