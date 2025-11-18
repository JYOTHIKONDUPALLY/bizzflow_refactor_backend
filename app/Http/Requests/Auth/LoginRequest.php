<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'franchise_id' => ['nullable', 'string', 'exists:franchises,id'],
            'location_id' => ['nullable', 'string', 'exists:locations,id'],
        ];
    }
}