<?php

namespace App\Http\Resources;

use App\Models\FileRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileRecordResource extends JsonResource
{
    /**
     * The resource instance.
     *
     * @var FileRecord
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge([
            'RptDt' => null,
            'TckrSymb' => null,
            'MktNm' => null,
            'SctyCtgyNm' => null,
            'ISIN' => null,
            'CrpnNm' => null
        ], $this->resource->toArray());
    }
}
