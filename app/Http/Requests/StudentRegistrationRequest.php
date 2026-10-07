<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRegistrationRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['nullable', 'regex:/^[\+]?[0-9\s\-\(\)\.]{8,20}$/'],
            'profile_image' => ['nullable', 'image', 'max:4096'],
            'school' => ['required', 'string', 'in:ECC,other'],
            'school_name' => ['nullable', 'required_if:school,other', 'string', 'max:150'],
            'major' => ['required', 'string', 'in:Ingenieur,Bachelor,Master'],
            'school_year' => ['required', 'string', 'in:1,2,3,4,Cesure,Laureat,Futur_diplome'],
            'linkedin_url' => ['nullable', 'url'],
            'github_url' => ['nullable', 'url'],
            'portfolio_url' => ['nullable', 'url'],
            'cv_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:8192'],
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
