<?php

namespace App\Models;

use Exception;
use App\Enums\FileUploadStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Stringable;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use MongoDB\Laravel\Relations\HasMany;
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
        'status',
        'records',
        'progress',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['download_path'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => FileUploadStatus::class,
        ];
    }

    /**
     * Get all of the records for the File
     *
     * @return \MongoDB\Laravel\Relations\HasMany
     */
    public function records(): HasMany
    {
        return $this->hasMany(FileRecord::class, 'FileId', '_id');
    }

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
        $name = $input['name'] ?? null;
        $created_at = $input['created_at'] ?? null;

        $key = "CachedFile[name={$name}][created_at={$created_at}]";

        return Cache::remember($key, now()->endOfWeek(), fn() =>
            static::query()
                ->when(!empty($name), fn($query) => $query->where('name', 'like', "%{$name}%"))
                ->when(!empty($created_at), fn($query) => $query->whereDate('created_at', $created_at))
                ->latest()
                ->first()
        );

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

    public function uploaded()
    {
        return $this->status === FileUploadStatus::Uploaded;
    }
}
