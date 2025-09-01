@extends('adminlte::page')

@section('title', 'Empresas')

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
    .custom-btn-info { background-color: var(--info-color) !important; border-color: var(--info-color) !important; color: #fff !important; }

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
    .action-buttons a:hover.text-success { color: var(--success-color) !important; }
    .action-buttons a:hover.text-primary { color: var(--info-color) !important; }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Empresas</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            <a class="btn custom-btn custom-btn-primary" href="{{ route('empresa.create') }}"><i class="fas fa-plus mr-1"></i> Nova Empresa</a>
            <a class="btn custom-btn custom-btn-info" href="{{ route('index_usuario') }}"><i class="fas fa-users mr-1"></i> Usuários</a>
            <a class="btn custom-btn custom-btn-secondary" href="{{ route('empresa.index') }}"><i class="fas fa-sync-alt mr-1"></i> Voltar</a>
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
                    <th class="text-left">Razão Social</th>
                    <th>CPF ou CNPJ</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($empresas as $empresa)
                    <tr>
                        <td><b>#{{ $empresa->id }}</b></td>
                        <td class="text-left">{{ $empresa->fantasia }}</td>
                        <td>{{ $empresa->cpf_cnpj }}</td>
                        <td class="action-buttons">
                            @if ($empresa->status == 1)
                                <a title="Desativar" class="text-danger" href="{{ route('desativarReativar_empresa', ['id' => $empresa->id]) }}">
                                    <i class="fas fa-ban"></i>
                                </a>
                            @else
                                <a title="Ativar" class="text-success" href="{{ route('desativarReativar_empresa', ['id' => $empresa->id]) }}">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                            @endif
                            <a title="Editar" href="{{ route('editar_empresa', [$empresa->id]) }}" class="text-teal">
                                <i class="far fa-edit"></i>
                            </a>
                            <a title="Visualizar" href="{{ route('empresa.view', [$empresa->id]) }}" class="text-primary">
                                <i class="far fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endcomponent
    </div>
</div>
@stop

@section('js')
    <script>
        // Função original mantida para garantir a compatibilidade
        function setaDadosModal(idCliente) {
            document.getElementById('idCliente').value = idCliente;
        }
    </script>
@stop