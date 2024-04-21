<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EducationRequest extends FormRequest
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
            'type.required' => 'Поле "Вид образования" является обязательным для заполнения.',
            'name.required' => 'Поле "Наименование учебного заведения" является обязательным для заполнения.',
            'is_studying.required' => 'Поле "МФО" является обязательным для заполнения.',
            'started.required' => 'Поле "Период обучения" является обязательным для заполнения.',
            'expired.required' => 'Поле "Период обучения" является обязательным для заполнения.',
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
            'name' => 'required|string',
            'is_studying' => 'required|boolean',
            'started' => 'required|date',
            'expired' => 'nullable|date',
        ];
    }
}
