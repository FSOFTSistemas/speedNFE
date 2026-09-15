@extends('adminlte::page')

@section('title', 'Relatórios')

@push('css')
{{-- Link original para o CSS do DataTables mantido --}}
<link href="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/cr-2.0.0/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.1/sc-2.4.1/datatables.min.css" rel="stylesheet">

<style>
    /* Estilos do Padrão Visual Definido */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    :root {
        --primary-color: #00033a;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --text-dark: #343a40;
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
    .card-header-filters .btn-link:hover {
        background-color: #e9ecef;
    }
    
    .custom-btn-primary { 
        background-color: var(--primary-color) !important; 
        border-color: var(--primary-color) !important; 
        color: #fff !important; 
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .custom-btn-primary:hover:not(:disabled) { 
        transform: translateY(-2px);
    }
    
    /* Estilo para botão desabilitado */
    .custom-btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        filter: grayscale(1);
    }

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

    /* Estilos da Tabela */
    .table thead th, .table tbody td {
        vertical-align: middle;
        text-align: center;
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
    .table td.text-left { text-align: left; }
    .dataTables_wrapper { padding: 20px; }

    .aviso-aux {
        font-size: 0.7rem;
        display: block;
        margin-top: 4px;
        color: #dc3545;
        font-weight: 500;
    }
</style>
@endpush

@php
    $ehMaster = Auth::user()->empresa_id == 1;
@endphp

@section('content_header')
    <div class="page-eyebrow">Relatórios</div>
    <h1 class="m-0 text-dark" style="font-weight: 700;">Painel de Vendas</h1>
    <div class="page-subtitle">Valor vendido, forma de pagamento, tíquete médio, produtos mais vendidos e lucro</div>
@stop

@section('content')

{{-- FILTRO DO PAINEL --}}
<div class="card card-main mb-4">
    <div class="filter-card-header" data-toggle="collapse" data-target="#filtrosPainel"
        aria-expanded="true" aria-controls="filtrosPainel">
        <h5 class="card-title mb-0"><i class="fas fa-filter mr-2"></i>Período do painel</h5>
        <i class="fas fa-chevron-down filter-toggle-icon"></i>
    </div>
    <div class="collapse show" id="filtrosPainel">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-6 col-md-3 mb-2">
                    <label for="painel_data_inicio" class="form-label">Data Início</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                        <input type="date" class="form-control" id="painel_data_inicio" value="{{ now()->subDays(30)->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <label for="painel_data_fim" class="form-label">Data Fim</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                        <input type="date" class="form-control" id="painel_data_fim" value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
                @if ($ehMaster)
                    <div class="col-6 col-md-3 mb-2">
                        <label for="painel_empresa" class="form-label">Empresa</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-building"></i></span></div>
                            <select class="form-control" id="painel_empresa">
                                <option value="1">Todas</option>
                                @foreach ($empresas as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->fantasia }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                <div class="col-6 col-md-2 mb-2">
                    <button type="button" id="btnAtualizarPainel" class="btn custom-btn custom-btn-primary w-100">Atualizar</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- KPIs --}}
<div class="stat-grid mb-4" id="painelKpis">
    <div class="stat-card">
        <div class="stat-icon bg-primary-soft"><i class="fas fa-money-bill-wave"></i></div>
        <div>
            <div class="stat-value" id="kpiValorTotal">R$ 0,00</div>
            <div class="stat-label">Valor vendido</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-info-soft"><i class="fas fa-receipt"></i></div>
        <div>
            <div class="stat-value" id="kpiQtdVendas">0</div>
            <div class="stat-label">Nº de vendas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-success-soft"><i class="fas fa-tag"></i></div>
        <div>
            <div class="stat-value" id="kpiTicketMedio">R$ 0,00</div>
            <div class="stat-label">Tíquete médio</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-warning-soft"><i class="fas fa-chart-line"></i></div>
        <div>
            <div class="stat-value" id="kpiLucroTotal">R$ 0,00</div>
            <div class="stat-label">Lucro estimado</div>
        </div>
    </div>
</div>

{{-- GRÁFICOS --}}
<div class="row mb-4">
    <div class="col-md-6 mb-4">
        <div class="card card-main" style="padding: 24px;">
            <h5 class="mb-3" style="font-weight: 600;">Valor vendido por período</h5>
            <div style="height: 300px;"><canvas id="chartVendasPeriodo"></canvas></div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card card-main" style="padding: 24px;">
            <h5 class="mb-3" style="font-weight: 600;">Forma de pagamento</h5>
            <div style="height: 300px;"><canvas id="chartFormaPagamento"></canvas></div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card card-main" style="padding: 24px;">
            <h5 class="mb-3" style="font-weight: 600;">Produtos mais vendidos</h5>
            <div style="height: 300px;"><canvas id="chartProdutos"></canvas></div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card card-main" style="padding: 24px;">
            <h5 class="mb-3" style="font-weight: 600;">Lucro vs Custo</h5>
            <div style="height: 300px;"><canvas id="chartLucroCusto"></canvas></div>
        </div>
        <small class="text-muted d-block mt-2 px-1">
            O custo usa o preço de custo <strong>atual</strong> do produto — se o custo mudou desde a venda, o lucro de períodos antigos pode não refletir o valor exato da época.
        </small>
    </div>
</div>

<div class="card card-main">
    {{-- CABEÇALHO COM FILTROS COLAPSÁVEIS --}}
    <div class="card-header-filters" id="headingOne">
        <h2 class="mb-0">
            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFilters" aria-expanded="true" aria-controls="collapseFilters">
                <i class="fas fa-filter mr-2"></i> Filtros do Relatório
            </button>
        </h2>
    </div>

    <div id="collapseFilters" class="collapse show" aria-labelledby="headingOne">
        <div class="card-body border-bottom">
            <form id="relatorioForm" method="POST" target="_blank" action="{{ route('relatorio-pdf') }}">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label for="tipo_relatorio" class="form-label">Tipo de Relatório</label>
                        <select class="form-select" id="tipo_relatorio" name="tipo_relatorio">
                            <option value="nfe">Todos</option>
                            <option value="tipoR1">Vendas Sintéticas</option>
                            <option value="tipoR2">Vendas Analíticas</option>
                            <option value="tipoR3">Vendas de Produtos</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="estado" class="form-label">Status</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="%">Todos</option>
                            <option value="Aprovado">Aprovado</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="inicio" class="form-label">Data Início</label>
                        <input type="date" class="form-control input-data" id="inicio" name="inicio">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="fim" class="form-label">Data Fim</label>
                        <input type="date" class="form-control input-data" id="fim" name="fim">
                    </div>
                    <div class="col-md-1 mb-3">
                        <button type="submit" class="btn custom-btn-primary w-100" id="btnGerarPdf" disabled title="Preencha as datas para liberar">
                            PDF <i class="fas fa-file-pdf"></i>
                        </button>
                        <span id="msgErroData" class="aviso-aux">Defina as datas</span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- CORPO DO CARD COM A TABELA DE DADOS --}}
    <div class="card-body">
        <table class="table table-hover" id="produtos">
            <thead class="table-light">
                <tr>
                    <th>Nº Nota</th>
                    <th>Data</th>
                    <th class="text-left">Cliente</th>
                    <th>Situação</th>
                    <th>Valor Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pedidos as $venda)
                <tr>
                    <td>{{ $venda->numero_nfe }}</td>
                    <td>{{ \Carbon\Carbon::parse($venda->data)->format('d/m/Y') }}</td>
                    <td class="text-left">{{ $venda->cliente }}</td>
                    <td>
                        @if ($venda->estado == 'Aprovado' || $venda->estado == 'Autorizado') <span class="badge badge-success">{{ $venda->estado }}</span>
                        @elseif ($venda->estado == 'Cancelado') <span class="badge badge-danger">{{ $venda->estado }}</span>
                        @else <span class="badge badge-warning">{{ $venda->estado }}</span>
                        @endif
                    </td>
                    <td>R$ {{ number_format($venda->total, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')
{{-- Scripts originais do DataTables mantidos --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/cr-2.0.0/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.1/sc-2.4.1/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Painel de gráficos (Valor por período, Forma de pagamento, Produtos, Lucro vs Custo)
    (function() {
        const paleta = {
            azul: '#2a78d6', aqua: '#1baf7a', amarelo: '#eda100', verde: '#008300',
            violeta: '#4a3aa7', vermelho: '#e34948', magenta: '#e87ba4', laranja: '#eb6834'
        };
        const ordemCategorica = [paleta.azul, paleta.aqua, paleta.amarelo, paleta.verde, paleta.violeta, paleta.vermelho, paleta.magenta, paleta.laranja];
        const graficos = {};

        function formatarMoeda(valor) {
            return 'R$ ' + Number(valor || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function destruirGrafico(nome) {
            if (graficos[nome]) {
                graficos[nome].destroy();
            }
        }

        function renderPainel(dados) {
            document.getElementById('kpiValorTotal').innerText = formatarMoeda(dados.kpis.valorTotal);
            document.getElementById('kpiQtdVendas').innerText = dados.kpis.qtdVendas;
            document.getElementById('kpiTicketMedio').innerText = formatarMoeda(dados.kpis.ticketMedio);
            document.getElementById('kpiLucroTotal').innerText = formatarMoeda(dados.kpis.lucroTotal);

            destruirGrafico('vendasPeriodo');
            graficos.vendasPeriodo = new Chart(document.getElementById('chartVendasPeriodo').getContext('2d'), {
                type: 'line',
                data: {
                    labels: dados.vendasPorPeriodo.map(i => i.periodo),
                    datasets: [{
                        label: 'Valor vendido',
                        data: dados.vendasPorPeriodo.map(i => i.valor),
                        borderColor: paleta.azul,
                        backgroundColor: paleta.azul + '33',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            destruirGrafico('formaPagamento');
            graficos.formaPagamento = new Chart(document.getElementById('chartFormaPagamento').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: dados.formaPagamento.map(i => i.forma),
                    datasets: [{
                        label: 'Total',
                        data: dados.formaPagamento.map(i => i.total),
                        backgroundColor: dados.formaPagamento.map((_, idx) => ordemCategorica[idx % ordemCategorica.length]),
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true } }
                }
            });

            destruirGrafico('produtos');
            graficos.produtos = new Chart(document.getElementById('chartProdutos').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: dados.produtosMaisVendidos.map(i => i.nome),
                    datasets: [{
                        label: 'Quantidade vendida',
                        data: dados.produtosMaisVendidos.map(i => i.quantidade),
                        backgroundColor: paleta.azul,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true } }
                }
            });

            destruirGrafico('lucroCusto');
            graficos.lucroCusto = new Chart(document.getElementById('chartLucroCusto').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: dados.lucroVsCusto.map(i => i.periodo),
                    datasets: [
                        { label: 'Lucro', data: dados.lucroVsCusto.map(i => i.lucro), backgroundColor: paleta.azul },
                        { label: 'Custo', data: dados.lucroVsCusto.map(i => i.custo), backgroundColor: paleta.aqua },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: true, position: 'top' } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        window.buscarDadosPainelRelatorios = function() {
            const params = new URLSearchParams({
                data_inicio: document.getElementById('painel_data_inicio').value,
                data_fim: document.getElementById('painel_data_fim').value,
            });
            const empresaSelect = document.getElementById('painel_empresa');
            if (empresaSelect) {
                params.append('empresa', empresaSelect.value);
            }

            fetch('{{ route('relatorios.dashboard-data') }}?' + params.toString())
                .then(response => response.json())
                .then(renderPainel)
                .catch(error => console.error('Erro ao carregar painel de relatórios:', error));
        };
    })();
</script>
<script>
    $(document).ready(function() {
        // DataTable original
        $('#produtos').DataTable({
            responsive: { details: true },
            language: { "url": 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json' },
            layout: {
                topStart: {
                    buttons: [
                        { extend: 'copyHtml5', text: '<i class="fa fa-clone text-secondary"></i>', titleAttr: 'Copiar' },
                        { extend: 'excelHtml5', text: '<i class="fa fa-file-excel text-success"></i>', titleAttr: 'Excel', title: 'Relatorio' },
                        { extend: 'pdfHtml5', text: '<i class="fa fa-file-pdf text-danger"></i>', titleAttr: 'PDF', title: 'Relatorio' }
                    ]
                }
            }
        });

        // Lógica de Trava do Botão
        const btnPdf = $('#btnGerarPdf');
        const inputsData = $('.input-data');
        const msgErro = $('#msgErroData');

        function validarCampos() {
            let todosPreenchidos = true;
            inputsData.each(function() {
                if ($(this).val() === "") {
                    todosPreenchidos = false;
                }
            });

            if (todosPreenchidos) {
                btnPdf.prop('disabled', false);
                msgErro.fadeOut();
            } else {
                btnPdf.prop('disabled', true);
                msgErro.fadeIn();
            }
        }

        // Monitora mudanças nos campos de data
        inputsData.on('change', validarCampos);

        // Painel de gráficos
        document.getElementById('btnAtualizarPainel').addEventListener('click', window.buscarDadosPainelRelatorios);
        window.buscarDadosPainelRelatorios();
    });
</script>
@endsection