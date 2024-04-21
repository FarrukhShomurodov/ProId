<?php

namespace App\Http\Requests\BankData;

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

    public function messages()
    {
        return [
            'name.required' => 'Поле "Наиминование счета" является обязательным для заполнения.',
            'name_of_banking_akkaunt.required' => 'Поле "Расчетный счет" является обязательным для заполнения.',
            'mfo.required' => 'Поле "МФО" является обязательным для заполнения.',
            'payment_account.required' => 'Поле "Наиминование банка" является обязательным для заполнения.',
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'name_of_banking_akkaunt' => 'required|string|max:100',
            'mfo' => 'required|integer',
            'payment_account' => 'required|integer',
        ];
    }
}
