@extends('adminlte::page')

@section('title', 'DRE - Demonstrativo de Resultado')

@section('content_header')
    <h3 class="m-0 text-dark">DRE - Demonstrativo de Resultado</h3>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="m-0">Filtros</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('dre.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <label>Data Inicial</label>
                        <input type="date" name="data_inicial" class="form-control" value="{{ request('data_inicial') }}"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label>Data Final</label>
                        <input type="date" name="data_final" class="form-control" value="{{ request('data_final') }}"
                            required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela da DRE -->
    <div class="card mt-3">
        <div class="card-header bg-dark text-white">
            <h5 class="m-0">Demonstrativo de Resultado</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-secondary">
                    <tr>
                        <th>Categoria</th>
                        <th>Valor (R$)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-success">
                        <td><strong>Total de receitas</strong></td>
                        <td><strong>R$ {{ number_format($receitas, 2, ',', '.') }}</strong></td>
                        @foreach ($receita_plano as $rPlanos)
                    <tr>
                        @if ($rPlanos->tipo == 'Entrada')
                            <td><strong>{{ $rPlanos->descricao }}</strong></td>
                            <td><strong>R$ {{ number_format($rPlanos->total, 2, ',', '.') }}</strong></td>
                        @endif
                    </tr>
                    @endforeach
                    </tr>
                    <tr class="table-danger">
                        <td><strong>Total de despesas</strong></td>
                        <td><strong>R$ {{ number_format($despesas, 2, ',', '.') }}</strong></td>
                        @foreach ($receita_plano as $rPlanos)
                    <tr>
                        @if ($rPlanos->tipo == 'Saída')
                            <td><strong>{{ $rPlanos->descricao }}</strong></td>
                            <td><strong>R$ {{ number_format($rPlanos->total, 2, ',', '.') }}</strong></td>
                        @endif
                    </tr>
                    @endforeach
                    </tr>
                    <tr class="{{ $lucro < 0 ? 'table-warning' : 'table-info' }}">
                        <td><strong>Lucro Líquido</strong></td>
                        <td><strong>R$ {{ number_format($lucro, 2, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Botão para Gerar PDF -->
    <div class="text-right mt-3">
        <a href="{{ route('dre.pdf', ['data_inicial' => request('data_inicial'), 'data_final' => request('data_final')]) }}"
            class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Gerar PDF
        </a>
    </div>
@stop
