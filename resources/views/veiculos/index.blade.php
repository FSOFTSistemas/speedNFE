@extends('adminlte::page')

@section('title', 'Veículos')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3>Veículos</h3>
        </div>
    </div>
@stop

@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <a class="btn btn-info" style="margin-bottom: 2%" href="{{ route('veiculos.create') }}">&nbsp;+ Veículo&nbsp;</a>
        </div>
    </div>

    <table class="table table-hover" id="veiculos">
        <thead class="table-primary">
            <tr>
                <th>Placa</th>
                <th>CPF/CNPJ</th>
                <th>Nome</th>
                <th>Tipo de propriedade</th>
                <th>Tara (Kg)</th>
                <th>Capacidade (M³)</th>
                <th>Tipo de veículo</th>
            </tr>
        </thead>
    </table>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#veiculos').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
                },
            });
        });
    </script>
@stop
