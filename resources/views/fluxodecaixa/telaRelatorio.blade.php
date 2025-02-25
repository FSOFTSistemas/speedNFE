@extends('adminlte::page')

@section('title', 'Relatórios de Fluxo de Caixa')

@section('content_header')
    <h1 class="m-0 text-dark">📊 Relatórios de Fluxo de Caixa</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Filtrar Relatório</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('fluxo_caixa.gerarRelatorio') }}" target="_blank" method="GET">
                @csrf
                <div class="row">
                    <!-- Data Início -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Data Início:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="date" name="data_inicio" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <!-- Data Fim -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Data Fim:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="date" name="data_fim" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <!-- Tipo de Relatório -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tipo de Relatório:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-filter"></i></span>
                                </div>
                                <select name="tipo_relatorio" class="form-control" required>
                                    <option value="geral">📄 Relatório Geral</option>
                                    <option value="receitas_despesas">💰 Receitas vs Despesas</option>
                                    <option value="categoria">📂 Por Categoria</option>
                                    <option value="empresa">🏢 Por Empresa</option>
                                    <option value="resumo">📊 Resumo Financeiro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botão de Gerar Relatório -->
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-file-pdf"></i> Gerar PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
