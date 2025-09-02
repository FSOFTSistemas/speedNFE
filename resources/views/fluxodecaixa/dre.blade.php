@extends('adminlte::page')

@section('title', 'DRE - Demonstrativo de Resultado')

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
        --success-color: #28a745;
        --danger-color: #dc3545;
    }

    body {
        font-family: 'Poppins', sans-serif;
    }
    
    .card-main {
        background: var(--card-bg);
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px var(--shadow-color);
        overflow: hidden;
    }

    .card-header-filters {
        background-color: #f8f9fa;
        border-bottom: 1px solid var(--border-color);
        padding: 0;
    }
    .card-header-filters .btn-link {
        color: var(--text-dark);
        text-decoration: none;
        font-weight: 600;
        width: 100%;
        text-align: left;
        padding: 1rem 1.5rem;
    }
    
    .custom-btn {
        font-weight: 500;
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .custom-btn:hover {
        transform: translateY(-2px);
    }
    .custom-btn-primary { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; color: #fff !important; }
    .custom-btn-danger { background-color: var(--danger-color) !important; border-color: var(--danger-color) !important; color: #fff !important; }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: .5rem;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        height: 48px;
    }
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 3, 58, .25);
    }
    
    /* Estilos da Tabela DRE */
    .table-dre {
        font-size: 1.1rem;
    }
    .table-dre thead th {
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background-color: #f8f9fa;
        border-bottom-width: 2px;
    }
    .table-dre .dre-detail td:first-child {
        padding-left: 2.5rem; /* Recuo para subcategorias */
    }
    .table-dre .dre-total td {
        font-weight: 700;
        border-top: 2px solid var(--text-dark);
        border-bottom: 2px solid var(--text-dark);
    }
    .table-dre .table-success, .table-dre .table-danger, .table-dre .table-info, .table-dre .table-warning {
        font-weight: 700;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">DRE - Demonstrativo de Resultado</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right">
            <a href="{{ route('dre.pdf', ['data_inicial' => request('data_inicial'), 'data_final' => request('data_final')]) }}"
               target="_blank" class="btn custom-btn custom-btn-danger">
                <i class="fas fa-file-pdf mr-1"></i> Gerar PDF
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="card card-main">
    <div class="card-header-filters" id="headingFilters">
        <h2 class="mb-0">
            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFilters" aria-expanded="true" aria-controls="collapseFilters">
                <i class="fas fa-filter mr-2"></i> Filtros do Período
            </button>
        </h2>
    </div>

    <div id="collapseFilters" class="collapse show" aria-labelledby="headingFilters">
        <div class="card-body border-bottom">
            <form target="_blank" action="{{ route('dre.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label for="data_inicial" class="form-label">Data Inicial</label>
                        <input type="date" id="data_inicial" name="data_inicial" class="form-control" value="{{ request('data_inicial') }}" required>
                    </div>
                    <div class="col-md-5">
                        <label for="data_final" class="form-label">Data Final</label>
                        <input type="date" id="data_final" name="data_final" class="form-control" value="{{ request('data_final') }}" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn custom-btn custom-btn-primary w-100">
                            <i class="fas fa-search mr-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-hover table-dre">
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th class="text-right">Valor (R$)</th>
                </tr>
            </thead>
            <tbody>
                <tr class="table-success">
                    <td>Receita Operacional Bruta</td>
                    <td class="text-right">R$ {{ number_format($receitas, 2, ',', '.') }}</td>
                </tr>
                @foreach ($receita_plano as $rPlanos)
                    @if ($rPlanos->tipo == 'Entrada')
                        <tr class="dre-detail">
                            <td>{{ $rPlanos->descricao }}</td>
                            <td class="text-right">R$ {{ number_format($rPlanos->total, 2, ',', '.') }}</td>
                        </tr>
                    @endif
                @endforeach
                
                <tr class="table-danger">
                    <td>(-) Deduções e Despesas</td>
                    <td class="text-right">R$ {{ number_format($despesas, 2, ',', '.') }}</td>
                </tr>
                @foreach ($receita_plano as $rPlanos)
                    @if ($rPlanos->tipo == 'Saída')
                        <tr class="dre-detail">
                            <td>{{ $rPlanos->descricao }}</td>
                            <td class="text-right">R$ {{ number_format($rPlanos->total, 2, ',', '.') }}</td>
                        </tr>
                    @endif
                @endforeach
                
                <tr class="dre-total {{ $lucro < 0 ? 'table-warning' : 'table-info' }}">
                    <td>(=) Resultado Líquido</td>
                    <td class="text-right">
                        R$ {{ number_format($lucro, 2, ',', '.') }}
                        @if ($receitas > 0)
                            <span class="badge badge-{{ $lucro < 0 ? 'danger' : 'primary' }} ml-2" style="font-size: 1rem;">
                                {{ number_format(($lucro / $receitas) * 100, 2, ',', '.') }}%
                            </span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@stop