@extends('adminlte::page')

@section('title', 'Produtos')

@push('css')
<style>
    /* Estilos importados da tela de clientes para consistência */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    :root {
        --primary-color: #00033a;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --text-dark: #343a40;
        --text-light: #6c757d;
        --action-edit: #28a745;
        --action-delete: #dc3545;
        --action-view: #17a2b8;
        --warning-yellow: #ffc107;
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

    /* Otimização dos botões do cabeçalho para mobile */
    .header-buttons .btn {
        display: block; /* Ocupa a largura total e força a quebra de linha */
        margin-bottom: 8px; /* Espaçamento entre os botões empilhados */
    }
    .header-buttons .btn:last-child {
        margin-bottom: 0;
    }

    @media (min-width: 992px) { /* A partir de telas grandes (lg) */
        .header-buttons .btn {
            display: inline-block; /* Volta ao comportamento inline */
            margin-bottom: 0;
            margin-left: 8px; /* Adiciona espaçamento horizontal */
        }
        .header-buttons .btn:first-child {
            margin-left: 0;
        }
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

    /* Ajuste para o nome do produto ficar à esquerda no corpo da tabela */
    .table td.product-name {
        text-align: left;
    }

    /* Estilos dos Botões de Ação */
    .action-buttons {
        white-space: nowrap; /* Garante que os botões não quebrem a linha */
    }
    .action-buttons a {
        color: var(--text-light);
        margin: 0 8px;
        font-size: 1.1rem;
        transition: color 0.3s ease;
    }
    .action-buttons a.text-edit:hover { color: var(--action-edit); }
    .action-buttons a.text-delete:hover { color: var(--action-delete); }
    .action-buttons a.text-view:hover { color: var(--action-view); }
    
    /* Modal de Exclusão */
    .modal-content {
        border-radius: 15px;
        border: none;
    }
    .modal-header {
        border-bottom: none;
        padding-bottom: 0;
    }
    .modal-body {
        padding-top: 1rem;
    }
    .delete-modal-icon {
        font-size: 3rem;
        color: var(--warning-yellow);
    }
    .btn-delete-confirm {
        background-color: var(--action-delete);
        border-color: var(--action-delete);
        color: #fff;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Produtos</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            <a class="btn custom-btn custom-btn-primary" href="{{ route('entradas.index') }}"><i class="fas fa-upload mr-1"></i> Importações</a>
            <a class="btn custom-btn custom-btn-primary" href="{{ route('categoria.index') }}"><i class="fas fa-sitemap mr-1"></i> Categorias</a>
            <a class="btn custom-btn custom-btn-primary" href="{{ route('produto.new') }}"><i class="fas fa-plus mr-1"></i> Novo Produto</a>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-main">
        <div class="card-body p-0">
             @component('components.dataTable', [
                'responsive' => [
                    'details' => [
                        'type' => 'column',
                        'target' => 0
                    ]
                ],
                'searching' => true,
                'lengthChange' => true,
                'pageLength' => 10,
                'ordering' => true,
                'showFooter' => false,
                'columnDefs' => [
                    ['className' => 'control', 'orderable' => false, 'targets' => 0],
                    ['responsivePriority' => 1, 'targets' => 1], // Prioridade alta para Nome do Produto
                    ['responsivePriority' => 2, 'targets' => -1] // Prioridade alta para Ações
                ]
            ])
                <thead class="table-light">
                    <tr>
                        <th style="width: 10px;"></th>
                        <th>PRODUTO</th>
                        <th class="d-none d-md-table-cell">PREÇO CUSTO</th>
                        <th class="d-none d-lg-table-cell">PREÇO VENDA</th>
                        <th class="d-none d-lg-table-cell">CATEGORIA</th>
                        <th>AÇÕES</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($produtos as $produto)
                        <tr>
                            <td></td>
                            <td class="product-name">{{ $produto->produto }}</td>
                            <td class="d-none d-md-table-cell">R$ {{ number_format($produto->precocusto, 2, ',', '.') }}</td>
                            <td class="d-none d-lg-table-cell">R$ {{ number_format($produto->precovenda, 2, ',', '.') }}</td>
                            <td class="d-none d-lg-table-cell">{{ $produto->descricao }}</td>
                            <td class="action-buttons">
                                <a title="Visualizar" href="{{ route('ver_produto', [$produto->id]) }}" class='text-view'><i class="far fa-eye"></i></a>
                                <a title="Editar" href="{{ route('editar_produto', ['id' => $produto->id]) }}" class='text-edit'><i class="far fa-edit"></i></a>
                                <a title="Excluir" href="#" onclick="setaDadosModal({{ $produto->id }})" class='text-delete' data-toggle="modal" data-target="#deleteModal"><i class="far fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @endcomponent
        </div>
    </div>
    
    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-triangle delete-modal-icon"></i>
                    </div>
                    <h5>Tem certeza que deseja apagar este produto?</h5>
                    <p class="text-muted">Todas as informações relacionadas a ele serão perdidas permanentemente.</p>
                    <form action="{{ route('excluir_produto') }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="idProduto" name="idProduto">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger btn-delete-confirm" onclick="document.getElementById('deleteForm').submit();">Sim, Excluir</button>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        function setaDadosModal(idProduto) {
            document.getElementById('idProduto').value = idProduto;
        }
    </script>
@endpush

