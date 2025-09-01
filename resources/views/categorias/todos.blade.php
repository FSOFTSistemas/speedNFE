@extends('adminlte::page')

@section('title', 'Categorias de Produtos')

@push('css')
<style>
    /* Estilos importados para consistência */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    :root {
        --primary-color: #00033a;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --text-dark: #343a40;
        --text-light: #6c757d;
        --status-active: #28a745;
        --status-inactive: #6c757d;
        --action-delete: #dc3545;
        --action-activate: #28a745;
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
    .custom-btn-primary {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        color: #fff !important;
    }
    .custom-btn-primary:hover {
        background-color: #00045e !important;
        border-color: #00045e !important;
    }
    .custom-btn-secondary {
        background-color: var(--text-light) !important;
        border-color: var(--text-light) !important;
        color: #fff !important;
    }
    .custom-btn-secondary:hover {
        background-color: #5a6268 !important;
        border-color: #545b62 !important;
    }
    
    /* Estilos da Tabela */
    .table thead th, .table tbody td {
        background-color: transparent !important;
        vertical-align: middle;
        text-align: center; /* Centraliza todo o texto por padrão */
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
    .table td.description-name {
        text-align: left; /* Alinha a descrição à esquerda */
    }

    /* Estilos dos Botões de Ação */
    .action-buttons a {
        color: var(--text-light);
        margin: 0 8px;
        font-size: 1.2rem;
        transition: color 0.3s ease;
    }
    .action-buttons a:hover.text-danger { color: var(--action-delete) !important; }
    .action-buttons a:hover.text-success { color: var(--action-activate) !important; }

</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-md-6 text-center text-md-left mb-3 mb-md-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Categorias</h1>
        </div>
        <div class="col-md-6 text-center text-md-right">
            <a class="btn custom-btn custom-btn-primary" href="{{ route('cadastrar_categoria') }}"><i class="fas fa-plus mr-1"></i> Nova Categoria</a>
            <a class="btn custom-btn custom-btn-secondary ml-2" href="{{ route('produto.index') }}">Voltar para Produtos</a>
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
                        <th style="text-align: left;">DESCRIÇÃO</th>
                        <th>STATUS</th>
                        @if ($empresa == 1)
                            <th>EMPRESA</th>
                        @endif
                        <th>ATIVAR/DESATIVAR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categorias as $categoria)
                        <tr>
                            <td class="description-name">{{ $categoria->descricao }}</td>
                            <td>
                                @if ($categoria->status == 1)
                                    <span class="badge badge-success">Ativa</span>
                                @else
                                    <span class="badge badge-secondary">Inativa</span>
                                @endif
                            </td>
                            @if ($empresa == 1)
                                <td>{{ $categoria->fantasia }}</td>
                            @endif
                            <td class="action-buttons">
                                <a href="{{ route('desativarReativar_categoria', ['id' => $categoria->id]) }}">
                                    @if ($categoria->status == 1)
                                        <i title="Desativar" class="fa fa-ban text-danger"></i>
                                    @else
                                        <i title="Reativar" class="fa fa-check text-success"></i>
                                    @endif
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
        // Scripts específicos da página, se necessário
    </script>
@stop
