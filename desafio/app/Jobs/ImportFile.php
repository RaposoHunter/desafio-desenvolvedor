<?php

namespace App\Jobs;

use App\Models\File;
use App\Imports\FileRecordsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ImportFile implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public File $file) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Excel::import(
            import: new FileRecordsImport($this->file),
            filePath: public_path("upload/{$this->file->path}")
        );
    }
}
