<?php
namespace App\Http\Requests\Order;
use Illuminate\Foundation\Http\FormRequest;
class PlaceOrderRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'shipping_address'=>['required','array'],
            'shipping_address.recipient_name'=>['required','string','max:150'],
            'shipping_address.phone'=>['required','string','max:30'],
            'shipping_address.address_line1'=>['required','string','max:255'],
            'shipping_address.city'=>['required','string','max:100'],
            'shipping_address.state'=>['required','string','max:100'],
            'shipping_address.postal_code'=>['required','string','max:20'],
            'shipping_address.country'=>['nullable','string','max:100'],
            'billing_address'=>['nullable','array'],
            'shipping_method_id'=>['nullable','integer','exists:shipping_methods,id'],
            'coupon_code'=>['nullable','string','max:80'],
        ];
    }
}
