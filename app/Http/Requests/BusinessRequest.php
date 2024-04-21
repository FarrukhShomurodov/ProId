<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BusinessRequest extends FormRequest
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
            'inn.required' => 'Поле "ИНН" является обязательным для заполнения.',
            'name_of_business.required' => 'Поле "Название фирмы" является обязательным для заполнения.',
            'form_of_business.required' => 'Поле "Форма бизнеса " является обязательным для заполнения.',
            'oked.required' => 'Поле "ОКЭД бизнеса" является обязательным для заполнения.',
            'address.required' => 'Поле "Юридический адрес" является обязательным для заполнения.',
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
            'user_id' => 'required|integer|exists:users,id',
            'inn' => 'required|integer',
            'name_of_business' => 'required|string|max:200',
            'form_of_business' => 'required|string|max:200',
            'oked' => 'required|integer',
            'address' => 'required|string',
        ];
    }
}
