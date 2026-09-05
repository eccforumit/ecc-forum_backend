<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyProfileUpdateRequest extends FormRequest
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
            'company_name' => ['sometimes', 'string', 'max:150'],
            'industry' => ['sometimes', 'string', 'in:tech,consulting,engineering,finance,energy,automotive,healthcare,education,retail,other'],
            'company_size' => ['sometimes', 'nullable', 'string', 'in:1-10,11-50,51-200,201-500,501-1000,1000+'],
            'company_description' => ['sometimes', 'nullable', 'string'],
            'website' => ['sometimes', 'nullable', 'url'],
            'logo' => ['sometimes', 'nullable', 'image', 'max:4096'],
            'address' => ['sometimes', 'nullable', 'string'],
            'contact_first_name' => ['sometimes', 'string', 'max:100'],
            'contact_last_name' => ['sometimes', 'string', 'max:100'],
            'contact_phone' => ['sometimes', 'regex:/^\+?[1-9]\d{1,14}$/'],
        ];
    }
}
