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
    <link rel="stylesheet" href="{{ asset(" css/Telas_Internas/home.css") }}">
    <title>Document</title>
</head>

<body>



    <canvas id="Emissão_Mês"></canvas>
    <script>
        var dados = {!! json_encode($quantidadePedidosPorMes->pluck('total_pedidos')->toArray()) !!};
        var ctx = document.getElementById("Emissão_Mês").getContext("2d");
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($quantidadePedidosPorMes->pluck('mes')->map(function($mes) { return \Carbon\Carbon::createFromFormat('m', $mes)->locale('pt_BR')->format('F'); })->toArray()) !!},
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
                        beginAtZero: true,
                    }
                }
            }
        });
    </script>
    
    



</body>

</html>

@stop