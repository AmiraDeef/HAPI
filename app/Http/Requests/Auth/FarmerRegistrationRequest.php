<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class FarmerRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => 'required|string|max:11|unique:users,phone_number',
            'username' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:farmer',
            'farm_id' => 'required|exists:farms,unique_farm_id',
        ];
    }
}
