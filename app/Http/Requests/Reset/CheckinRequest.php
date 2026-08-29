<?php

namespace App\Http\Requests\Reset;

use Illuminate\Foundation\Http\FormRequest;

class CheckinRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'day_number' => ['required','integer','between:0,30'],
            'answers' => ['required','array'],
            'notes' => ['nullable','string','max:2000'],
        ];
    }
}
