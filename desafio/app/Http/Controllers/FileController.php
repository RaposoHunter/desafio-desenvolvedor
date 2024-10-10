<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\File;
use App\Jobs\ImportFile;
use Illuminate\Http\Response;
use App\Enums\FileUploadStatus;
use App\Http\Requests\FileRequest;
use App\Http\Resources\FileResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\FileHistoryRequest;

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
    public function show(File $file)
    {
        //
    }
}
