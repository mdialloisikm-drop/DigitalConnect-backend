<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'user_type' => 'required|in:admin,client,freelance',

            // Pour les freelances
            'title' => 'required_if:user_type,freelance|string',
            'description' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric',
            'experience_years' => 'nullable|integer',

            // Pour les clients
            'company_name' => 'nullable|string',
            'company_description' => 'nullable|string',
        ];
    }
}
