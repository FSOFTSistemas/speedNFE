<html lang="en">
@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Gráficos</h1>
@stop

@section('content')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset("css/Telas_Internas/home.css") }}">
    <title>Document</title>
</head>
<body>

    <canvas id="Emissão_Mês"></canvas>

    <script>
        var dados = [50, 80, 120, 150, 200, 250, 300, 350, 400, 450, 500, 550];
        var ctx = document.getElementById("Emissão_Mês").getContext("2d");
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                datasets: [{
                    label: 'Emissão de Notas Por Mês',
                    data: dados,
                    backgroundColor: '#224B66',
                    borderColor: '#224B66',
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
    </script>

    <canvas id="Produtos_Estoque" width="400" height="180" style="margin-top: 50px;" ></canvas>

    <script>
        var dadosProdutos = [550, 500, 450, 400, 350, 300, 250, 200, 150, 120, 80, 50];
        var ctxProdutos = document.getElementById("Produtos_Estoque").getContext("2d");
        var myChartProdutos = new Chart(ctxProdutos, {
            type: 'bar',
            data: {
                labels: ['Macarrão', 'Arroz', 'Batata', 'Carro', 'Boi', 'Cavalo', 'Carroça', 'Teclado', 'Mouse', 'Boné', 'Pirulito', 'Garrafa'],
                datasets: [{
                    label: 'Produtos em Estoque',
                    data: dadosProdutos,
                    backgroundColor: '#224B66',
                    borderColor: '#224B66',
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
    </script>

</body>
</html>

@stop
