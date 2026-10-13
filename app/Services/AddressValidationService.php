<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AddressValidationService
{
    /**
     * Get validation rules for an address payload.
     */
    public static function rules(string $prefix = '', bool $isRequired = true): array
    {
        $req = $isRequired ? 'required' : 'nullable';
        $p = $prefix ? rtrim($prefix, '.') . '.' : '';

        return [
            $p . 'recipient_name' => [$req, 'string', 'min:2', 'max:150'],
            $p . 'full_name' => ['nullable', 'string', 'min:2', 'max:150'],
            $p . 'phone' => [$req, 'string', 'regex:/^(\+91[\-\s]?)?[0]?[6789]\d{9}$|^(\+[1-9]{1}[0-9]{3,14})$/'],
            $p . 'email' => ['nullable', 'email', 'max:150'],
            $p . 'address_line1' => [$req, 'string', 'min:3', 'max:255'],
            $p . 'address_line2' => ['nullable', 'string', 'max:255'],
            $p . 'landmark' => ['nullable', 'string', 'max:150'],
            $p . 'area' => ['nullable', 'string', 'max:100'],
            $p . 'city' => [$req, 'string', 'min:2', 'max:100'],
            $p . 'district' => ['nullable', 'string', 'max:100'],
            $p . 'state' => [$req, 'string', 'min:2', 'max:100'],
            $p . 'postal_code' => [$req, 'string', 'regex:/^[1-9][0-9]{5}$|^[A-Za-z0-9\s\-]{3,12}$/'],
            $p . 'country' => ['nullable', 'string', 'max:100'],
            $p . 'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            $p . 'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            $p . 'delivery_instructions' => ['nullable', 'string', 'max:500'],
            $p . 'address_type' => ['nullable', 'string', 'in:home,office,other,shipping,billing'],
            $p . 'is_default' => ['nullable', 'boolean'],
            $p . 'is_verified' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom validation error messages.
     */
    public static function messages(string $prefix = ''): array
    {
        $p = $prefix ? rtrim($prefix, '.') . '.' : '';

        return [
            $p . 'recipient_name.required' => 'Recipient full name is required.',
            $p . 'phone.regex' => 'Please provide a valid 10-digit mobile number or international phone with country code.',
            $p . 'address_line1.required' => 'Street address line 1 is required.',
            $p . 'city.required' => 'City is required.',
            $p . 'state.required' => 'State / Province is required.',
            $p . 'postal_code.regex' => 'Please provide a valid 6-digit postal PIN code or postal code.',
            $p . 'latitude.between' => 'Latitude must be between -90 and 90 degrees.',
            $p . 'longitude.between' => 'Longitude must be between -180 and 180 degrees.',
        ];
    }

    /**
     * Validate an address array, throwing ValidationException on failure.
     */
    public function validate(array $data, bool $isRequired = true): array
    {
        $validator = Validator::make($data, self::rules('', $isRequired), self::messages(''));

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->sanitize($validator->validated());
    }

    /**
     * Clean and normalize address input.
     */
    public function sanitize(array $data): array
    {
        $name = trim($data['full_name'] ?? ($data['recipient_name'] ?? ''));

        // Strip non-digit characters except leading plus from phone
        $rawPhone = trim($data['phone'] ?? '');
        $cleanedPhone = preg_replace('/[^\d+]/', '', $rawPhone);
        if (strlen($cleanedPhone) === 10 && !str_starts_with($cleanedPhone, '+')) {
            $cleanedPhone = '+91' . $cleanedPhone;
        }

        $addressType = strtolower(trim($data['address_type'] ?? 'home'));
        if (!in_array($addressType, ['home', 'office', 'other', 'shipping', 'billing'])) {
            $addressType = 'home';
        }

        return [
            'recipient_name' => $name,
            'full_name' => $name,
            'phone' => $cleanedPhone ?: null,
            'email' => !empty($data['email']) ? strtolower(trim($data['email'])) : null,
            'address_line1' => trim($data['address_line1'] ?? ''),
            'address_line2' => !empty($data['address_line2']) ? trim($data['address_line2']) : null,
            'landmark' => !empty($data['landmark']) ? trim($data['landmark']) : null,
            'area' => !empty($data['area']) ? trim($data['area']) : null,
            'city' => ucwords(strtolower(trim($data['city'] ?? ''))),
            'district' => !empty($data['district']) ? ucwords(strtolower(trim($data['district']))) : null,
            'state' => ucwords(strtolower(trim($data['state'] ?? ''))),
            'postal_code' => strtoupper(trim($data['postal_code'] ?? '')),
            'country' => !empty($data['country']) ? trim($data['country']) : 'India',
            'latitude' => isset($data['latitude']) && $data['latitude'] !== '' ? (float)$data['latitude'] : null,
            'longitude' => isset($data['longitude']) && $data['longitude'] !== '' ? (float)$data['longitude'] : null,
            'delivery_instructions' => !empty($data['delivery_instructions']) ? trim($data['delivery_instructions']) : null,
            'address_type' => $addressType,
            'is_default' => !empty($data['is_default']),
            'is_verified' => !empty($data['is_verified']),
        ];
    }
}
