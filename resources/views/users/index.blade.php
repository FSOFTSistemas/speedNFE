@extends('adminlte::page')

@section('title', 'Usuários')

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
        --info-color: #17a2b8;
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
    .action-buttons a:hover.text-warning, .action-buttons a:hover.text-teal { color: var(--action-edit) !important; }
    .action-buttons a:hover.text-danger { color: var(--action-delete) !important; }

    .modal-content { border-radius: 15px; border: none; }
    .modal-header { border-bottom: 1px solid var(--border-color); }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Usuários</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            <a href="{{ route('usuario.create') }}" class='btn custom-btn custom-btn-primary'><i class="fas fa-plus mr-1"></i> Novo Usuário</a>
            <a href="{{ route('empresa.index') }}" class="btn custom-btn custom-btn-secondary">Voltar</a>
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
                    <th class="text-left">Login</th>
                    <th>Cargo</th>
                    <th class="text-left">Empresa</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td><b>#{{ $user->id }}</b></td>
                        <td class="text-left">{{ $user->email }}</td>
                        <td>
                            @if($user->cargo == 'master') <span class="badge badge-danger">Master</span>
                            @elseif($user->cargo == 'admin') <span class="badge badge-primary">Admin</span>
                            @else <span class="badge badge-secondary">{{ $user->cargo }}</span>
                            @endif
                        </td>
                        <td class="text-left">{{ $user->fantasia }}</td>
                        <td class="action-buttons">
                            <a title="Editar" href="{{ route('editar_usuario', ['id' => $user->id]) }}" class="text-teal"><i class="far fa-edit"></i></a>
                            <a title="Deletar" href="#" data-toggle="modal" data-target="#modalExcluirUsuario" onclick="setaDadosModal({{ $user->id }}, '{{ $user->email }}')" class="text-danger"><i class="far fa-trash-alt"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endcomponent
    </div>
</div>

@component('components.modal', [
    'modalId' => 'modalExcluirUsuario',
    'modalTitle' => 'Excluir Usuário',
    'sizeModal' => 'modal-md',
])
    <form id="deleteForm" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" name="userId" id="userId">
        <div class="modal-body text-center">
            <p>Tem certeza que deseja apagar o usuário <br> <strong id="userEmail" class="text-danger"></strong>?</p>
            <p class="text-danger mt-3"><b>Atenção:</b> Esta ação é irreversível!</p>
        </div>
        <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button class="btn custom-btn custom-btn-danger" type="submit">Sim, Excluir</button>
        </div>
    </form>
@endcomponent
@stop

@section('js')
    <script>
        // Função aprimorada para exibir o e-mail no modal
        function setaDadosModal(userId, userEmail) {
            // Define a action correta para o formulário
            let form = document.getElementById('deleteForm');
            form.action = '/usuarios/' + userId; // Ajuste a rota conforme necessário
            
            // Seta o ID no input hidden
            document.getElementById('userId').value = userId;

            // Seta o email do usuário no corpo do modal para confirmação
            document.getElementById('userEmail').textContent = userEmail;
        }
    </script>
@stop