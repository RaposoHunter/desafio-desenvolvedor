<?php

namespace App\Models;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Stringable;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['download_path'];

    /**
     * Get the value of the file's download path.
     *
     * @param  string  $value
     * @return string
     */
    public function getDownloadPathAttribute($value)
    {
        return Storage::url($this->path);
    }

    /**
     * Retorna um arquivo baseado no nome e/ou data de referência.
     *
     * @param array{name?:string,created_at?:string} $input
     *
     * @return static
     */
    public static function history(array $input)
    {
        return static::query()
                     ->when(!empty($input['name']), fn($query) => $query->where('name', 'like', "%{$input['name']}%"))
                     ->when(!empty($input['created_at']), fn($query) => $query->whereDate('created_at', $input['created_at']))
                     ->latest()
                     ->first();
    }

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
