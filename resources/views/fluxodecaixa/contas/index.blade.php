@extends('adminlte::page')

@section('title', 'Plano de Contas')

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
    .custom-btn-success { background-color: var(--success-color) !important; border-color: var(--success-color) !important; color: #fff !important; }
    .custom-btn-danger { background-color: var(--action-delete) !important; border-color: var(--action-delete) !important; color: #fff !important; }
    .custom-btn-warning { background-color: var(--action-edit) !important; border-color: var(--action-edit) !important; color: #212529 !important; }

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
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Plano de Contas</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            <button class="btn custom-btn custom-btn-success" data-toggle="modal" data-target="#modalCreate">
                <i class="fas fa-plus mr-1"></i> Nova Conta
            </button>
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
                    <th>ID</th>
                    <th>Código</th>
                    <th class="text-left">Descrição</th>
                    <th>Tipo</th>
                    <th class="text-left">Conta Pai</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contas as $conta)
                    <tr>
                        <td>{{ $conta->id }}</td>
                        <td>{{ $conta->codigo }}</td>
                        <td class="text-left">{{ $conta->descricao }}</td>
                        <td>
                            @if ($conta->tipo == 'Sintética') <span class="badge badge-primary">Sintética</span>
                            @else <span class="badge badge-info">Analítica</span>
                            @endif
                        </td>
                        <td class="text-left">{{ $conta->contaPai->descricao ?? '-' }}</td>
                        <td class="action-buttons">
                            <a href="#" class="text-warning" title="Editar" onclick="openEditModal({{ json_encode($conta) }})">
                                <i class="far fa-edit"></i>
                            </a>
                            <a href="#" class="text-danger" title="Excluir" onclick="openDeleteModal({{ $conta->id }}, '{{ $conta->descricao }}')">
                                <i class="far fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endcomponent
    </div>
</div>

<div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Nova Conta</h5><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button></div>
            <div class="modal-body">
                <form action="{{ route('contas.store') }}" method="POST">
                    @csrf
                    {{-- O conteúdo do seu modal de criação original (assumindo a estrutura) --}}
                    <div class="form-group mb-3"><label class="form-label">Descrição</label><input type="text" class="form-control" name="descricao" required></div>
                    <div class="form-group mb-3"><label class="form-label">Código</label><input type="text" class="form-control" name="codigo" required></div>
                    <div class="form-group mb-3"><label class="form-label">Tipo</label><select class="form-control" name="tipo"><option value="Sintética">Sintética</option><option value="Analítica">Analítica</option></select></div>
                    <div class="form-group mb-3"><label class="form-label">Conta Pai</label><select class="form-control" name="conta_pai_id"><option value="">Nenhuma</option>@foreach($contas->where('tipo', 'Sintética') as $contaPai)<option value="{{ $contaPai->id }}">{{ $contaPai->descricao }}</option>@endforeach</select></div>
                    <div class="text-center mt-4"><button type="submit" class="btn custom-btn custom-btn-success">Salvar Nova Conta</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Editar Conta</h5><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button></div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3"><label class="form-label">Descrição</label><input type="text" class="form-control" name="descricao" id="edit_descricao" required></div>
                    <div class="form-group mb-3"><label class="form-label">Código</label><input type="text" class="form-control" name="codigo" id="edit_codigo" required></div>
                    <div class="form-group mb-3"><label class="form-label">Tipo</label><select class="form-control" name="tipo" id="edit_tipo"><option value="Sintética">Sintética</option><option value="Analítica">Analítica</option></select></div>
                    <div class="form-group mb-3"><label class="form-label">Conta Pai</label><select class="form-control" name="conta_pai_id" id="edit_conta_pai_id"><option value="">Nenhuma</option>@foreach($contas->where('tipo', 'Sintética') as $contaPai)<option value="{{ $contaPai->id }}">{{ $contaPai->descricao }}</option>@endforeach</select></div>
                    <div class="text-center mt-4"><button type="submit" class="btn custom-btn custom-btn-warning">Salvar Alterações</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Confirmar Exclusão</h5><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button></div>
            <div class="modal-body text-center">
                <p>Tem certeza que deseja apagar a conta <strong id="deleteContaDescricao"></strong>?</p>
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
@stop

@section('js')
<script>
    function openEditModal(conta) {
        // Define a action do formulário
        $('#editForm').attr('action', '/contas/' + conta.id);

        // Preenche os campos do formulário
        $('#edit_descricao').val(conta.descricao);
        $('#edit_codigo').val(conta.codigo);
        $('#edit_tipo').val(conta.tipo);
        $('#edit_conta_pai_id').val(conta.conta_pai_id);
        
        // Abre o modal
        $('#modalEdit').modal('show');
    }

    function openDeleteModal(id, descricao) {
        // Define a action do formulário
        $('#deleteForm').attr('action', '/contas/' + id);

        // Preenche o nome da conta para confirmação
        $('#deleteContaDescricao').text(descricao);

        // Abre o modal
        $('#modalDelete').modal('show');
    }
</script>
@stop