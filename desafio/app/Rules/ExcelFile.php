<?php

namespace App\Rules;

use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rules\File;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;

/**
 * Aceita apenas arquivos Excel. (csv, xls and xlsx)
 */
class ExcelFile implements ValidationRule, ValidatorAwareRule, DataAwareRule
{
    /**
     * The validator instance.
     *
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * Run the validation rule.
     *
     * @param  \Illuminate\Http\UploadedFile $value
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Arquivos CSV são tratados como text/plain (txt) pela regra "mimes"
        // Então, é necessário verificar a extensão do arquivo manualmente
        $rule = File::types(['csv', 'xls', 'xlsx', 'txt'])
                    ->rules('bail')
                    ->rules([
                        function (string $attribute, UploadedFile $file, Closure $fail) {
                            if($file->extension() === 'txt' && $file->getClientOriginalExtension() !== 'csv') {
                                $fail($this->validator->customMessages['mimes'] ?? 'Informe um arquivo nos formatos csv, xls ou xlsx');
                            }
                        }
                    ])
                    ->setValidator($this->validator)
                    ->setData($this->data);

        if($rule->passes($attribute, $value)) return;

        $fail($rule->message()[0]);
    }

    /**
     * Set the current validator.
     */
    public function setValidator(Validator $validator): static
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
