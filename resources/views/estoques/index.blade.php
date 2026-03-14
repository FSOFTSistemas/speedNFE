@extends('adminlte::page')

@section('title', 'Estoque')

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
        --action-edit: #ffc107;
        --action-view: #17a2b8;
        --status-available: #28a745;
        --status-unavailable: #dc3545;
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
    .table td.product-name {
        text-align: left; /* Alinha o nome do produto à esquerda */
    }

    /* Estilos dos Botões de Ação */
    .action-buttons {
        white-space: nowrap;
    }
    .action-buttons a {
        color: var(--text-light);
        margin: 0 8px;
        font-size: 1.2rem;
        transition: color 0.3s ease;
    }
    .action-buttons a.text-edit:hover { color: var(--action-edit); }
    .action-buttons a.text-view:hover { color: var(--action-view); }

</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Estoque</h1>
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
                'pageLength' => 100,
                'ordering' => true,
                'showFooter' => false,
                 'columnDefs' => [
                    ['responsivePriority' => 1, 'targets' => 1], // Nome do Produto
                    ['responsivePriority' => 2, 'targets' => 3], // Estoque
                    ['responsivePriority' => 3, 'targets' => 4]  // Ações
                ]
            ])
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th style="text-align: left;">PRODUTO</th>
                        <th>STATUS</th>
                        <th>ESTOQUE</th>
                        <th>AÇÕES</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($estoques as $estoque)
                        <tr>
                            <td><b>#{{ $estoque->id }}</b></td>
                            <td class="product-name">{{ $estoque->produto->produto }}</td>
                            <td>
                                @if ($estoque->estoque_atual > 0)
                                    <span class="badge badge-success">Disponível</span>
                                @else
                                    <span class="badge badge-danger">Sem Estoque</span>
                                @endif
                            </td>
                            <td>{{ $estoque->estoque_atual }}</td>
                            <td class="action-buttons">
                                <a title="Visualizar" href="{{ route('estoque.show', [$estoque->id]) }}" class="text-view"><i class="far fa-eye"></i></a>
                                @if (Auth::user()->tipo == "admin")
                                <a title="Editar" href="{{ route('estoque.edit', [$estoque->id]) }}" class="text-edit"><i class="far fa-edit"></i></a>
                                @endif
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
