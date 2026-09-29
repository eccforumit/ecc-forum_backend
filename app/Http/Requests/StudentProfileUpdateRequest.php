<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentProfileUpdateRequest extends FormRequest
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
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'profile_image' => ['nullable', 'image', 'max:4096'],
            'school' => ['nullable', 'string', 'in:ECC,other'],
            'school_name' => ['nullable', 'string', 'max:150'],
            'major' => ['nullable', 'string', 'in:Ingenieur,Bachelor,Master'],
            'school_year' => ['nullable', 'string', 'in:1,2,3,4,Cesure,Laureat,Futur_diplome'],
            'linkedin_url' => ['nullable', 'string', 'max:255'],
            'github_url' => ['nullable', 'string', 'max:255'],
            'portfolio_url' => ['nullable', 'string', 'max:255'],
            'cv_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:8192'],
        ];
    }
}
