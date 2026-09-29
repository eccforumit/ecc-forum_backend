<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QrCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:profile,cv'],
            'format' => ['sometimes', 'string', 'in:svg,png'],
            'size' => ['sometimes', 'integer', 'min:100', 'max:500']
        ];
    }
}
