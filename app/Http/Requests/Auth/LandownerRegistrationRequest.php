<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LandownerRegistrationRequest extends FormRequest
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

            'phone_number' =>['required','digits:11','numeric','unique:users,phone_number'] ,
            'username' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:landowner',
        ];

    }
    public function messages()
    {
        return [
            'phone_number.digits:11' => 'Please enter a valid phone number (11 digits).',

            // ... other message overrides ...
        ];
    }
}
