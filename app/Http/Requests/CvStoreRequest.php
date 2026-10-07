<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CvStoreRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:150'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'regex:/^\+?[1-9]\d{1,14}$/'],
            'location' => ['nullable', 'string', 'max:150'],
            'summary' => ['nullable', 'string'],
            'skills' => ['required', 'array'],
            'skills.*.name' => ['required', 'string', 'max:100'],
            'skills.*.level' => ['required', 'string', 'in:beginner,intermediate,advanced,expert'],
            'work_experiences' => ['nullable', 'array'],
            'work_experiences.*.company' => ['required_with:work_experiences.*', 'string', 'max:150'],
            'work_experiences.*.position' => ['nullable', 'string', 'max:150'],
            'work_experiences.*.startDate' => ['nullable', 'string', 'max:25'],
            'work_experiences.*.endDate' => ['nullable', 'string', 'max:25'],
            'work_experiences.*.description' => ['nullable', 'string'],
            'work_experiences.*.current' => ['nullable', 'boolean'],
            'educations' => ['nullable', 'array'],
            'educations.*.institution' => ['required_with:educations.*', 'string', 'max:150'],
            'educations.*.degree' => ['nullable', 'string', 'max:150'],
            'educations.*.field' => ['nullable', 'string', 'max:150'],
            'educations.*.startDate' => ['nullable', 'string', 'max:25'],
            'educations.*.endDate' => ['nullable', 'string', 'max:25'],
            'educations.*.current' => ['nullable', 'boolean'],
            'languages' => ['nullable', 'array'],
            'languages.*.language' => ['required_with:languages.*', 'string', 'max:100'],
            'languages.*.level' => ['nullable', 'string', 'max:100'],
            'website' => ['nullable', 'url'],
            'linkedin' => ['nullable', 'url'],
            'github' => ['nullable', 'url'],
            'twitter' => ['nullable', 'url'],
        ];
    }
}
