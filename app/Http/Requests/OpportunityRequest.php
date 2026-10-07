<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpportunityRequest extends FormRequest
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
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'title' => [$required, 'string', 'max:150'],
            'location' => ['sometimes', 'nullable', 'string', 'max:150'],
            'employment_type' => ['sometimes', 'nullable', 'string', 'max:100'],
            'is_remote' => ['sometimes', 'boolean'],
            'salary_range' => ['sometimes', 'nullable', 'string', 'max:100'],
            'skills' => ['sometimes', 'array'],
            'skills.*' => ['string', 'max:100'],
            'description' => [$required, 'string'],
            'requirements' => ['sometimes', 'nullable', 'string'],
            'benefits' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:draft,published,closed'],
        ];
    }
}
