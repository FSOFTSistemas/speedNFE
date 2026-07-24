@extends('adminlte::page')

@php
    $ehRamoMotos = optional(Auth::user()->empresa)->ramo_atividade === 'motos';
@endphp

@section('title', $ehRamoMotos ? 'Veículos' : 'Produtos')

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <div class="page-eyebrow">Catálogo</div>
            <h1 class="m-0 text-dark" style="font-weight: 700;">{{ $ehRamoMotos ? 'Veículos' : 'Produtos' }}</h1>
            <div class="page-subtitle">Consulte, filtre e gerencie seus {{ $ehRamoMotos ? 'veículos' : 'produtos' }} cadastrados</div>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            <a class="btn custom-btn custom-btn-primary" href="{{ route('entradas.index') }}"><i class="fas fa-upload mr-1"></i> Importações</a>
            <a class="btn custom-btn custom-btn-primary" href="{{ route('categoria.index') }}"><i class="fas fa-sitemap mr-1"></i> Categorias</a>
            <a class="btn custom-btn custom-btn-primary" href="{{ route('produto.new') }}"><i class="fas fa-plus mr-1"></i> {{ $ehRamoMotos ? 'Novo Veículo' : 'Novo Produto' }}</a>
        </div>
    </div>
@stop

@section('content')
    @php
        $filtrosAtivos = request()->hasAny(['busca', 'categoria_id', 'chassi']);
    @endphp

    <div class="card card-main mb-4">
        <div class="filter-card-header" data-toggle="collapse" data-target="#filtrosProdutos"
            aria-expanded="{{ $filtrosAtivos ? 'true' : 'false' }}" aria-controls="filtrosProdutos">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter mr-2"></i>Filtros
                @if ($filtrosAtivos)
                    <span class="badge badge-info filter-active-badge ml-2">Ativos</span>
                @endif
            </h5>
            <i class="fas fa-chevron-down filter-toggle-icon"></i>
        </div>
        <div class="collapse {{ $filtrosAtivos ? 'show' : '' }}" id="filtrosProdutos">
        <div class="card-body">
            <form action="{{ route('produto.index') }}" method="GET" class="row align-items-end">
                <div class="col-12 col-md-5 mb-2">
                    <label for="busca" class="form-label">Nome ou Código</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control" id="busca" name="busca"
                            placeholder="Nome ou código do {{ $ehRamoMotos ? 'veículo' : 'produto' }}" value="{{ request()->get('busca') }}">
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <label for="categoria_id" class="form-label">Categoria</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-sitemap"></i></span>
                        </div>
                        <select class="form-control" id="categoria_id" name="categoria_id">
                            <option value="">Todas</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}" @selected(request('categoria_id') == $categoria->id)>
                                    {{ $categoria->descricao }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if ($ehRamoMotos)
                    <div class="col-6 col-md-3 mb-2">
                        <label for="chassi" class="form-label">Chassi</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-motorcycle"></i></span>
                            </div>
                            <input type="text" class="form-control" id="chassi" name="chassi"
                                placeholder="Chassi" value="{{ request()->get('chassi') }}">
                        </div>
                    </div>
                @endif
                <div class="col-12 col-md-1 mb-2">
                    <button type="submit" class="btn custom-btn custom-btn-primary w-100">Filtrar</button>
                </div>
            </form>
            @if ($filtrosAtivos)
                <div class="mt-2 text-right">
                    <a href="{{ route('produto.index') }}" class="text-muted"><i class="fas fa-times-circle"></i> Limpar filtros</a>
                </div>
            @endif
        </div>
        </div>
    </div>

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
                        <th>{{ $ehRamoMotos ? 'MODELOS' : 'PRODUTO' }}</th>
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
                            <td class="product-name text-left">{{ $produto->produto }}</td>
                            <td class="d-none d-md-table-cell">R$ {{ number_format($produto->precocusto, 2, ',', '.') }}</td>
                            <td class="d-none d-lg-table-cell">R$ {{ number_format($produto->precovenda, 2, ',', '.') }}</td>
                            <td class="d-none d-lg-table-cell">{{ $produto->descricao }}</td>
                            <td class="action-buttons">
                                <span class="d-none d-md-inline-flex">
                                    <a title="Visualizar" href="{{ route('ver_produto', [$produto->id]) }}" class="text-primary"><i class="far fa-eye"></i></a>
                                    <a title="Editar" href="{{ route('editar_produto', ['id' => $produto->id]) }}" class="text-info"><i class="far fa-edit"></i></a>
                                    <a title="Excluir" href="#" onclick="setaDadosModal({{ $produto->id }})" class="text-danger" data-toggle="modal" data-target="#deleteModal"><i class="far fa-trash-alt"></i></a>
                                </span>
                                <div class="mobile-actions d-md-none">
                                    <a href="{{ route('ver_produto', [$produto->id]) }}" class="btn btn-sm btn-outline-primary"><i class="far fa-eye"></i> Visualizar</a>
                                    <a href="{{ route('editar_produto', ['id' => $produto->id]) }}" class="btn btn-sm btn-outline-info"><i class="far fa-edit"></i> Editar</a>
                                    <a href="#" onclick="setaDadosModal({{ $produto->id }})" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deleteModal"><i class="far fa-trash-alt"></i> Excluir</a>
                                </div>
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
                        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: var(--warning-color);"></i>
                    </div>
                    <h5>Tem certeza que deseja apagar este {{ $ehRamoMotos ? 'veículo' : 'produto' }}?</h5>
                    <p class="text-muted">Todas as informações relacionadas a ele serão perdidas permanentemente.</p>
                    <form action="{{ route('excluir_produto') }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="idProduto" name="idProduto">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="document.getElementById('deleteForm').submit();">Sim, Excluir</button>
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
