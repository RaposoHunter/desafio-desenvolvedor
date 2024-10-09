<?php

namespace App\Models;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Stringable;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'extension',
        'path',
        'size',
    ];

    /**
     * Realiza o upload do arquivo para o disco padrão.
     *
     * @return array{path:string,extension:string,size:int}
     *
     * @throws Exception
     */
    public static function upload(UploadedFile $file)
    {
        $filename = str($file->hashName('files'))
                        ->whenEndsWith('.txt', fn(Stringable $str) => $str->replaceEnd('.txt', '.csv'));

        throw_if(!$data['path'] = $file->storeAs($filename), new Exception('Erro ao salvar arquivo'));

        $data['extension'] = $file->getClientOriginalExtension();
        $data['size'] = $file->getSize();

        return $data;
    }
}
