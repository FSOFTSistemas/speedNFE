@extends('adminlte::page')

@section('title', 'Relatórios de Fluxo de Caixa')

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
    .custom-btn-success { background-color: var(--success-color) !important; border-color: var(--success-color) !important; color: #fff !important; }
    
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: .5rem;
    }
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        height: 48px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 3, 58, .25);
    }
</style>
@endpush

@section('content_header')
    <h1 class="m-0 text-dark" style="font-weight: 600;">📊 Relatórios de Fluxo de Caixa</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card card-main">
                <div class="card-body">
                    <form action="{{ route('fluxo_caixa.gerarRelatorio') }}" target="_blank" method="GET">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="data_inicio" class="form-label">Data Início:</label>
                                <input type="date" id="data_inicio" name="data_inicio" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="data_fim" class="form-label">Data Fim:</label>
                                <input type="date" id="data_fim" name="data_fim" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tipo_relatorio" class="form-label">Tipo de Relatório:</label>
                                <select id="tipo_relatorio" name="tipo_relatorio" class="form-select" required>
                                    <option value="geral">📄 Relatório Geral</option>
                                    <option value="receitas_despesas">💰 Receitas vs Despesas</option>
                                    <option value="categoria">📂 Por Categoria</option>
                                    <option value="empresa">🏢 Por Empresa</option>
                                    <option value="resumo">📊 Resumo Financeiro</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-lg custom-btn custom-btn-success">
                                <i class="fas fa-file-pdf mr-2"></i> Gerar Relatório em PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection