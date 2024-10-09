<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\File;
use Illuminate\Http\Response;
use App\Http\Requests\FileRequest;
use App\Http\Resources\FileResource;
use Illuminate\Support\Facades\Storage;

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
