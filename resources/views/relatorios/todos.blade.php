@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')

@stop

@section('content')
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/cr-2.0.0/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.1/sc-2.4.1/datatables.min.css" rel="stylesheet">
</head>
<body>

<div class="container" style="padding-top: 20px">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">  
                <div class="card-header">Relatórios</div>

                <div class="card-body">
                    <form id="relatorioForm" method="POST" action="{{ route('relatorio-pdf') }}">
                        @csrf
                        <div class="form-group">
                            <label for="tipo_relatorio">Selecione o Tipo de Relatório:</label>
                            <select class="form-control" id="tipo_relatorio" name="tipo_relatorio">
                                <option value="nfe">Todos</option>
                                <option value="tipoR1">Vendas Sintéticas</option>
                                <option value="tipoR2">Vendas Analíticas</option>
                                <option value="tipoR3">Vendas de Produtos </option>

                            </select>
                        </div>
                        <div class="form-group">
                            <label for="estado">Status:</label>
                            <select class="form-control" id="estado" name="estado">
                                <option value="%">Todos</option>
                                <option value="Aprovado">Aprovado</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inicio">Data Início:</label>
                            <input type="date" class="form-control" id="inicio" name="inicio">
                        </div>
                        <div class="form-group">
                            <label for="fim">Data Fim:</label>
                            <input type="date" class="form-control" id="fim" name="fim">
                        </div>
                        <button type="submit" class="btn btn-primary" form="relatorioForm">Gerar PDF</button>
                    </form>

                </div>
            </div>

            <table class="display" id="produtos">
                <thead class="table-primary">
                    <tr>
                        <th>Nº Nota</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Situação</th>
                        <th>Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedidos as $venda )

                    <tr>
                        <td>{{ $venda->numero_nfe }}</td>
                        <td>{{ $venda->data }}</td>
                        <td>{{ $venda->cliente }}</td>
                        <td>{{ $venda->estado }}</td>
                        <td>{{ $venda->total }}</td>
                    </tr>
                        
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

    
</body>
</html>


@section('js')


<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/cr-2.0.0/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.1/sc-2.4.1/datatables.min.js"></script>
<script>
    function gerarPdf() {
        var form = document.getElementById('relatorioForm');
        form.action = "{{ route('relatorio-pdf') }}";
        form.submit();
    }
    </script>
<script>

$(document).ready(function() {
            $('#produtos').DataTable({
                responsive: {
                    details: true
                },
                language: {
                    "url": 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
                },
                layout: {
                    topStart: 'buttons',
                    top2Start: 'pageLength'
                },
                buttons: [{
                        extend: 'copyHtml5',
                        text: '<i class="fa fa-clone text-secondary"></i>',
                        titleAttr: 'Copiar',
                        download: 'open'
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel text-success"></i>',
                        titleAttr: 'Excel',
                        download: 'open',
                        title: 'Relatório de Clientes'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa fa-file-pdf text-danger"></i>',
                        titleAttr: 'PDF',
                        download: 'open',
                        title: 'Relatório de Clientes'
                    }
                ]
            });
        });
</script>
@endsection