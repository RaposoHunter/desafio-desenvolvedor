<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileHistoryRequest extends FormRequest
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
            'name' => ['nullable', 'required_without:created_at', 'string', 'max:191', 'regex:/^[\wÁ-ý\- ]+$/i'],
            'created_at' => ['nullable', 'required_without:name', 'date_format:Y-m-d'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.regex' => 'O nome deve conter apenas caracteres alfanuméricos, espaços, hífens e sublinhados.',
            'created_at.date_format' => 'Informe uma data no formato YYYY-MM-DD.',
            'required_without' => 'Informe o nome ou a data de referência do arquivo.',
        ];
    }
}
