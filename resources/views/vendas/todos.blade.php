@extends('adminlte::page')

@section('title', 'Resumo de Notas NFe')

@push('css')
<style>
    /* Estilos do Padrão Visual Definido */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    :root {
        --primary-color: #00033a;
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
        --success-color: #28a745;
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
    .custom-btn-warning { background-color: var(--warning-color) !important; border-color: var(--warning-color) !important; color: #000 !important; }
    .custom-btn-danger { background-color: var(--action-delete) !important; border-color: var(--action-delete) !important; color: #fff !important; }
    
    .header-buttons .btn { display: block; margin-bottom: 8px; }
    .header-buttons .btn:last-child { margin-bottom: 0; }
    @media (min-width: 992px) {
        .header-buttons .btn { display: inline-block; margin-bottom: 0; margin-left: 8px; }
    }
    
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
    .table td.text-left { text-align: left; }

    .action-buttons {
        white-space: nowrap;
        text-align: right;
    }
    .action-buttons a {
        color: var(--text-light);
        margin: 0 8px;
        font-size: 1.2rem;
        transition: color 0.3s ease;
    }
    .action-buttons a:hover.text-danger { color: var(--action-delete) !important; }
    .action-buttons a:hover.text-success { color: var(--action-send) !important; }
    .action-buttons a:hover.text-primary { color: var(--action-view) !important; }
    .action-buttons a:hover.text-info { color: var(--info-color) !important; }
    .action-buttons a:hover.text-warning { color: var(--warning-color) !important; }

    .modal-content { border-radius: 15px; border: none; }
    .modal-header { border-bottom: 1px solid #f0f0f0; }
    .form-label { font-weight: 500; color: #495057; margin-bottom: .5rem; }
    .form-control { border-radius: 8px; border: 1px solid var(--border-color); height: 48px; }
    .form-control:focus { border-color: #80bdff; box-shadow: 0 0 0 0.2rem rgba(0, 3, 58, .25); }

    .bloqueado { opacity: 0.5; pointer-events: none; }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Resumo de Notas NFe</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            <a href="/vendas/nova" class="btn custom-btn custom-btn-primary">
                <i class="fas fa-plus mr-1"></i> Nova NFe
            </a>
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
                'ordering' => false,
                'showFooter' => false,
            ])
                <thead class="table-light">
                    <tr>
                        <th width="5%">Nº</th>
                        <th width="30%" class="text-left">Cliente</th>
                        <th width="10%">Valor</th>
                        <th width="10%">Data</th>
                        <th width="10%">Estado</th>
                        @if ($empresa == 1)
                            <th width="15%" class="text-left">Empresa</th>
                        @endif
                        <th width="20%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedidos as $index => $pedido)
                        <tr>
                            <td>{{ $pedido->numero_nfe }}</td>
                            <td class="text-left">{{ $pedido->nome }}</td>
                            <td>R${{ number_format($pedido->total, 2, ',', '.') }}</td>
                            <td>{{ date('d/m/Y', strtotime($pedido->data)) }}</td>
                            <td>
                                @if ($pedido->estado == 'Pendente') <span class="badge badge-warning">{{ $pedido->estado }}</span>
                                @elseif ($pedido->estado == 'Autorizado') <span class="badge badge-success">{{ $pedido->estado }}</span>
                                @elseif ($pedido->estado == 'Cancelado') <span class="badge badge-danger">{{ $pedido->estado }}</span>
                                @else <span class="badge badge-secondary">{{ $pedido->estado }}</span>
                                @endif
                            </td>
                            @if ($empresa == 1)
                                <td class="text-left">{{ $pedido->fantasia }}</td>
                            @endif
                            <td class="action-buttons">
                                <a href="{{ url('/debug-xml/' . $pedido->id) }}" target="_blank" class="btn btn-sm btn-info" title="Ver XML (Debug)">
    <i class="fas fa-eye"></i> XML
</a>
                                @if ($pedido->estado == 'Pendente' || $pedido->estado == 'Rejeitado')
                                    <a target="_blank" href="{{ route('vendas.show', [$pedido->id]) }}" title="Visualizar" class="text-primary"><i class="fa fa-eye"></i></a>
                                    <a href="{{ route('vendas.editar', [$pedido->id]) }}" title="Editar" class="text-info"><i class="fa fa-edit"></i></a>
                                    <a href="#" title="Excluir" onclick="setaDadosExcluir({{ $pedido->id }});" class="text-danger" data-toggle="modal" data-target="#excluirModal"><i class="fa fa-trash"></i></a>
                                    <a href="{{ route('enviarXML', ['id' => $pedido->id]) }}" onclick="loadPage()" title="Enviar NFe" class="text-success"><i class="fas fa-upload"></i></a>
                                @elseif($pedido->estado == 'Autorizado')
                                    <a target="_blank" href="{{ route('imprimirXML', [$pedido->id]) }}" title="Imprimir" class="text-primary"><i class="fa fa-print"></i></a>
                                    <a href="#" title="Carta de Correção" onclick="openCceModal({{ $pedido->id }})" class="text-warning" data-toggle="modal" data-target="#cceModal"><i class="fas fa-file-alt"></i></a>
                                    @if ($pedido->sequencia_evento > 0)
                                        <a target='_blank' title="Imprimir CCe" href="/venda/cce/{{ $pedido->id }}" class="text-dark"><i class="fa fa-print"></i></a>
                                    @endif
                                    <a href="#" title="Cancelar" onclick="openCancelModal({{ $pedido->id }})" class="text-danger" data-toggle="modal" data-target="#cancelarModal"><i class="fa fa-ban"></i></a>
                                @else
                                    <a target='_blank' title="Imprimir Cancelamento" href="{{ route('imprimirCancelamentoXML', ['id' => $pedido->id]) }}" class="text-dark"><i class="fa fa-print"></i></a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @endcomponent
        </div>
    </div>

    <div class="modal fade" id="cceModal" tabindex="-1" role="dialog" aria-labelledby="cceModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('cartaCorrecao') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="cceModalLabel">Justificativa de Carta de Correção</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="venda_id_cce" id="venda_id_cce">
                        <div class="form-group">
                            <label for="justificativa_cce" class="form-label">Justificativa (mínimo 15 caracteres)</label>
                            <textarea name="justificativa" id="justificativa_cce" class="form-control" rows="6" required minlength="15"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="submit" onclick="loadPage()" class="btn custom-btn custom-btn-primary">Enviar CCe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancelarModal" tabindex="-1" role="dialog" aria-labelledby="cancelarModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('cancelar') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelarModalLabel">Justificativa de Cancelamento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="venda_id_cancelar" id="venda_id_cancelar">
                        <div class="form-group">
                            <label for="justificativa_cancelar" class="form-label">Justificativa (mínimo 15 caracteres)</label>
                            <textarea class="form-control" name="justificativa" id="justificativa_cancelar" rows="6" required minlength="15"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="submit" onclick="loadPage()" class="btn custom-btn custom-btn-danger">Confirmar Cancelamento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="excluirModal" tabindex="-1" role="dialog" aria-labelledby="excluirModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('pedido.deletar') }}" method="post">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title" id="excluirModalLabel">Deletar Pedido</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <h5 class="text-danger text-center">Tem certeza que deseja deletar o pedido?<br>Isso irá excluir todas as informações sobre o mesmo!</h5>
                        <input type="hidden" name="pedido_id" id="pedido_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="submit" onclick="loadPage()" class="btn custom-btn custom-btn-warning">Deletar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="load" tabindex="-1" aria-labelledby="loadLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="loadLabel">Aguarde...</h5></div>
                <div class="modal-body text-center p-4">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"><span class="sr-only">Loading...</span></div>
                    <p class="mt-3 mb-0">Processando sua solicitação.</p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function setaDadosExcluir(id) {
            document.getElementById('pedido_id').value = id;
        }
        function openCceModal(id) {
            document.getElementById('venda_id_cce').value = id;
        }
        function openCancelModal(id) {
            document.getElementById('venda_id_cancelar').value = id;
        }

        function loadPage() {
            var myModal = new bootstrap.Modal(document.getElementById('load'), {
                keyboard: false,
                backdrop: 'static'
            });
            myModal.show();
        }
    </script>
@stop