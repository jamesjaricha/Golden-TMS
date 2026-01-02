<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'whatsapp_number' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]+$/'],
            'whatsapp_notifications_enabled' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert checkbox value to boolean
        $this->merge([
            'whatsapp_notifications_enabled' => $this->has('whatsapp_notifications_enabled'),
        ]);

        // Clean up WhatsApp number - remove spaces and dashes
        if ($this->whatsapp_number) {
            $this->merge([
                'whatsapp_number' => preg_replace('/[\s\-]/', '', $this->whatsapp_number),
            ]);
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'whatsapp_number.regex' => 'Please enter a valid phone number with country code (e.g., +263771234567)',
        ];
    }
}
