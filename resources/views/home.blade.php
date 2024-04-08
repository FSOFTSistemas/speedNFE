@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
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

        <div class="card-container">
            <div class="card card-red" onclick="toggleExtraInfo('red')">
                <div class="card-content">
                    <div class="column-left">
                        <div class="card-icon">
                            <i class="fas fa-boxes" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="column-right">
                        <div class="small-info">
                            <div class="small-info-1">Clientes Cadastrados</div>
                        </div>
                        <div class="extra-info" id="extra-info-red" style="display: none;">
                            <div class="large-info">{{ $quantidadeCliente }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-blue" onclick="toggleExtraInfo('blue')">
                <div class="card-content">
                    <div class="column-left">
                        <div class="card-icon">
                            <i class="fas fa-user" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="column-right">
                        <div class="small-info">
                            <div class="small-info-2">Produtos Cadastrados</div>
                        </div>
                        <div class="extra-info" id="extra-info-blue" style="display: none;">
                            <div class="large-info">{{ $quantidadeProduto }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-green" onclick="toggleExtraInfo('green')">
                <div class="card-content">
                    <div class="column-left">
                        <div class="card-icon">
                            <i class="fas fa-sticky-note" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="column-right">
                        <div class="small-info" style="margin-left: 60px">
                            <div class="small-info-3">Notas Emitidas</div>
                        </div>
                        <div class="extra-info" id="extra-info-green" style="display: none;">
                            <div class="large-info">{{ $quantidadePedidosPorMes }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-yellow" onclick="toggleExtraInfo('yellow')">
                <div class="card-content">
                    <div class="column-left">
                        <div class="card-icon">
                            <i class="fas fa-file-invoice-dollar" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="column-right">
                        <div class="small-info">
                            <div class="small-info-4">Valor Total da Notas</div>
                        </div>
                        <div class="extra-info" id="extra-info-yellow" style="display: none;">
                            <div class="large-info" style="font-size: 18px;">R$ {{ $quantidadeValorPedido }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        <div id="section-graph">

            <canvas id="totalVendasMes"></canvas>

        </div>
        <script>
            const ctx = document.getElementById('totalVendasMes');
        
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {{ Js::from($totalMes[0]) }},
                    datasets: [{
                        label: 'Total de Vendas por Mês',
                        data:{{ Js::from($totalMes[1]) }},
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
            scales: {
                y: {
                    beginAtZero: true
                },
            }
        }
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
