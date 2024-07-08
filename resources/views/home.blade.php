@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="row text-center">
        <div class="col">
            <h3>Dashboard</h3>
        </div>
    </div>
@stop

@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="stylesheet" href="{{ asset('css/Telas_Internas/home.css') }}">
    </head>

    <body>
        <div class="row">
            @can('master')
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $quantidadePedidosPorMes }}</h3>
                            <p>Notas emitidas este mês</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="/vendas/nova" class="small-box-footer">Emitir NFE <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @endcan

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $quantidadeProduto }}</h3>
                        <p>Produtos cadastrados</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="/produto" class="small-box-footer">Mais informações <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $quantidadeCliente }}</h3>
                        <p>Clientes cadastrados</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="/cliente" class="small-box-footer">Mais informações <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            @can('master')
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>R$ {{ $quantidadeValorPedido }}</h3>
                            <p>Valor total das notas</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="/relatorios" class="small-box-footer">Mais informaçôes <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @endcan
        </div>

        <div class="mt-5">
            <canvas id="nfe-chart"></canvas>
        </div>

        <div class="mt-5">
            <canvas id="nfce-chart"></canvas>
        </div>

        <div class="mt-5">
            <canvas id="mdfe-chart"></canvas>
        </div>

        <script>
            var meses = ['', 'Jan', 'Feb', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']

            document.addEventListener('DOMContentLoaded', function() {
                fetch('/total-mes-nfes')
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            return false
                        }
                        const ctx = document.getElementById('nfe-chart').getContext('2d');
                        var indicesSelecionados = data.map(item => item.mes);
                        var mesesSelecionados = indicesSelecionados.map(index => meses[index]);
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: mesesSelecionados,
                                datasets: [{
                                    label: 'Total de Vendas por Mês (NFe)',
                                    data: data.map(item => item.total_vendas),
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Erro:', error));
            });

            document.addEventListener('DOMContentLoaded', function() {
                fetch('nfce/total-mes-nfces')
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            return false
                        }
                        const ctx2 = document.getElementById('nfce-chart').getContext('2d');
                        var indicesSelecionados = data.map(item => item.mes);
                        var mesesSelecionados = indicesSelecionados.map(index => meses[index]);
                        new Chart(ctx2, {
                            type: 'bar',
                            data: {
                                labels: mesesSelecionados,
                                datasets: [{
                                    label: 'Total de Vendas por Mês (NFCe)',
                                    data: data.map(item => item.total_vendas),
                                    backgroundColor: 'rgba(235, 54, 54, 0.2)',
                                    borderColor: 'rgba(180, 0, 0, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Erro:', error));
            });

            document.addEventListener('DOMContentLoaded', function() {
                fetch('mdfes/total-mes-mdfes')
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            return false
                        }
                        const ctx3 = document.getElementById('mdfe-chart').getContext('2d');
                        var indicesSelecionados = data.map(item => item.mes);
                        var mesesSelecionados = indicesSelecionados.map(index => meses[index]);
                        new Chart(ctx3, {
                            type: 'bar',
                            data: {
                                labels: mesesSelecionados,
                                datasets: [{
                                    label: 'Total de Vendas por Mês (MDFe)',
                                    data: data.map(item => item.total_vendas),
                                    backgroundColor: 'rgba(72, 235, 54, 0.2)',
                                    borderColor: 'rgba(18, 176, 0, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Erro:', error));
            });
        </script>

        <script>
            function toggleExtraInfo(color) {
                var extraInfo = document.getElementById('extra-info-' + color);
                extraInfo.style.display = extraInfo.style.display === 'none' ? 'block' : 'none';
            }
        </script>
    </body>

    </html>
@stop
