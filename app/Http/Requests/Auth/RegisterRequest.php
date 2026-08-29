<?php
namespace App\Http\Requests\Auth;
use Illuminate\Foundation\Http\FormRequest;
class RegisterRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'first_name'=>['required','string','max:100'],
            'last_name'=>['nullable','string','max:100'],
            'display_name'=>['nullable','string','max:150'],
            'email'=>['nullable','email','max:190','unique:users,email'],
            'mobile'=>['nullable','string','max:30','unique:users,mobile'],
            'password'=>['required','string','min:8','confirmed'],
            'whatsapp_opt_in'=>['boolean'],
            'marketing_opt_in'=>['boolean'],
        ];
    }
}
