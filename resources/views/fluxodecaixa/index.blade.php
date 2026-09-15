@extends('adminlte::page')

@section('title', 'Fluxo de Caixa')

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
        --action-edit: #ffc107;
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
    .custom-btn-secondary { background-color: #6c757d !important; border-color: #6c757d !important; color: #fff !important; }
    .custom-btn-success { background-color: var(--success-color) !important; border-color: var(--success-color) !important; color: #fff !important; }
    .custom-btn-danger { background-color: var(--action-delete) !important; border-color: var(--action-delete) !important; color: #fff !important; }
    .custom-btn-warning { background-color: var(--action-edit) !important; border-color: var(--action-edit) !important; color: #212529 !important; }

    
    .header-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
    }
     @media (min-width: 992px) {
        .header-actions {
            justify-content: flex-end;
        }
    }
    .header-actions .form-inline .form-control {
        margin-bottom: 10px;
    }
     @media (min-width: 768px) {
        .header-actions .form-inline .form-control {
             margin-bottom: 0;
        }
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
    }
    .table tbody tr:hover { background-color: #f1f1f1 !important; }
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
    .action-buttons a:hover.text-warning { color: var(--action-edit) !important; }
    .action-buttons a:hover.text-danger { color: var(--action-delete) !important; }

    .modal-content { border-radius: 15px; border: none; }
    .modal-header { border-bottom: 1px solid var(--border-color); }
    .form-label { font-weight: 500; color: #495057; margin-bottom: .5rem; }
    .form-control, .form-select { border-radius: 8px; border: 1px solid var(--border-color); height: 48px; }
    .form-control:focus, .form-select:focus { border-color: #80bdff; box-shadow: 0 0 0 0.2rem rgba(0, 3, 58, .25); }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-4 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Fluxo de Caixa</h1>
        </div>
        <div class="col-lg-8 text-center text-lg-right header-actions">
            <button class="btn custom-btn custom-btn-success" data-toggle="modal" data-target="#createModal">
                <i class="fas fa-plus mr-1"></i> Novo Lançamento
            </button>
            <a href="{{ route('contas.index') }}" class="btn custom-btn custom-btn-secondary">
                <i class="fas fa-list-alt mr-1"></i> Plano de Contas
            </a>
        </div>
    </div>
@stop

@section('content')
    @php
        $filtrosAtivos = request()->hasAny(['data_inicio', 'data_fim', 'tipo', 'origem']);
    @endphp
    <div class="card card-main mb-4">
        <div class="filter-card-header" data-toggle="collapse" data-target="#filtrosFluxo"
            aria-expanded="{{ $filtrosAtivos ? 'true' : 'false' }}" aria-controls="filtrosFluxo">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter mr-2"></i>Filtros
                @if ($filtrosAtivos)
                    <span class="badge badge-info filter-active-badge ml-2">Ativos</span>
                @endif
            </h5>
            <i class="fas fa-chevron-down filter-toggle-icon"></i>
        </div>
        <div class="collapse {{ $filtrosAtivos ? 'show' : '' }}" id="filtrosFluxo">
        <div class="card-body">
            <form action="{{ route('fluxo-caixa.index') }}" method="GET" class="row align-items-end">
                <div class="col-6 col-md-2 mb-2">
                    <label for="data_inicio" class="form-label">Data Início</label>
                    <input type="date" class="form-control" id="data_inicio" name="data_inicio"
                        value="{{ request()->get('data_inicio', $dataInicio->format('Y-m-d')) }}">
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label for="data_fim" class="form-label">Data Fim</label>
                    <input type="date" class="form-control" id="data_fim" name="data_fim"
                        value="{{ request()->get('data_fim', $dataFim->format('Y-m-d')) }}">
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label for="tipo" class="form-label">Tipo</label>
                    <select class="form-control" id="tipo" name="tipo">
                        <option value="">Todos</option>
                        <option value="Entrada" @selected(request('tipo') == 'Entrada')>Entrada</option>
                        <option value="Saída" @selected(request('tipo') == 'Saída')>Saída</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label for="origem" class="form-label">Origem</label>
                    <select class="form-control" id="origem" name="origem">
                        <option value="">Todas</option>
                        <option value="Manual" @selected(request('origem') == 'Manual')>Manual</option>
                        <option value="NFe" @selected(request('origem') == 'NFe')>NFe</option>
                        <option value="NFCe" @selected(request('origem') == 'NFCe')>NFCe</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 mb-2">
                    <button type="submit" class="btn custom-btn custom-btn-primary w-100">Filtrar</button>
                </div>
            </form>
            @if ($filtrosAtivos)
                <div class="mt-2 text-right">
                    <a href="{{ route('fluxo-caixa.index') }}" class="text-muted"><i class="fas fa-times-circle"></i> Limpar filtros</a>
                </div>
            @endif
        </div>
        </div>
    </div>

<div class="card card-main">
    <div class="card-body p-0">
        @component('components.dataTable', [
            'responsive' => true,
            'searching' => true,
            'lengthChange' => true,
            'pageLength' => 25,
            'ordering' => false,
            'showFooter' => false,
            'sumColumnIndex' => 2,
        ])
            <thead class="table-light">
                <tr>
                    <th class="text-left">Descrição</th>
                    <th class="text-left">Plano de Contas</th>
                    <th>Valor</th>
                    <th>Tipo</th>
                    <th>Origem</th>
                    <th>Data</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lancamentos as $lancamento)
                    <tr>
                        <td class="text-left">{{ $lancamento->descricao }}</td>
                        <td class="text-left">{{ $lancamento->planoDeContas->descricao ?? 'Não informado' }}</td>
                        <td class="{{ $lancamento->tipo == 'Entrada' ? 'text-success' : 'text-danger' }}">
                            {{ $lancamento->tipo == 'Entrada' ? '+' : '-' }} R$ {{ number_format($lancamento->valor, 2, ',', '.') }}
                        </td>
                        <td>
                            @if ($lancamento->tipo == 'Entrada') <span class="badge badge-success">Entrada</span>
                            @else <span class="badge badge-danger">Saída</span>
                            @endif
                        </td>
                        <td>
                            @if ($lancamento->origem == 'NFe')
                                <a href="{{ route('imprimirXML', $lancamento->origem_id) }}" target="_blank" class="badge badge-primary" title="Ver NFe">NFe #{{ $lancamento->origem_id }}</a>
                            @elseif ($lancamento->origem == 'NFCe')
                                <a href="{{ route('nfce.show', $lancamento->origem_id) }}" target="_blank" class="badge badge-primary" title="Ver NFCe">NFCe #{{ $lancamento->origem_id }}</a>
                            @else
                                <span class="badge badge-secondary">Manual</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($lancamento->data)->format('d/m/Y') }}</td>
                        <td class="action-buttons">
                            @if ($lancamento->origem)
                                <span class="text-muted" title="Edição bloqueada — lançamento gerado automaticamente"><i class="fa fa-lock"></i></span>
                                <a title="Excluir" href="#" class="text-danger" onclick="openDeleteModal({{ $lancamento->id }}, true)"><i class="fa fa-trash"></i></a>
                            @else
                                <a title="Editar" href="#" class="text-warning" onclick="openEditModal({{ json_encode($lancamento) }})"><i class="fa fa-edit"></i></a>
                                <a title="Excluir" href="#" class="text-danger" onclick="openDeleteModal({{ $lancamento->id }}, false)"><i class="fa fa-trash"></i></a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endcomponent
    </div>
</div>

{{-- MODAIS (DEFINIDOS APENAS UMA VEZ FORA DO LOOP) --}}

<div class="modal fade" id="modalEditFluxo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Editar Lançamento</h5><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button></div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3"><label class="form-label">Plano de Contas</label><select class="form-control" name="plano_de_contas_id" id="edit_plano_de_contas_id" required><option value="">Selecione</option>@foreach ($planosDeContas as $plano)<option value="{{ $plano->id }}">{{ $plano->descricao }}</option>@endforeach</select></div>
                    <div class="form-group mb-3"><label class="form-label">Descrição</label><input type="text" class="form-control" name="descricao" id="edit_descricao" required></div>
                    <div class="form-group mb-3"><label class="form-label">Valor</label><input type="number" class="form-control" name="valor" step="0.01" id="edit_valor" required></div>
                    <div class="form-group mb-3"><label class="form-label">Tipo</label><select class="form-control" name="tipo" id="edit_tipo"><option value="Entrada">Entrada</option><option value="Saída">Saída</option></select></div>
                    <div class="form-group mb-3"><label class="form-label">Data</label><input type="date" class="form-control" name="data" id="edit_data" required></div>
                    <div class="text-center"><button type="submit" class="btn custom-btn custom-btn-warning">Salvar Alterações</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Apagar este Lançamento?</h5><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button></div>
            <div class="modal-body text-center">
                <p class="text-danger">Você irá excluir todas as informações sobre este lançamento!</p>
                <p class="text-danger" id="deleteWarningAutomatico" style="display: none;">
                    <strong>Atenção:</strong> este lançamento foi gerado automaticamente por uma nota. Excluí-lo aqui
                    não cancela nem estorna a nota de origem — os dois ficarão dessincronizados.
                </p>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn custom-btn custom-btn-danger">Sim, Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Novo Lançamento</h5><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button></div>
            <div class="modal-body">
                <form action="{{ route('fluxo-caixa.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3"><label class="form-label">Plano de Contas</label><select class="form-control" name="plano_de_contas_id" required><option value="">Selecione</option>@foreach ($planosDeContas as $plano)<option value="{{ $plano->id }}">{{ $plano->descricao }}</option>@endforeach</select></div>
                    <div class="form-group mb-3"><label class="form-label">Descrição</label><input type="text" class="form-control" name="descricao" required></div>
                    <div class="form-group mb-3"><label class="form-label">Valor</label><input type="number" class="form-control" name="valor" step="0.01" required></div>
                    <div class="form-group mb-3"><label class="form-label">Tipo</label><select class="form-control" name="tipo"><option value="Entrada">Entrada</option><option value="Saída">Saída</option></select></div>
                    <div class="form-group mb-3"><label class="form-label">Data</label><input type="date" class="form-control" name="data" required></div>
                    <div class="text-center"><button type="submit" class="btn custom-btn custom-btn-success">Salvar Lançamento</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    function openEditModal(lancamento) {
        // Popula o formulário do modal de edição genérico
        $('#editForm').attr('action', '/fluxo-caixa/' + lancamento.id);
        $('#edit_plano_de_contas_id').val(lancamento.plano_de_contas_id);
        $('#edit_descricao').val(lancamento.descricao);
        $('#edit_valor').val(lancamento.valor);
        $('#edit_tipo').val(lancamento.tipo);
        
        // Formata a data de 'YYYY-MM-DD HH:MM:SS' para 'YYYY-MM-DD'
        if (lancamento.data) {
            $('#edit_data').val(lancamento.data.split('T')[0]);
        }
        
        // Abre o modal
        $('#modalEditFluxo').modal('show');
    }

    function openDeleteModal(id, isAutomatico) {
        // Define a action do formulário de exclusão genérico
        $('#deleteForm').attr('action', '/fluxo-caixa/' + id);
        $('#deleteWarningAutomatico').toggle(!!isAutomatico);

        // Abre o modal
        $('#deleteModal').modal('show');
    }
</script>
@stop