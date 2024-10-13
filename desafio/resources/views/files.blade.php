@extends('layouts.main')

@push('css')
    <style scoped>
        .file-container {
            cursor: pointer;
            height: 100%;
            transition: border 0.1s;
        }

        .file-container:hover {
            border-color: grey;
        }

        .file-container.active {
            border-color: #d20000;
        }

        .file-container .progress-bar {
            height: 3px;
            background-color: #d20000;

            border-radius: 0 var(--bs-card-inner-border-radius) var(--bs-card-inner-border-radius) var(--bs-card-inner-border-radius);

            transition: width 0.5s;
        }

        .file-container .progress-bar.uploaded {
            border-top-right-radius: 0;
        }

        .file-container.add-file {
            min-height: 125px;
            border: 2px dashed #ccc;
        }

        .file-container.add-file:hover {
            border-color: grey;
        }

        .file-container.add-file .card-body {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
        }

        .active>.page-link, .page-link.active {
            background-color: #d20000 !important;
            border-color: #d20000 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <h1 class="text-center mt-2 mb-4">Arquivos</h1>

        <div id="pagination-container" class="row text-center d-none">
            <nav aria-label="File Navigation">
                <ul class="pagination pagination-sm justify-content-center">
                    <li class="page-item">
                        <button class="page-link" href="" aria-label="Anterior">
                            <span aria-hidden="true">&laquo;</span>
                        </button>
                    </li>
                    <li class="page-item">
                        <button class="page-link" href="" aria-label="Próximo">
                            <span aria-hidden="true">&raquo;</span>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="row gy-3 files-container">
            <div class="col-md-4">
                <div class="card file-container add-file" data-bs-toggle="modal" data-bs-target="#add-file-modal">
                    <div class="card-body">
                        + Adicionar
                    </div>
                </div>
            </div>
        </div>

        <div id="file-records-container" class="row mt-3 d-none">
            <div class="table-responsive">
                <table id="file-records-table" class="table table-striped">
                    <thead>
                        <tr>
                            @foreach((new \App\Models\FileRecord)->getFillable() as $fillable)
                                @if($fillable === 'FileId')
                                    @continue
                                @endif

                                <th>{{ $fillable }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="file-records-table-empty" class="d-none">
                            <td colspan="{{ count((new \App\Models\FileRecord)->getFillable()) - 1 }}">
                                Nenhum registro encontrado
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-2 d-none" id="file-records-pagination-container">
                <nav aria-label="File Records Navigation">
                    <ul class="pagination pagination-sm justify-content-center">
                        <li class="page-item">
                            <button class="page-link" href="" aria-label="Anterior">
                                <span aria-hidden="true">&laquo;</span>
                            </button>
                        </li>
                        <li class="page-item">
                            <button class="page-link" href="" aria-label="Próximo">
                                <span aria-hidden="true">&raquo;</span>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add-file-modal" tabindex="-1" role="dialog" aria-labelledby="add-file-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="add-file-modal-label">Adicionar arquivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-file-form">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <label for="name" class="form-label">Nome</label>
                                    <input type="text" class="form-control" />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name" class="form-label">Arquivo <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" accept=".csv,.xls,.xlsx" required />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Fechar</button>
                    <button form="add-file-form" type="submit" class="btn btn-primary">Adicionar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

<template id="file-card-template">
    <div class="col-md-4">
        <div class="card file-container">
            <div class="card-header text-center">
                <span class="file-name"></span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="row">
                        <div class="col-4 fw-bold file-info-key-link">Link</div>
                        <div class="col-8 text-end file-info-value-link">
                            <a class="text-decoration-underline" href="" target="_blank" rel="noreferrer">Clique para baixar</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4 fw-bold file-info-key-type">Tipo</div>
                        <div class="col-8 text-end file-info-value-type"></div>
                    </div>

                    <div class="row">
                        <div class="col-4 fw-bold file-info-key-size">Tamanho</div>
                        <div class="col-8 text-end file-info-value-size"></div>
                    </div>

                    <div class="row">
                        <div class="col-4 fw-bold file-info-key-status">Status</div>
                        <div class="col-8 text-end file-info-value-status"></div>
                    </div>

                    <div class="row">
                        <div class="col-4 fw-bold file-info-key-records">Registros</div>
                        <div class="col-8 text-end file-info-value-records"></div>
                    </div>

                    <div class="row">
                        <div class="col-4 fw-bold file-info-key-createdAt">Criado em</div>
                        <div class="col-8 text-end file-info-value-createdAt"></div>
                    </div>
                </div>
            </div>
            <div class="progress-bar" style="width: 0%"></div>
        </div>
    </div>
</template>

<template id="pagination-template">
    <li class="page-item page-item-page"><button class="page-link"></button></li>
</template>

<template id="file-record-row">
    <tr>
        @foreach((new \App\Models\FileRecord)->getFillable() as $fillable)
            @if($fillable === 'FileId')
                @continue
            @endif

            <td data-key="{{ $fillable }}"></td>
        @endforeach
    </tr>
</template>

@push('js')
    <script src="{{ asset('js/file.js') }}"></script>
@endpush
