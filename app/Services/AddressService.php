<?php

namespace App\Services;

use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AddressService
{
    public function __construct(
        protected AddressValidationService $validator = new AddressValidationService()
    ) {}

    /**
     * Store or update an address in the customer address book.
     */
    public function saveCustomerAddress(User $user, array $data, ?bool $makeDefault = null): CustomerAddress
    {
        $sanitized = $this->validator->sanitize($data);

        $hasAddresses = $user->addresses()->exists();
        $isDefault = $makeDefault ?? ($sanitized['is_default'] || !$hasAddresses);

        return DB::transaction(function () use ($user, $sanitized, $isDefault) {
            if ($isDefault) {
                $user->addresses()->update(['is_default' => false]);
            }

            $address = $user->addresses()->create([
                'address_type' => $sanitized['address_type'] ?? 'shipping',
                'recipient_name' => $sanitized['recipient_name'],
                'phone' => $sanitized['phone'],
                'email' => $sanitized['email'] ?? $user->email,
                'address_line1' => $sanitized['address_line1'],
                'address_line2' => $sanitized['address_line2'],
                'landmark' => $sanitized['landmark'],
                'area' => $sanitized['area'],
                'city' => $sanitized['city'],
                'district' => $sanitized['district'],
                'state' => $sanitized['state'],
                'postal_code' => $sanitized['postal_code'],
                'country' => $sanitized['country'] ?? 'India',
                'latitude' => $sanitized['latitude'],
                'longitude' => $sanitized['longitude'],
                'delivery_instructions' => $sanitized['delivery_instructions'],
                'is_default' => $isDefault,
                'is_verified' => $sanitized['is_verified'] ?? false,
            ]);

            return $address;
        });
    }

    /**
     * Snapshot shipping and billing addresses for an order permanently.
     * Guarantees that future edits by the customer in their address book NEVER alter historical orders.
     */
    public function snapshotOrderAddresses(Order $order, array $shippingData, ?array $billingData = null, ?int $customerAddressId = null): array
    {
        $shippingSanitized = $this->validator->sanitize($shippingData);
        $billingSanitized = $billingData ? $this->validator->sanitize($billingData) : $shippingSanitized;

        return DB::transaction(function () use ($order, $shippingSanitized, $billingSanitized, $customerAddressId) {
            // 1. Create or update permanent OrderAddress records
            $shippingAddress = $order->orderAddresses()->updateOrCreate(
                ['type' => 'shipping'],
                [
                    'customer_address_id' => $customerAddressId,
                    'full_name' => $shippingSanitized['full_name'],
                    'phone' => $shippingSanitized['phone'],
                    'email' => $shippingSanitized['email'] ?? $order->user?->email,
                    'address_line1' => $shippingSanitized['address_line1'],
                    'address_line2' => $shippingSanitized['address_line2'],
                    'landmark' => $shippingSanitized['landmark'],
                    'area' => $shippingSanitized['area'],
                    'city' => $shippingSanitized['city'],
                    'district' => $shippingSanitized['district'],
                    'state' => $shippingSanitized['state'],
                    'postal_code' => $shippingSanitized['postal_code'],
                    'country' => $shippingSanitized['country'] ?? 'India',
                    'latitude' => $shippingSanitized['latitude'],
                    'longitude' => $shippingSanitized['longitude'],
                    'delivery_instructions' => $shippingSanitized['delivery_instructions'],
                    'address_type' => $shippingSanitized['address_type'] ?? 'home',
                    'is_verified' => $shippingSanitized['is_verified'] ?? false,
                    'raw_payload' => $shippingSanitized,
                ]
            );

            $billingAddress = $order->orderAddresses()->updateOrCreate(
                ['type' => 'billing'],
                [
                    'customer_address_id' => null,
                    'full_name' => $billingSanitized['full_name'],
                    'phone' => $billingSanitized['phone'],
                    'email' => $billingSanitized['email'] ?? $order->user?->email,
                    'address_line1' => $billingSanitized['address_line1'],
                    'address_line2' => $billingSanitized['address_line2'],
                    'landmark' => $billingSanitized['landmark'],
                    'area' => $billingSanitized['area'],
                    'city' => $billingSanitized['city'],
                    'district' => $billingSanitized['district'],
                    'state' => $billingSanitized['state'],
                    'postal_code' => $billingSanitized['postal_code'],
                    'country' => $billingSanitized['country'] ?? 'India',
                    'latitude' => $billingSanitized['latitude'],
                    'longitude' => $billingSanitized['longitude'],
                    'delivery_instructions' => $billingSanitized['delivery_instructions'],
                    'address_type' => $billingSanitized['address_type'] ?? 'home',
                    'is_verified' => $billingSanitized['is_verified'] ?? false,
                    'raw_payload' => $billingSanitized,
                ]
            );

            // 2. Synchronize order JSON columns for backward-compatibility
            $order->update([
                'shipping_address_json' => $shippingAddress->toSnapshotArray(),
                'billing_address_json' => $billingAddress->toSnapshotArray(),
            ]);

            return [
                'shipping' => $shippingAddress,
                'billing' => $billingAddress,
            ];
        });
    }

    /**
     * Update an order address by administrator.
     */
    public function updateOrderAddress(Order $order, array $data, string $type = 'shipping'): OrderAddress
    {
        $sanitized = $this->validator->sanitize($data);

        return DB::transaction(function () use ($order, $sanitized, $type) {
            $orderAddress = $order->orderAddresses()->updateOrCreate(
                ['type' => $type],
                [
                    'full_name' => $sanitized['full_name'],
                    'phone' => $sanitized['phone'],
                    'email' => $sanitized['email'] ?? $order->user?->email,
                    'address_line1' => $sanitized['address_line1'],
                    'address_line2' => $sanitized['address_line2'],
                    'landmark' => $sanitized['landmark'],
                    'area' => $sanitized['area'],
                    'city' => $sanitized['city'],
                    'district' => $sanitized['district'],
                    'state' => $sanitized['state'],
                    'postal_code' => $sanitized['postal_code'],
                    'country' => $sanitized['country'] ?? 'India',
                    'latitude' => $sanitized['latitude'],
                    'longitude' => $sanitized['longitude'],
                    'delivery_instructions' => $sanitized['delivery_instructions'],
                    'address_type' => $sanitized['address_type'] ?? 'home',
                    'is_verified' => $sanitized['is_verified'] ?? false,
                    'raw_payload' => $sanitized,
                ]
            );

            // Update JSON mirror
            $column = $type === 'shipping' ? 'shipping_address_json' : 'billing_address_json';
            $order->update([
                $column => $orderAddress->toSnapshotArray(),
            ]);

            return $orderAddress;
        });
    }
}
