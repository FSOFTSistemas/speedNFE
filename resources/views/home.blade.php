@extends('adminlte::page')

@section('title', 'Dashboard')

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@php
    $agora = now()->locale('pt_BR');
    $hora = (int) $agora->format('H');
    $saudacao = $hora < 12 ? 'Bom dia' : ($hora < 18 ? 'Boa tarde' : 'Boa noite');
    $primeiroNome = explode(' ', trim(auth()->user()->name))[0];

    $mostrarNotas = !auth()->user()->can('client-NFCe') || !auth()->user()->can('client-MDFe') || !auth()->user()->can('client-CTe') || !auth()->user()->can('client-advanced3');
    $mostrarEstoque = !auth()->user()->can('client-MDFe') || !auth()->user()->can('client-CTe') || !auth()->user()->can('client-advanced3');

    $kpis = [
        ['icone' => 'fas fa-money-bill-wave', 'cor' => 'primary', 'label' => 'Faturamento do mês', 'valor' => 'R$ ' . number_format($resumoMes['valorTotal'], 2, ',', '.'), 'variacao' => $resumoMes['variacaoValorTotal']],
        ['icone' => 'fas fa-receipt', 'cor' => 'info', 'label' => 'Vendas (NFe + NFCe)', 'valor' => $resumoMes['qtdVendas'], 'variacao' => $resumoMes['variacaoQtdVendas']],
        ['icone' => 'fas fa-tag', 'cor' => 'success', 'label' => 'Tíquete médio', 'valor' => 'R$ ' . number_format($resumoMes['ticketMedio'], 2, ',', '.'), 'variacao' => $resumoMes['variacaoTicketMedio']],
        ['icone' => 'fas fa-chart-line', 'cor' => 'warning', 'label' => 'Lucro estimado', 'valor' => 'R$ ' . number_format($resumoMes['lucroTotal'], 2, ',', '.'), 'variacao' => $resumoMes['variacaoLucroTotal']],
    ];
@endphp

@section('content')
    {{-- Boas-vindas --}}
    <div class="home-hero">
        <div>
            <div class="home-hero-date">{{ $agora->translatedFormat('l, d \d\e F \d\e Y') }}</div>
            <h1>{{ $saudacao }}, {{ $primeiroNome }}!</h1>
            <p>Veja como o negócio está indo este mês e o que precisa da sua atenção.</p>
        </div>
        <div class="home-hero-actions">
            @if ($mostrarNotas)
                <a href="/vendas/nova" class="btn btn-light"><i class="fas fa-plus mr-1"></i> Emitir NFe</a>
            @endif
            <a href="/relatorios" class="btn btn-outline-light"><i class="fas fa-chart-pie mr-1"></i> Relatórios</a>
        </div>
    </div>

    <div class="alert home-announcement fade show" role="alert">
        <span class="stat-icon bg-info-soft"><i class="fas fa-bell"></i></span>
        <div>
            <strong class="title">Novidade no sistema!</strong>
            <p>
                Agora você pode fazer o <strong>pagamento online do plano</strong> e acompanhar suas
                <strong>faturas em tempo real</strong>.
                <a href="{{ route('faturas.index') }}" class="font-weight-bold">Conferir <i class="fas fa-arrow-right"></i></a>
            </p>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    {{-- KPIs do mês, com variação vs mês anterior --}}
    <div class="home-section-title">
        <h5>Resumo do mês</h5>
        <small>comparado ao mês anterior</small>
    </div>
    <div class="stat-grid">
        @foreach ($kpis as $kpi)
            <div class="stat-card">
                <div class="stat-icon bg-{{ $kpi['cor'] }}-soft"><i class="{{ $kpi['icone'] }}"></i></div>
                <div class="stat-body">
                    <div class="stat-label">{{ $kpi['label'] }}</div>
                    <div class="stat-value" title="{{ $kpi['valor'] }}">{{ $kpi['valor'] }}</div>
                    <div class="stat-trend">
                        @if ($kpi['variacao'] > 0)
                            <span class="up"><i class="fas fa-arrow-up"></i> {{ abs($kpi['variacao']) }}%</span>
                        @elseif ($kpi['variacao'] < 0)
                            <span class="down"><i class="fas fa-arrow-down"></i> {{ abs($kpi['variacao']) }}%</span>
                        @else
                            <span><i class="fas fa-minus"></i> estável</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Caixa e alertas operacionais --}}
    <div class="home-section-title">
        <h5>Caixa e pendências</h5>
        <a href="{{ route('fluxo-caixa.index') }}">Ver fluxo de caixa <i class="fas fa-arrow-right ml-1"></i></a>
    </div>
    <div class="stat-grid">
        <a href="{{ route('fluxo-caixa.index') }}" class="stat-card">
            <div class="stat-icon bg-success-soft"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-body">
                <div class="stat-label">Entradas no mês</div>
                <div class="stat-value">R$ {{ number_format($fluxoCaixaMes['entradas'], 2, ',', '.') }}</div>
            </div>
        </a>
        <a href="{{ route('fluxo-caixa.index') }}" class="stat-card">
            <div class="stat-icon bg-danger-soft"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-body">
                <div class="stat-label">Saídas no mês</div>
                <div class="stat-value">R$ {{ number_format($fluxoCaixaMes['saidas'], 2, ',', '.') }}</div>
            </div>
        </a>
        <a href="{{ route('fluxo-caixa.index') }}" class="stat-card">
            <div class="stat-icon {{ $fluxoCaixaMes['saldo'] >= 0 ? 'bg-primary-soft' : 'bg-danger-soft' }}"><i class="fas fa-wallet"></i></div>
            <div class="stat-body">
                <div class="stat-label">Saldo do mês</div>
                <div class="stat-value {{ $fluxoCaixaMes['saldo'] < 0 ? 'text-danger' : '' }}">R$ {{ number_format($fluxoCaixaMes['saldo'], 2, ',', '.') }}</div>
            </div>
        </a>
        <a href="/vendas" class="stat-card {{ $alertas['notasComPendencia'] > 0 ? 'is-alert' : '' }}">
            <div class="stat-icon {{ $alertas['notasComPendencia'] > 0 ? 'bg-danger-soft' : 'bg-success-soft' }}"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-body">
                <div class="stat-label">Notas com pendência</div>
                <div class="stat-value">{{ $alertas['notasComPendencia'] }}</div>
            </div>
        </a>
        @if ($mostrarEstoque)
            <a href="{{ route('estoque.index') }}" class="stat-card {{ $alertas['produtosSemEstoque'] > 0 ? 'is-alert' : '' }}">
                <div class="stat-icon {{ $alertas['produtosSemEstoque'] > 0 ? 'bg-warning-soft' : 'bg-success-soft' }}"><i class="fas fa-box-open"></i></div>
                <div class="stat-body">
                    <div class="stat-label">Produtos sem estoque</div>
                    <div class="stat-value">{{ $alertas['produtosSemEstoque'] }}</div>
                </div>
            </a>
        @endif
    </div>

    {{-- Gráficos de decisão --}}
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card-main chart-card">
                <div class="home-section-title">
                    <h5>Faturamento — últimos 30 dias</h5>
                    <small>NFe + NFCe</small>
                </div>
                <div class="chart-box"><canvas id="chartFaturamento30"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card-main chart-card">
                <div class="home-section-title">
                    <h5>Mais vendidos</h5>
                    <small>mês atual</small>
                </div>
                <div class="chart-box" id="produtosHomeBox"><canvas id="chartProdutosHome"></canvas></div>
            </div>
        </div>
    </div>

    {{-- Atalhos e cadastros --}}
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="home-section-title">
                <h5>Acesso rápido</h5>
            </div>
            <x-home-atalhos />
        </div>
        <div class="col-lg-4 mb-4">
            <div class="home-section-title">
                <h5>Sua base</h5>
            </div>
            <div class="card-main" style="padding: 8px 20px;">
                <ul class="cadastro-list">
                    @if ($mostrarNotas)
                        <li>
                            <a href="/vendas">
                                <span class="stat-icon bg-primary-soft"><i class="fas fa-file-invoice"></i></span>
                                <span class="cadastro-label">Notas emitidas no mês</span>
                                <span class="cadastro-value">{{ $quantidadePedidosPorMes }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="/relatorios">
                                <span class="stat-icon bg-info-soft"><i class="fas fa-coins"></i></span>
                                <span class="cadastro-label">Valor das notas no mês</span>
                                <span class="cadastro-value">R$ {{ number_format($quantidadeValorPedido, 2, ',', '.') }}</span>
                            </a>
                        </li>
                    @endif
                    @if ($mostrarEstoque)
                        <li>
                            <a href="{{ route('estoque.index') }}">
                                <span class="stat-icon bg-success-soft"><i class="fas fa-boxes"></i></span>
                                <span class="cadastro-label">Produtos com estoque</span>
                                <span class="cadastro-value">{{ $quantidadeProdutosEmEstoque }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('produto.index') }}">
                                <span class="stat-icon bg-warning-soft"><i class="fas fa-tags"></i></span>
                                <span class="cadastro-label">Produtos cadastrados</span>
                                <span class="cadastro-value">{{ $quantidadeProduto }}</span>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('cliente.index') }}">
                            <span class="stat-icon bg-primary-soft"><i class="fas fa-users"></i></span>
                            <span class="cadastro-label">Clientes cadastrados</span>
                            <span class="cadastro-value">{{ $quantidadeCliente }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Volume mensal por documento fiscal (cards sem dados são ocultados) --}}
    <div class="row">
        <div class="col-xl-4 col-lg-6 mb-4" data-doc-chart>
            <div class="card-main chart-card">
                <div class="home-section-title"><h5>NFe por mês</h5></div>
                <div class="chart-box" style="height: 240px;"><canvas id="nfe-chart"></canvas></div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-4" data-doc-chart>
            <div class="card-main chart-card">
                <div class="home-section-title"><h5>NFCe por mês</h5></div>
                <div class="chart-box" style="height: 240px;"><canvas id="nfce-chart"></canvas></div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-4" data-doc-chart>
            <div class="card-main chart-card">
                <div class="home-section-title"><h5>MDFe por mês</h5></div>
                <div class="chart-box" style="height: 240px;"><canvas id="mdfe-chart"></canvas></div>
            </div>
        </div>
    </div>

    <script>
        const paleta = {
            azul: '#2a78d6', aqua: '#1baf7a', amarelo: '#eda100', verde: '#008300',
            violeta: '#4a3aa7', vermelho: '#e34948', magenta: '#e87ba4', laranja: '#eb6834'
        };
        const formatoMoeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
        const meses = ['', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

        function aplicarPadroesChart() {
            Chart.defaults.font.family = "'Poppins', sans-serif";
            Chart.defaults.color = '#6c757d';
            Chart.defaults.plugins.tooltip.padding = 10;
            Chart.defaults.plugins.tooltip.cornerRadius = 8;
            Chart.defaults.plugins.tooltip.backgroundColor = '#00033a';
        }

        const gradeSuave = { color: 'rgba(0, 0, 0, 0.05)' };

        function renderGraficosDecisao() {
            const vendasUltimos30Dias = @json($vendasUltimos30Dias);
            const produtosMaisVendidos = @json($produtosMaisVendidos);

            const ctxFaturamento = document.getElementById('chartFaturamento30').getContext('2d');
            const gradiente = ctxFaturamento.createLinearGradient(0, 0, 0, 280);
            gradiente.addColorStop(0, paleta.azul + '55');
            gradiente.addColorStop(1, paleta.azul + '00');

            new Chart(ctxFaturamento, {
                type: 'line',
                data: {
                    labels: vendasUltimos30Dias.map(i => i.dia),
                    datasets: [{
                        label: 'Faturamento',
                        data: vendasUltimos30Dias.map(i => i.valor),
                        borderColor: paleta.azul,
                        backgroundColor: gradiente,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: ctx => formatoMoeda.format(ctx.parsed.y) } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { maxTicksLimit: 8 } },
                        y: { beginAtZero: true, grid: gradeSuave, ticks: { callback: v => formatoMoeda.format(v).replace(',00', '') } }
                    }
                }
            });

            if (produtosMaisVendidos.length === 0) {
                document.getElementById('produtosHomeBox').innerHTML =
                    '<div class="chart-empty"><i class="fas fa-shopping-basket"></i><span>Sem vendas registradas este mês</span></div>';
                return;
            }

            new Chart(document.getElementById('chartProdutosHome').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: produtosMaisVendidos.map(i => i.nome.length > 22 ? i.nome.slice(0, 22) + '…' : i.nome),
                    datasets: [{
                        label: 'Quantidade vendida',
                        data: produtosMaisVendidos.map(i => i.quantidade),
                        backgroundColor: paleta.azul,
                        borderRadius: 6,
                        maxBarThickness: 22,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { title: items => produtosMaisVendidos[items[0].dataIndex].nome } }
                    },
                    scales: {
                        x: { beginAtZero: true, grid: gradeSuave },
                        y: { grid: { display: false } }
                    }
                }
            });
        }

        // Gráfico mensal por documento fiscal; oculta o card quando não há dados ou a requisição falha
        function createChart(elementId, fetchUrl, label, cor) {
            const coluna = document.getElementById(elementId).closest('[data-doc-chart]');

            fetch(fetchUrl)
                .then(response => response.json())
                .then(data => {
                    if (!Array.isArray(data) || data.length === 0) {
                        coluna.style.display = 'none';
                        return;
                    }

                    new Chart(document.getElementById(elementId).getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: data.map(item => meses[item.mes]),
                            datasets: [{
                                label: label,
                                data: data.map(item => item.total_vendas),
                                backgroundColor: cor + 'cc',
                                hoverBackgroundColor: cor,
                                borderRadius: 6,
                                maxBarThickness: 32,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { grid: { display: false } },
                                y: { beginAtZero: true, grid: gradeSuave }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Erro ao carregar dados do gráfico:', error);
                    coluna.style.display = 'none';
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            aplicarPadroesChart();
            renderGraficosDecisao();
            createChart('nfe-chart', '/total-mes-nfes', 'Vendas NFe', paleta.azul);
            createChart('nfce-chart', '/nfce/total-mes-nfces', 'Vendas NFCe', paleta.aqua);
            createChart('mdfe-chart', '/mdfes/total-mes-mdfes', 'MDFe emitidos', paleta.violeta);
        });
    </script>
@stop
