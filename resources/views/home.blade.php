@extends('adminlte::page')

@section('title', 'Dashboard')

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content_header')
    <div class="page-eyebrow">Visão geral</div>
    <h1 class="m-0 text-dark" style="font-weight: 700;">Dashboard</h1>
    <div class="page-subtitle">Como o negócio está indo este mês, pra te ajudar a decidir</div>
@stop

@section('content')
<div class="alert alert-info alert-dismissible fade show shadow-sm mt-3" role="alert">
    <div class="d-flex align-items-center">
        <div class="mr-3">
            <i class="fas fa-bell fa-2x text-white"></i>
        </div>
        <div class="flex-fill">
            <h5 class="mb-1 font-weight-bold text-dark">Novidade no sistema!</h5>
           <p class="mb-1">
    O sistema agora oferece <strong>pagamento online do plano contratado</strong> e
    <strong>acompanhamento em tempo real das suas faturas</strong>, tudo de forma prática e segura.
</p>

            <a href="{{ route('faturas.index') }}" class="btn btn-sm  mt-1 text-white">
                <i class="fas fa-arrow-right"></i> Clique aqui para conferir
            </a>
        </div>
    </div>
    <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

{{-- KPIs do mês, com variação vs mês anterior --}}
<div class="stat-grid mb-4">
    <div class="stat-card">
        <div class="stat-icon bg-primary-soft"><i class="fas fa-money-bill-wave"></i></div>
        <div>
            <div class="stat-value">
                R$ {{ number_format($resumoMes['valorTotal'], 2, ',', '.') }}
                @if ($resumoMes['variacaoValorTotal'] != 0)
                    <span class="badge {{ $resumoMes['variacaoValorTotal'] > 0 ? 'badge-success' : 'badge-danger' }} ml-1" style="font-size: 0.65rem;">
                        <i class="fas fa-arrow-{{ $resumoMes['variacaoValorTotal'] > 0 ? 'up' : 'down' }}"></i> {{ abs($resumoMes['variacaoValorTotal']) }}%
                    </span>
                @endif
            </div>
            <div class="stat-label">Faturamento do mês</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-info-soft"><i class="fas fa-receipt"></i></div>
        <div>
            <div class="stat-value">
                {{ $resumoMes['qtdVendas'] }}
                @if ($resumoMes['variacaoQtdVendas'] != 0)
                    <span class="badge {{ $resumoMes['variacaoQtdVendas'] > 0 ? 'badge-success' : 'badge-danger' }} ml-1" style="font-size: 0.65rem;">
                        <i class="fas fa-arrow-{{ $resumoMes['variacaoQtdVendas'] > 0 ? 'up' : 'down' }}"></i> {{ abs($resumoMes['variacaoQtdVendas']) }}%
                    </span>
                @endif
            </div>
            <div class="stat-label">Vendas no mês (NFe + NFCe)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-success-soft"><i class="fas fa-tag"></i></div>
        <div>
            <div class="stat-value">
                R$ {{ number_format($resumoMes['ticketMedio'], 2, ',', '.') }}
                @if ($resumoMes['variacaoTicketMedio'] != 0)
                    <span class="badge {{ $resumoMes['variacaoTicketMedio'] > 0 ? 'badge-success' : 'badge-danger' }} ml-1" style="font-size: 0.65rem;">
                        <i class="fas fa-arrow-{{ $resumoMes['variacaoTicketMedio'] > 0 ? 'up' : 'down' }}"></i> {{ abs($resumoMes['variacaoTicketMedio']) }}%
                    </span>
                @endif
            </div>
            <div class="stat-label">Tíquete médio</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-warning-soft"><i class="fas fa-chart-line"></i></div>
        <div>
            <div class="stat-value">
                R$ {{ number_format($resumoMes['lucroTotal'], 2, ',', '.') }}
                @if ($resumoMes['variacaoLucroTotal'] != 0)
                    <span class="badge {{ $resumoMes['variacaoLucroTotal'] > 0 ? 'badge-success' : 'badge-danger' }} ml-1" style="font-size: 0.65rem;">
                        <i class="fas fa-arrow-{{ $resumoMes['variacaoLucroTotal'] > 0 ? 'up' : 'down' }}"></i> {{ abs($resumoMes['variacaoLucroTotal']) }}%
                    </span>
                @endif
            </div>
            <div class="stat-label">Lucro estimado</div>
        </div>
    </div>
</div>

{{-- Caixa e alertas operacionais --}}
<div class="stat-grid mb-4">
    <a href="{{ route('fluxo-caixa.index') }}" class="text-decoration-none">
        <div class="stat-card">
            <div class="stat-icon bg-success-soft"><i class="fas fa-arrow-circle-up"></i></div>
            <div>
                <div class="stat-value">R$ {{ number_format($fluxoCaixaMes['entradas'], 2, ',', '.') }}</div>
                <div class="stat-label">Entradas de caixa no mês</div>
            </div>
        </div>
    </a>
    <a href="{{ route('fluxo-caixa.index') }}" class="text-decoration-none">
        <div class="stat-card">
            <div class="stat-icon {{ $fluxoCaixaMes['saldo'] >= 0 ? 'bg-primary-soft' : 'bg-danger-soft' }}"><i class="fas fa-wallet"></i></div>
            <div>
                <div class="stat-value">R$ {{ number_format($fluxoCaixaMes['saldo'], 2, ',', '.') }}</div>
                <div class="stat-label">Saldo de caixa do mês</div>
            </div>
        </div>
    </a>
    <a href="/vendas" class="text-decoration-none">
        <div class="stat-card">
            <div class="stat-icon bg-danger-soft"><i class="fas fa-file-excel"></i></div>
            <div>
                <div class="stat-value">{{ $alertas['notasComPendencia'] }}</div>
                <div class="stat-label">Notas com pendência</div>
            </div>
        </div>
    </a>
</div>

{{-- Gráficos de decisão --}}
<div class="row mb-4">
    <div class="col-md-6 mb-4">
        <div class="card card-main" style="padding: 24px;">
            <h5 class="mb-3" style="font-weight: 600;">Faturamento (últimos 30 dias)</h5>
            <div style="height: 280px;"><canvas id="chartFaturamento30"></canvas></div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card card-main" style="padding: 24px;">
            <h5 class="mb-3" style="font-weight: 600;">Produtos mais vendidos (mês atual)</h5>
            <div style="height: 280px;"><canvas id="chartProdutosHome"></canvas></div>
        </div>
    </div>
</div>

    <div class="row">
        {{-- Card: Notas Emitidas --}}
        @if (!auth()->user()->can('client-NFCe') || !auth()->user()->can('client-MDFe') || !auth()->user()->can('client-CTe') || !auth()->user()->can('client-advanced3'))
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card card-main h-100" style="padding: 20px;">
                    <div class="stat-value">{{ $quantidadePedidosPorMes }}</div>
                    <div class="stat-label mb-3">Notas emitidas este mês</div>
                    <a href="/vendas/nova" class="mt-auto">Emitir NFE <i class="fas fa-arrow-circle-right ml-1"></i></a>
                </div>
            </div>
        @endif

        {{-- Card: Produtos com Estoque --}}
        @if (!auth()->user()->can('client-MDFe') || !auth()->user()->can('client-CTe') || !auth()->user()->can('client-advanced3'))
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card card-main h-100" style="padding: 20px;">
                    <div class="stat-value">{{ $quantidadeProdutosEmEstoque }}</div>
                    <div class="stat-label mb-3">Produtos com estoque</div>
                    <a href="{{ route('estoque.index') }}" class="mt-auto">Mais informações <i class="fas fa-arrow-circle-right ml-1"></i></a>
                </div>
            </div>

            {{-- Card: Produtos Cadastrados --}}
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card card-main h-100" style="padding: 20px;">
                    <div class="stat-value">{{ $quantidadeProduto }}</div>
                    <div class="stat-label mb-3">Produtos cadastrados</div>
                    <a href="/produto" class="mt-auto">Mais informações <i class="fas fa-arrow-circle-right ml-1"></i></a>
                </div>
            </div>
        @endif

        {{-- Card: Clientes Cadastrados --}}
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card card-main h-100" style="padding: 20px;">
                <div class="stat-value">{{ $quantidadeCliente }}</div>
                <div class="stat-label mb-3">Clientes cadastrados</div>
                <a href="/cliente" class="mt-auto">Mais informações <i class="fas fa-arrow-circle-right ml-1"></i></a>
            </div>
        </div>

        {{-- Card: Valor Total das Notas --}}
        @if (!auth()->user()->can('client-NFCe') || !auth()->user()->can('client-MDFe') || !auth()->user()->can('client-CTe') || !auth()->user()->can('client-advanced3'))
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card card-main h-100" style="padding: 20px;">
                    <div class="stat-value">R$ {{ number_format($quantidadeValorPedido, 2, ',', '.') }}</div>
                    <div class="stat-label mb-3">Valor total das notas (mês)</div>
                    <a href="/relatorios" class="mt-auto">Mais informações <i class="fas fa-arrow-circle-right ml-1"></i></a>
                </div>
            </div>
        @endif
    </div>

    {{-- Volume mensal por documento fiscal --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-main mb-4" style="padding: 24px;">
                <div style="height: 260px;"><canvas id="nfe-chart"></canvas></div>
            </div>
            <div class="card card-main mb-4" style="padding: 24px;">
                <div style="height: 260px;"><canvas id="nfce-chart"></canvas></div>
            </div>
            <div class="card card-main mb-4" style="padding: 24px;">
                <div style="height: 260px;"><canvas id="mdfe-chart"></canvas></div>
            </div>
        </div>
    </div>

    <script>
        function renderGraficosDecisao() {
            const paleta = {
                azul: '#2a78d6', aqua: '#1baf7a', amarelo: '#eda100', verde: '#008300',
                violeta: '#4a3aa7', vermelho: '#e34948', magenta: '#e87ba4', laranja: '#eb6834'
            };

            const vendasUltimos30Dias = @json($vendasUltimos30Dias);
            const produtosMaisVendidos = @json($produtosMaisVendidos);

            new Chart(document.getElementById('chartFaturamento30').getContext('2d'), {
                type: 'line',
                data: {
                    labels: vendasUltimos30Dias.map(i => i.dia),
                    datasets: [{
                        label: 'Faturamento',
                        data: vendasUltimos30Dias.map(i => i.valor),
                        borderColor: paleta.azul,
                        backgroundColor: paleta.azul + '33',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            if (produtosMaisVendidos.length === 0) {
                document.getElementById('chartProdutosHome').closest('.card-main').innerHTML = '<h5 style="font-weight: 600;">Produtos mais vendidos (mês atual)</h5><p class="text-muted text-center mb-0 mt-4">Sem vendas registradas este mês</p>';
            } else {
                new Chart(document.getElementById('chartProdutosHome').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: produtosMaisVendidos.map(i => i.nome),
                        datasets: [{
                            label: 'Quantidade vendida',
                            data: produtosMaisVendidos.map(i => i.quantidade),
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
            }
        }

        // Lógica para renderizar os gráficos permanece a mesma
        var meses = ['', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

        // Função auxiliar para criar gráficos e evitar repetição de código
        function createChart(elementId, fetchUrl, label, backgroundColor, borderColor) {
            fetch(fetchUrl)
                .then(response => response.json())
                .then(data => {
                    if (!data || data.length === 0) {
                        document.getElementById(elementId).closest('.card-main').style.display = 'none';
                        return;
                    }
                    const ctx = document.getElementById(elementId).getContext('2d');
                    const mesesSelecionados = data.map(item => meses[item.mes]);
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: mesesSelecionados,
                            datasets: [{
                                label: label,
                                data: data.map(item => item.total_vendas),
                                backgroundColor: backgroundColor,
                                borderColor: borderColor,
                                borderWidth: 1,
                                borderRadius: 5,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: label,
                                    font: {
                                        size: 16
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Erro ao carregar dados do gráfico:', error);
                    document.getElementById(elementId).closest('.card-main').style.display = 'none';
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderGraficosDecisao();
            createChart(
                'nfe-chart',
                '/total-mes-nfes',
                'Total de Vendas por Mês (NFe)',
                'rgba(52, 152, 219, 0.5)',
                'rgba(52, 152, 219, 1)'
            );
            createChart(
                'nfce-chart',
                '/nfce/total-mes-nfces',
                'Total de Vendas por Mês (NFCe)',
                'rgba(231, 76, 60, 0.5)',
                'rgba(231, 76, 60, 1)'
            );
            createChart(
                'mdfe-chart',
                '/mdfes/total-mes-mdfes',
                'Total de Vendas por Mês (MDFe)',
                'rgba(46, 204, 113, 0.5)',
                'rgba(46, 204, 113, 1)'
            );
        });
    </script>
@stop
