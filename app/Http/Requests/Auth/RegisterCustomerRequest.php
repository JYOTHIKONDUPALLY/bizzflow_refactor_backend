<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'franchise_id' => ['required', 'string', 'exists:franchises,id'],
            'business_unit_id' => ['required', 'string', 'exists:business_units,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
            // 'country_id' => ['required', 'integer', 'exists:countries,id'],
            // 'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            // 'language' => ['nullable', 'string', 'max:5'],
        ];
    }
}