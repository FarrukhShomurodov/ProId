<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class JobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => Auth::user()->id
        ]);
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Поле "Тип професии" является обязательным для заполнения.',
            'profession.required' => 'Поле "Специальность" является обязательным для заполнения.',
            'education.required' => 'Поле "Информация обо образование" является обязательным для заполнения.'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'profession' => 'required|string',
            'education_id' => 'exists:education,id'
        ];
    }
}
