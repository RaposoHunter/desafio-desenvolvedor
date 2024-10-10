<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\File;
use App\Jobs\ImportFile;
use Illuminate\Http\Response;
use App\Enums\FileUploadStatus;
use App\Http\Requests\FileRequest;
use App\Http\Resources\FileResource;
use MongoDB\Laravel\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\FileContentRequest;
use App\Http\Requests\FileHistoryRequest;
use App\Http\Resources\FileRecordResource;
use Illuminate\Pagination\LengthAwarePaginator;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FileRequest $request)
    {
        $input = $request->validated();

        try {
            $input = array_merge($input, File::upload($request->file('file')));
            $input['status'] = FileUploadStatus::Pending;

            $file = File::query()->create($input);
        } catch(Exception $e) {
            logger()->error('Erro ao salvar arquivo', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => $input,
            ]);

            if(isset($input['path'])) {
                Storage::delete($input['path']);
            }

            return response()->json([
                'message' => 'Algo deu errado ao salvar o arquivo. Tente novamente mais tarde!',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        dispatch(new ImportFile($file));

        return new FileResource($file);
    }

    /**
     * Retorna um arquivo baseado no nome e/ou data de referência.
     */
    public function history(FileHistoryRequest $request)
    {
        $file = File::history($request->validated());

        return new FileResource($file);
    }

    /**
     * Display the specified resource.
     */
    public function content(File $file, FileContentRequest $request)
    {
        if(Storage::fileMissing($file->path)) {
            return response()->json([
                'message' => 'Arquivo não encontrado',
            ], Response::HTTP_NOT_FOUND);
        }

        if(!$file->uploaded()) {
            return response()->json([
                'message' => 'O arquivo ainda não processado. Aguarde mais uns minutos e tente novamente.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $records = $file->records();

        if($request->filled('RptDt') || $request->filled('TckrSymb')) {
            $records = $records->when($request->filled('RptDt'), fn(Builder $query) => $query->where('RptDt', $request->input('RptDt')))
                                ->when($request->filled('TckrSymb'), fn(Builder $query) => $query->where('TckrSymb', $request->input('TckrSymb')))
                                ->first();
        } else {
            $records = $records->paginate(10);
        }

        return $records instanceof LengthAwarePaginator
            ? FileRecordResource::collection($records)
            : new FileRecordResource($records);
    }
}
