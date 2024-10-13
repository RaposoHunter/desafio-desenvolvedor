<?php

namespace App\Imports;

use App\Models\File;
use App\Models\FileRecord;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class FileRecordsImport implements ToModel, WithHeadingRow, WithCustomCsvSettings, WithChunkReading, WithBatchInserts, WithCustomChunkSize
{
    /**
     * Tamanho dos chunks de leitura/escrita da importação.
     *
     * NOTE: Valores menores podem aumentar o tempo de execução, mas reduzem o uso de memória. Alterar conforme a necessidade.
     */
    protected $chunk_size = 10000;

    public function __construct(public File $file)
    {
        // NOTE: Mesmo com a leitura/escrita do arquivo em chunks, é possível exceder o limite de memória.
        ini_set('memory_limit', '-1');

        // Remove a formatação padrão das colunas do cabeçalho para atribuição rápida dos valores ao model.
        HeadingRowFormatter::default('none');
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Recupera apenas os valores não vazios do array para evitar "sujar" a tabela.
        $attributes = array_filter($row);

        // Adiciona o ID do arquivo ao registro.
        $attributes['FileId'] = $this->file->id;

        return (new FileRecord($attributes));
    }

    public function beforeImport()
    {
        $this->file->fill([
            'status' => FileUploadStatus::Uploading,
        ])->saveQuietly();
    }

    public function afterImport()
    {
        $this->file->fill([
            'status' => FileUploadStatus::Uploaded,
        ])->saveQuietly();
    }

    public function headingRow(): int
    {
        // Os arquivos de exemplo possuem cabeçalhos na segunda linha. Sendo a primeira reservada ao status do arquivo.
        return 2;
    }

    /**
     * @return array
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return $this->chunk_size;
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return $this->chunk_size;
    }
}
