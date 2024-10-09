<?php

namespace App\Http\Requests;

use App\Rules\ExcelFile;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @method array{name:string,file:\Illuminate\Http\UploadedFile} validated(array|int|string|null $key = null, mixed $default = null) Get the validated data from the request.
 */
class FileRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Caso o usuário não informe um nome para o arquivo, o nome do arquivo será utilizado (caso exista)
        if($this->filled('name')) return;

        if(!$file = $this->file('file')) return;

        if(is_array($file)) return;

        $this->merge([
            'name' => pathinfo($file->getClientOriginalName(), \PATHINFO_FILENAME)
        ]);
    }

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
            'name' => ['nullable', 'string', 'max:191', 'regex:/^[\wÁ-ý\- ]+$/i', 'unique:files,name'],
            'file' => ['required', new ExcelFile],
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
            'name.max' => 'O nome do arquivo deve ter no máximo :max caracteres',
            'name.regex' => 'O nome do arquivo deve conter apenas caracteres alfanuméricos, espaços, hífens e sublinhados',
            'name.unique' => 'O nome do arquivo já está em uso',
            'required' => 'Campo obrigatório',
            'file.file' => 'Arquivo inválido',
            'mimes' => 'Informe um arquivo nos formatos csv, xls ou xlsx',
        ];
    }
}
