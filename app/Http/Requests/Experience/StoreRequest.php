<?php

namespace App\Http\Requests\Experience;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'place.required' => 'Поле "Место Работы " является обязательным для заполнения.',
            'post.required' => 'Поле "Специальность" является обязательным для заполнения.',
            'started.required' => 'Поле "Должность" является обязательным для заполнения.',
            'expired.required' => 'Поле "Период работы" является обязательным для заполнения.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'job_id' => 'required|int|exists:jobs,id',
            'place' => 'required|string|max:150',
            'post' => 'required|string|max:150',
            'started' => 'required|date',
            'expired' => 'nullable|date',
            'is_working' => 'required|boolean'
        ];
    }
}
