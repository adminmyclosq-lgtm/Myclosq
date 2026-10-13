<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAddress extends Model
{
    use HasFactory;

    protected $table = 'order_addresses';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_verified' => 'boolean',
            'raw_payload' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customerAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'customer_address_id');
    }

    /* Scopes */

    public function scopeShipping($query)
    {
        return $query->where('type', 'shipping');
    }

    public function scopeBilling($query)
    {
        return $query->where('type', 'billing');
    }

    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    public function scopeByState($query, string $state)
    {
        return $query->where('state', $state);
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByPostalCode($query, string $postalCode)
    {
        return $query->where('postal_code', $postalCode);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeHasCoordinates($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    /* Accessors & Map Helpers */

    public function getFormattedAddressAttribute(): string
    {
        $lines = array_filter([
            $this->full_name,
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

    public function getOpenStreetMapUrlAttribute(): string
    {
        if ($this->has_coordinates) {
            return "https://www.openstreetmap.org/?mlat={$this->latitude}&mlon={$this->longitude}#map=16/{$this->latitude}/{$this->longitude}";
        }

        $query = urlencode($this->inline_address);
        return "https://nominatim.openstreetmap.org/search?q={$query}";
    }

    /**
     * GeoJSON Feature representation for interactive mapping libraries (Leaflet, Mapbox, Google Maps API).
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
                'order_id' => $this->order_id,
                'type' => $this->type,
                'full_name' => $this->full_name,
                'phone' => $this->phone,
                'city' => $this->city,
                'state' => $this->state,
                'postal_code' => $this->postal_code,
                'country' => $this->country,
                'address_type' => $this->address_type,
                'is_verified' => $this->is_verified,
                'inline_address' => $this->inline_address,
                'delivery_instructions' => $this->delivery_instructions,
            ],
        ];
    }

    /**
     * Convert to standardized snapshot array for backward-compatible JSON storage.
     */
    public function toSnapshotArray(): array
    {
        return [
            'recipient_name' => $this->full_name,
            'full_name' => $this->full_name,
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
            'country' => $this->country,
            'latitude' => $this->latitude ? (float)$this->latitude : null,
            'longitude' => $this->longitude ? (float)$this->longitude : null,
            'delivery_instructions' => $this->delivery_instructions,
            'address_type' => $this->address_type,
            'is_verified' => $this->is_verified,
        ];
    }
}
