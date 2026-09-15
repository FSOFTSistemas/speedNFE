@extends('adminlte::page')

@php
    $ehRamoMotos = optional(Auth::user()->empresa)->ramo_atividade === 'motos';
@endphp

@section('title', 'Estoque')

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <div class="page-eyebrow">Estoque</div>
            <h1 class="m-0 text-dark" style="font-weight: 700;">Estoque</h1>
            <div class="page-subtitle">Visão geral dos níveis de estoque por produto</div>
        </div>
    </div>
@stop

@section('content')
    @php
        $totalProdutos = $estoques->count();
        $totalDisponiveis = $estoques->where('estoque_atual', '>', 0)->count();
        $totalSemEstoque = $totalProdutos - $totalDisponiveis;
        $totalUnidades = $estoques->sum('estoque_atual');
        $valorTotalEstoque = $estoques->sum(function ($estoque) {
            return $estoque->estoque_atual * optional($estoque->produto)->precocusto;
        });
    @endphp

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon bg-primary-soft"><i class="fas fa-boxes"></i></div>
            <div>
                <div class="stat-value">{{ $totalProdutos }}</div>
                <div class="stat-label">Produtos no estoque</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-success-soft"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="stat-value">{{ $totalDisponiveis }}</div>
                <div class="stat-label">Disponíveis</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-danger-soft"><i class="fas fa-times-circle"></i></div>
            <div>
                <div class="stat-value">{{ $totalSemEstoque }}</div>
                <div class="stat-label">Sem estoque</div>
            </div>
        </div>
        @if (Auth::user()->tipo == 'admin' || Auth::user()->cargo == 'master')
            <div class="stat-card">
                <div class="stat-icon bg-info-soft"><i class="fas fa-money-bill-wave"></i></div>
                <div>
                    <div class="stat-value">R$ {{ number_format($valorTotalEstoque, 2, ',', '.') }}</div>
                    <div class="stat-label">Valor total em estoque</div>
                </div>
            </div>
        @endif
    </div>

    @if ($ehRamoMotos)
        @php
            $filtrosAtivos = request()->hasAny(['chassi', 'modelo']);
        @endphp
        <div class="card card-main mb-4">
            <div class="filter-card-header" data-toggle="collapse" data-target="#filtrosEstoque"
                aria-expanded="{{ $filtrosAtivos ? 'true' : 'false' }}" aria-controls="filtrosEstoque">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter mr-2"></i>Filtros
                    @if ($filtrosAtivos)
                        <span class="badge badge-info filter-active-badge ml-2">Ativos</span>
                    @endif
                </h5>
                <i class="fas fa-chevron-down filter-toggle-icon"></i>
            </div>
            <div class="collapse {{ $filtrosAtivos ? 'show' : '' }}" id="filtrosEstoque">
                <div class="card-body">
                    <form action="{{ route('estoque.index') }}" method="GET" class="row align-items-end">
                        <div class="col-12 col-md-4 mb-2">
                            <label for="modelo" class="form-label">Modelo</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-motorcycle"></i></span>
                                </div>
                                <input type="text" class="form-control" id="modelo" name="modelo"
                                    placeholder="Nome do modelo" value="{{ request()->get('modelo') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-2">
                            <label for="chassi" class="form-label">Chassi</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                </div>
                                <input type="text" class="form-control" id="chassi" name="chassi"
                                    placeholder="Chassi" value="{{ request()->get('chassi') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-2 mb-2">
                            <button type="submit" class="btn custom-btn custom-btn-primary w-100">Filtrar</button>
                        </div>
                    </form>
                    @if ($filtrosAtivos)
                        <div class="mt-2 text-right">
                            <a href="{{ route('estoque.index') }}" class="text-muted"><i class="fas fa-times-circle"></i> Limpar filtros</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @php
        $colEstoque = $ehRamoMotos ? 3 : 2;
        $colAcoes = $ehRamoMotos ? 5 : 4;
    @endphp

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
                    ['responsivePriority' => 1, 'targets' => 1], // Produto
                    ['responsivePriority' => 2, 'targets' => $colEstoque], // Estoque atual
                    ['responsivePriority' => 3, 'targets' => $colAcoes]  // Ações
                ]
            ])
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th class="text-left">Produto</th>
                        @if ($ehRamoMotos)
                            <th>Chassi</th>
                        @endif
                        <th>Estoque atual</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($estoques as $estoque)
                        <tr>
                            <td><b>#{{ $estoque->id }}</b></td>
                            <td class="text-left">{{ optional($estoque->produto)->produto }}</td>
                            @if ($ehRamoMotos)
                                <td>{{ optional($estoque->produto)->chassiVeic ?? '-' }}</td>
                            @endif
                            <td><b>{{ $estoque->estoque_atual }}</b></td>
                            <td>
                                @if ($estoque->estoque_atual > 0)
                                    <span class="badge badge-success">Disponível</span>
                                @else
                                    <span class="badge badge-danger">Sem Estoque</span>
                                @endif
                            </td>
                            <td class="action-buttons">
                                <span class="d-none d-md-inline-flex">
                                    <a title="Visualizar" href="{{ route('estoque.show', [$estoque->id]) }}" class="btn btn-info btn-sm"><i class="far fa-eye"></i></a>
                                    @if (Auth::user()->tipo == "admin")
                                        <a title="Editar" href="{{ route('estoque.edit', [$estoque->id]) }}" class="btn btn-warning btn-sm"><i class="far fa-edit"></i></a>
                                    @endif
                                </span>
                                <div class="mobile-actions d-md-none">
                                    <a href="{{ route('estoque.show', [$estoque->id]) }}" class="btn btn-sm btn-outline-info"><i class="far fa-eye"></i> Visualizar</a>
                                    @if (Auth::user()->tipo == "admin")
                                        <a href="{{ route('estoque.edit', [$estoque->id]) }}" class="btn btn-sm btn-outline-warning"><i class="far fa-edit"></i> Editar</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @endcomponent
        </div>
    </div>
@stop
