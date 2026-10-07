<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRegistrationRequest extends FormRequest
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
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'confirm_password' => ['required', 'same:password'],
            'company_name' => ['required', 'string', 'max:150'],
            'industry' => ['required', 'string', 'in:tech,consulting,engineering,finance,energy,automotive,healthcare,education,retail,other'],
            'company_size' => ['nullable', 'string', 'in:1-10,11-50,51-200,201-500,501-1000,1000+'],
            'company_description' => ['nullable', 'string'],
            'website' => ['nullable', 'url'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'address' => ['nullable', 'string'],
            'contact_first_name' => ['required', 'string', 'max:100'],
            'contact_last_name' => ['required', 'string', 'max:100'],
            'contact_phone' => ['required', 'regex:/^\+?[0-9]{7,15}$/'],
            'accept_terms' => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('confirm_password')) {
            $this->merge([
                'password_confirmation' => $this->input('confirm_password'),
            ]);
        }
    }
}
