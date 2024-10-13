<?php

namespace App\Imports;

use App\Models\File;
use App\Models\FileRecord;
use App\Enums\FileUploadStatus;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class FileRecordsImport implements ToModel, WithHeadingRow, WithCustomCsvSettings, WithChunkReading, WithBatchInserts, WithCustomChunkSize, WithEvents
{
    use RegistersEventListeners;

    private int $processed_bytes = 0;
    private int $records = 0;

    /**
     * Tamanho dos chunks de leitura/escrita da importação.
     *
     * NOTE: Valores menores podem aumentar o tempo de execução, mas reduzem o uso de memória. Alterar conforme a necessidade.
     */
    protected $chunk_size = 10000;

    public function __construct(public File $file)
    {
        // NOTE: Mesmo com a leitura/escrita do arquivo em chunks, é possível exceder o limite de memória.
        ini_set('memory_limit', '512M');

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
        // Incrementando o total de bytes processados para cálculo do progresso.
        $this->processed_bytes += strlen(implode(';', $row));

        // Incrementando o total de registros processados.
        $this->records++;

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
            'records' => 0,
            'progress' => 0.0,
        ])->saveQuietly();
    }

    public function afterBatch()
    {
        $this->file->fill([
            'records' => $this->records,
            'progress' => round($this->processed_bytes / $this->file->size, 4) * 100,
        ])->saveQuietly();
    }

    public function afterImport()
    {
        $this->file->fill([
            'status' => FileUploadStatus::Uploaded,
            'progress' => 100.0,
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
