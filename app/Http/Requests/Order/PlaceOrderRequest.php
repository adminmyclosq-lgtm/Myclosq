<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_address' => ['required', 'array'],
            'shipping_address.recipient_name' => ['required', 'string', 'min:2', 'max:150'],
            'shipping_address.phone' => ['required', 'string', 'max:30'],
            'shipping_address.email' => ['nullable', 'email', 'max:150'],
            'shipping_address.address_line1' => ['required', 'string', 'max:255'],
            'shipping_address.address_line2' => ['nullable', 'string', 'max:255'],
            'shipping_address.landmark' => ['nullable', 'string', 'max:150'],
            'shipping_address.area' => ['nullable', 'string', 'max:100'],
            'shipping_address.city' => ['required', 'string', 'max:100'],
            'shipping_address.district' => ['nullable', 'string', 'max:100'],
            'shipping_address.state' => ['required', 'string', 'max:100'],
            'shipping_address.postal_code' => ['required', 'string', 'max:20'],
            'shipping_address.country' => ['nullable', 'string', 'max:100'],
            'shipping_address.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'shipping_address.longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'shipping_address.delivery_instructions' => ['nullable', 'string', 'max:500'],
            'shipping_address.address_type' => ['nullable', 'string', 'in:home,office,other,shipping'],

            'billing_address' => ['nullable', 'array'],
            'billing_address.recipient_name' => ['nullable', 'string', 'max:150'],
            'billing_address.phone' => ['nullable', 'string', 'max:30'],
            'billing_address.address_line1' => ['nullable', 'string', 'max:255'],
            'billing_address.address_line2' => ['nullable', 'string', 'max:255'],
            'billing_address.city' => ['nullable', 'string', 'max:100'],
            'billing_address.state' => ['nullable', 'string', 'max:100'],
            'billing_address.postal_code' => ['nullable', 'string', 'max:20'],
            'billing_address.country' => ['nullable', 'string', 'max:100'],

            'shipping_method_id' => ['nullable', 'integer', 'exists:shipping_methods,id'],
            'coupon_code' => ['nullable', 'string', 'max:80'],
            'payment_method' => ['required', 'in:online,cod'],
        ];
    }
}
