@extends('adminlte::page')

@section('title', 'Dashboard')

{{-- Adiciona os estilos customizados para a página --}}
@push('css')
<style>
    /* Importa a fonte Poppins para consistência com a tela de login */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    /* Variáveis de cor para fácil manutenção */
    :root {
        --primary-color: #0a2540;
        --accent-blue: #3498db;
        --accent-purple: #8e44ad;
        --accent-green: #2ecc71;
        --accent-yellow: #f1c40f;
        --accent-red: #e74c3c;
        --card-bg: #ffffff;
        --text-light: #f8f9fa;
        --text-dark: #343a40;
        --shadow-color: rgba(0, 0, 0, 0.08);
    }

    /* Estilo base da página */
    body {
        font-family: 'Poppins', sans-serif;
    }

    /* Estilo do novo card de estatísticas */
    .stat-card {
        background: var(--card-bg);
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px var(--shadow-color);
        padding: 25px;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    }

    /* Conteúdo interno do card */
    .stat-card .inner {
        position: relative;
        z-index: 2;
    }

    .stat-card h3 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0;
        color: var(--text-dark);
    }

    .stat-card p {
        font-size: 1rem;
        color: #6c757d;
    }

    /* Ícone decorativo no fundo */
    .stat-card .icon {
        position: absolute;
        top: 50%;
        right: 20px;
        transform: translateY(-50%);
        font-size: 80px;
        color: rgba(0, 0, 0, 0.07);
        z-index: 1;
        transition: transform 0.4s ease, color 0.4s ease;
    }

    .stat-card:hover .icon {
        transform: translateY(-50%) scale(1.1);
    }

    /* Rodapé do card com o link */
    .stat-card-footer {
        display: block;
        padding: 10px 0 0 0;
        margin-top: 15px;
        border-top: 1px solid #eee;
        text-align: center;
        color: #6c757d;
        text-decoration: none;
        font-weight: 500;
        z-index: 2;
        position: relative;
        transition: color 0.3s ease;
    }

    /* Variações de cor para cada card */
    .stat-card.blue { border-left: 5px solid var(--accent-blue); }
    .stat-card.purple { border-left: 5px solid var(--accent-purple); }
    .stat-card.green { border-left: 5px solid var(--accent-green); }
    .stat-card.yellow { border-left: 5px solid var(--accent-yellow); }
    .stat-card.red { border-left: 5px solid var(--accent-red); }

    .stat-card.blue .stat-card-footer:hover { color: var(--accent-blue); }
    .stat-card.purple .stat-card-footer:hover { color: var(--accent-purple); }
    .stat-card.green .stat-card-footer:hover { color: var(--accent-green); }
    .stat-card.yellow .stat-card-footer:hover { color: var(--accent-yellow); }
    .stat-card.red .stat-card-footer:hover { color: var(--accent-red); }
    
    /* Container dos gráficos */
    .chart-container {
        background: #fff;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 20px var(--shadow-color);
    }

</style>
@endpush

{{-- Adiciona a biblioteca Chart.js --}}
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush


@section('content_header')
    <div class="row">
        <div class="col">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Dashboard</h1>
        </div>
    </div>
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
    <div class="row">
        {{-- Card: Notas Emitidas --}}
        @if (!auth()->user()->can('client-NFCe') || !auth()->user()->can('client-MDFe') || !auth()->user()->can('client-CTe') || !auth()->user()->can('client-advanced3'))
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="stat-card blue">
                    <div class="inner">
                        <h3>{{ $quantidadePedidosPorMes }}</h3>
                        <p>Notas emitidas este mês</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <a href="/vendas/nova" class="stat-card-footer">
                        Emitir NFE <i class="fas fa-arrow-circle-right ml-1"></i>
                    </a>
                </div>
            </div>
        @endif

        {{-- Card: Produtos com Estoque --}}
        @if (!auth()->user()->can('client-MDFe') || !auth()->user()->can('client-CTe') || !auth()->user()->can('client-advanced3'))
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="stat-card purple">
                    <div class="inner">
                        <h3>{{ $quantidadeProdutosEmEstoque }}</h3>
                        <p>Produtos com estoque</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <a href="{{ route('estoque.index') }}" class="stat-card-footer">
                        Mais informações <i class="fas fa-arrow-circle-right ml-1"></i>
                    </a>
                </div>
            </div>

            {{-- Card: Produtos Cadastrados --}}
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="stat-card green">
                    <div class="inner">
                        <h3>{{ $quantidadeProduto }}</h3>
                        <p>Produtos cadastrados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <a href="/produto" class="stat-card-footer">
                        Mais informações <i class="fas fa-arrow-circle-right ml-1"></i>
                    </a>
                </div>
            </div>
        @endif

        {{-- Card: Clientes Cadastrados --}}
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="stat-card yellow">
                <div class="inner">
                    <h3>{{ $quantidadeCliente }}</h3>
                    <p>Clientes cadastrados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="/cliente" class="stat-card-footer">
                    Mais informações <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Card: Valor Total das Notas --}}
        @if (!auth()->user()->can('client-NFCe') || !auth()->user()->can('client-MDFe') || !auth()->user()->can('client-CTe') || !auth()->user()->can('client-advanced3'))
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="stat-card red">
                    <div class="inner">
                        <h3>R$ {{ $quantidadeValorPedido }}</h3>
                        <p>Valor total das notas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <a href="/relatorios" class="stat-card-footer">
                        Mais informações <i class="fas fa-arrow-circle-right ml-1"></i>
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Seção de Gráficos --}}
    <div class="row">
        <div class="col-12">
            <div class="chart-container mb-4">
                <canvas id="nfe-chart"></canvas>
            </div>
            <div class="chart-container mb-4">
                <canvas id="nfce-chart"></canvas>
            </div>
            <div class="chart-container mb-4">
                <canvas id="mdfe-chart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Lógica para renderizar os gráficos permanece a mesma
        var meses = ['', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

        // Função auxiliar para criar gráficos e evitar repetição de código
        function createChart(elementId, fetchUrl, label, backgroundColor, borderColor) {
            fetch(fetchUrl)
                .then(response => response.json())
                .then(data => {
                    if (!data || data.length === 0) {
                        document.getElementById(elementId).parentElement.style.display = 'none';
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
                    document.getElementById(elementId).parentElement.style.display = 'none';
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
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
