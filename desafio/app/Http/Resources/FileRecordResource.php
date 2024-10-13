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
        if(!$this->resource) return [];

        $data = [];

        foreach($this->resource->getFillable() as $fillable) {
            $data[$fillable] = $this->resource->$fillable;
        }

        return array_merge($data, parent::toArray($request));
    }
}
