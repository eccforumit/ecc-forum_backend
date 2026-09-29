<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CvUpdateRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:150'],
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email'],
            'phone' => ['sometimes', 'nullable', 'regex:/^\+?[1-9]\d{1,14}$/'],
            'location' => ['sometimes', 'nullable', 'string', 'max:150'],
            'summary' => ['sometimes', 'nullable', 'string'],
            'skills' => ['sometimes', 'array'],
            'skills.*.name' => ['required_with:skills', 'string', 'max:100'],
            'skills.*.level' => ['required_with:skills', 'string', 'in:beginner,intermediate,advanced,expert'],
            'work_experiences' => ['sometimes', 'array'],
            'work_experiences.*.company' => ['required_with:work_experiences', 'string', 'max:150'],
            'work_experiences.*.position' => ['nullable', 'string', 'max:150'],
            'work_experiences.*.startDate' => ['nullable', 'string', 'max:25'],
            'work_experiences.*.endDate' => ['nullable', 'string', 'max:25'],
            'work_experiences.*.description' => ['nullable', 'string'],
            'work_experiences.*.current' => ['nullable', 'boolean'],
            'educations' => ['sometimes', 'array'],
            'educations.*.institution' => ['required_with:educations', 'string', 'max:150'],
            'educations.*.degree' => ['nullable', 'string', 'max:150'],
            'educations.*.field' => ['nullable', 'string', 'max:150'],
            'educations.*.startDate' => ['nullable', 'string', 'max:25'],
            'educations.*.endDate' => ['nullable', 'string', 'max:25'],
            'educations.*.current' => ['nullable', 'boolean'],
            'languages' => ['sometimes', 'array'],
            'languages.*.language' => ['required_with:languages', 'string', 'max:100'],
            'languages.*.level' => ['nullable', 'string', 'max:100'],
            'website' => ['sometimes', 'nullable', 'url'],
            'linkedin' => ['sometimes', 'nullable', 'url'],
            'github' => ['sometimes', 'nullable', 'url'],
            'twitter' => ['sometimes', 'nullable', 'url'],
        ];
    }
}
