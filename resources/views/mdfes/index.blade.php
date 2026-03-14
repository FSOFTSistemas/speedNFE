@extends('adminlte::page')

@section('title', 'Resumo de Notas MDFe')

@push('css')
<style>
    /* Estilos importados para consistência */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    :root {
        --primary-color: #00033a;
        --success-color: #28a745;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --text-dark: #343a40;
        --text-light: #6c757d;
        --action-view: #007bff;
        --action-delete: #dc3545;
        --action-send: #28a745;
        --info-color: #17a2b8;
        --warning-color: #ffc107;
    }

    body {
        font-family: 'Poppins', sans-serif;
    }
    
    .card-main {
        background: var(--card-bg);
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px var(--shadow-color);
        padding: 30px;
    }
    
    .custom-btn {
        font-weight: 500;
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .custom-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .custom-btn-primary { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; color: #fff !important; }
    .custom-btn-info { background-color: var(--info-color) !important; border-color: var(--info-color) !important; color: #fff !important; }
    .custom-btn-warning { background-color: var(--warning-color) !important; border-color: var(--warning-color) !important; color: #000 !important; }
    .custom-btn-success { background-color: var(--success-color) !important; border-color: var(--success-color) !important; color: #fff !important; }
    .custom-btn-danger { background-color: var(--action-delete) !important; border-color: var(--action-delete) !important; color: #fff !important; }
    
    /* Otimização dos botões do cabeçalho para mobile */
    .header-buttons .btn {
        display: block;
        margin-bottom: 8px;
    }
     .header-buttons .btn:last-child {
        margin-bottom: 0;
    }
    @media (min-width: 992px) {
        .header-buttons .btn {
            display: inline-block;
            margin-bottom: 0;
            margin-left: 8px;
        }
    }
    
    /* Estilos da Tabela */
    .table thead th, .table tbody td {
        background-color: transparent !important;
        vertical-align: middle;
        text-align: center;
    }
    .table thead th {
        color: var(--text-dark) !important;
        font-weight: 600;
        border-bottom: 2px solid var(--border-color) !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table tbody tr:hover {
        background-color: #f1f1f1 !important;
    }

    /* Ações */
    .action-buttons {
        white-space: nowrap;
    }
    .action-buttons a {
        color: var(--text-light);
        margin: 0 8px;
        font-size: 1.2rem;
        transition: color 0.3s ease;
    }
    .action-buttons a:hover.text-danger, .action-buttons a:hover.text-red { color: var(--action-delete) !important; }
    .action-buttons a:hover.text-success { color: var(--action-send) !important; }
    .action-buttons a:hover.text-primary, .action-buttons a:hover.text-blue { color: var(--action-view) !important; }
    .action-buttons a:hover.text-info, .action-buttons a:hover.text-teal { color: var(--info-color) !important; }
    .action-buttons a:hover.text-green { color: var(--success-color) !important; }
    .action-buttons a:hover.text-yellow, .action-buttons a:hover.text-orange { color: var(--warning-color) !important; }


    /* Modal */
    .modal-content {
        border-radius: 15px;
        border: none;
    }
    .modal-header {
        border-bottom: 1px solid #f0f0f0;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        padding: 10px 15px;
        height: auto;
    }
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 3, 58, .25);
    }
    .bloqueado {
        opacity: 0.5;
        pointer-events: none;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Resumo de Notas MDFe</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            <a class="btn custom-btn custom-btn-primary" href="{{ route('mdfe.create') }}"><i class="fas fa-plus mr-1"></i> Emitir MDFe</a>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-main">
        <div class="card-body p-0">
            @component('components.dataTable', [
                'responsive' => true,
                'searching' => true,
                'lengthChange' => true,
                'pageLength' => 10,
                'ordering' => true,
                'showFooter' => false,
            ])
                <thead class="table-light">
                    <tr>
                        <th>Nº</th>
                        <th>Série</th>
                        <th>Emissão</th>
                        <th>Situação</th>
                        <th>UF Início</th>
                        <th>UF Fim</th>
                        @if ($empresa == 1)
                            <th>Empresa</th>
                        @endif
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mdfes as $mdfe)
                        <tr>
                            <td>{{ $mdfe->numero }}</td>
                            <td>{{ $mdfe->serie }}</td>
                            <td>{{ date('d/m/Y', strtotime($mdfe->data)) }}</td>
                            <td>
                                @if ($mdfe->situacao->name == 'CANCELADO')
                                    <span class="badge badge-danger">{{ $mdfe->situacao->name }}</span>
                                @elseif ($mdfe->situacao->name == 'AUTORIZADO')
                                    <span class="badge badge-success">{{ $mdfe->situacao->name }}</span>
                                @elseif ($mdfe->situacao->name == 'ENCERRADO')
                                    <span class="badge badge-primary">{{ $mdfe->situacao->name }}</span>
                                @else
                                    <span class="badge badge-warning">{{ $mdfe->situacao->name }}</span>
                                @endif
                            </td>
                            <td>{{ $mdfe->uf_inicio }}</td>
                            <td>{{ $mdfe->uf_termino }}</td>
                            @if ($empresa == 1)
                                <td>{{ $mdfe->fantasia }}</td>
                            @endif
                            <td class="action-buttons">
                                @if ($mdfe->situacao->value === 'Pendente' || $mdfe->situacao->value === 'Rejeitado')
                                    <a title="Visualizar" target="_blank" href='{{ route('mdfe.view', [$mdfe->id]) }}' class='text-primary'><i class="far fa-eye"></i></a>
                                    <a title="Editar" href='{{ route('mdfe.edit', [$mdfe->id]) }}' class='text-info'><i class="far fa-edit"></i></a>
                                    <a title="Excluir" href="#" onclick="setaDadosModalExcluir({{ $mdfe->id }})" class='text-danger' data-toggle="modal" data-target=".bd-delete-modal-lg"><i class="far fa-trash-alt"></i></a>
                                    <a title="Enviar MDFe" onclick="loadPage()" href="{{ route('mdfe.enviar', [$mdfe->id]) }}" class='text-success'><i class="fa fa-upload"></i></a>
                                @elseif ($mdfe->situacao->value === 'Autorizado')
                                    <a title="Cancelar" href="#" onclick="setaDadosModalCancelar({{ $mdfe->id }})" class='text-danger' data-toggle="modal" data-target=".bd-cancel-modal-lg"><i class="fa fa-ban"></i></a>
                                    <a title="Encerrar" onclick="loadPage()" href='{{ route('mdfe.close', [$mdfe->id]) }}' class='text-secondary'><i class="fa fa-truck-loading"></i></a>
                                    <a title="Imprimir" target="_blank" href='{{ route('mdfe.print', [$mdfe->id, 0]) }}' class='text-green'><i class="fa fa-print"></i></a>
                                    <a title="Baixar XML" href="{{ route('mdfe.downloadXML', [$mdfe->id]) }}" class="text-blue"><i class="fas fa-download"></i></a>
                                @elseif ($mdfe->situacao->value === 'Encerrado')
                                    <a title="Imprimir Encerramento" target="_blank" href='{{ route('mdfe.print', [$mdfe->id, 1]) }}' class='text-yellow'><i class="fa fa-print"></i></a>
                                    <a title="Baixar XML" href="{{ route('mdfe.downloadXML', [$mdfe->id]) }}" class="text-primary"><i class="fas fa-download"></i></a>
                                @else
                                    <a title="Imprimir Cancelamento" target="_blank" href='{{ route('mdfe.print', [$mdfe->id, 2]) }}' class='text-orange'><i class="fa fa-print"></i></a>
                                    <a title="Baixar XML" href="{{ route('mdfe.downloadXML', [$mdfe->id]) }}" class="text-primary"><i class="fas fa-download"></i></a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @endcomponent
        </div>
    </div>

    {{-- MODAL PARA EXCLUIR --}}
    <div class="modal fade bd-delete-modal-lg" tabindex="-1" role="dialog" aria-labelledby="modalExcluirLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExcluirLabel">Confirmar Exclusão</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-center">Tem certeza que deseja apagar esta MDFe?<br>
                    <strong class="text-danger">Esta ação não pode ser desfeita.</strong></p>
                    <form action="{{ route('mdfe.delete') }}" method="POST" id="deleteForm" class="text-center">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="mdfeId" name="mdfeId">
                    </form>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn custom-btn custom-btn-danger" onclick="document.getElementById('deleteForm').submit();">Sim, Excluir</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PARA CANCELAR --}}
    <div class="modal fade bd-cancel-modal-lg" tabindex="-1" role="dialog" aria-labelledby="modalCancelarLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCancelarLabel">Cancelar MDFe</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                     <form action="{{ route('mdfe.cancel') }}" method="POST" id="cancelForm">
                        @csrf
                        <input type="hidden" required id="mdfe_id" name="mdfe_id">
                        <div class="form-group">
                            <label for="justificativa">Justificativa para o Cancelamento</label>
                            <textarea class="form-control" required name="justificativa" id="justificativa" rows="5" placeholder="A justificativa deve ter no mínimo 15 caracteres..."></textarea>
                        </div>
                    </form>
                </div>
                 <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn custom-btn custom-btn-warning" onclick="document.getElementById('cancelForm').submit();">Confirmar Cancelamento</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PRELOADER --}}
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Aguarde...</h5>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3 mb-0">Processando sua solicitação.</p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function setaDadosModalExcluir(mdfeId) {
            document.getElementById('mdfeId').value = mdfeId;
        }

        function setaDadosModalCancelar(mdfeId) {
            document.getElementById('mdfe_id').value = mdfeId;
        }

        function loadPage() {
            var botoes = document.getElementsByTagName("a");
            for (var i = 0; i < botoes.length; i++) {
                bloquearBotao(botoes[i]);
            }

            var myModal = new bootstrap.Modal(document.getElementById('exampleModal'), {
                keyboard: false,
                backdrop: 'static'
            });
            myModal.show();
        }

        function bloquearBotao(botao) {
            botao.disabled = true;
            botao.classList.add("bloqueado");
        }
    </script>
@stop