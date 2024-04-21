<?php

namespace App\Http\Requests\BoxOffice;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Поле "Наименование кассы" является обязательным для заполнения.',
            'service.required' => 'Поле "Сервис" является обязательным для заполнения.',
            'bank_data_id.required' => 'Поле "Банковский счет *" является обязательным для заполнения.',
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
            'name' => 'required|string|max:100',
            'service' => 'required|string',
            'bank_data_id' => 'required|integer|exists:banks_data,id',
        ];
    }
}
