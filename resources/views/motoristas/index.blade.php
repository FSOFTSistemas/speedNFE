@extends('adminlte::page')

@section('title', 'Motoristas')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3>Motoristas</h3>
        </div>
    </div>
@stop

@section('content')

    <div class="row" style="margin-bottom: 2%">
        <div class="col">
            <a class="btn btn-info" href="{{ route('motorista.create') }}">+ Registrar Motorista</a>
        </div>
    </div>

    <div class="container">
        <table class="table table-hover" id="motoristas_table">
            <thead class="table-primary">
                <tr>
                    <th>Nome</th>
                    <th>Cpf</th>
                    <th>Empresa</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($motoristas as $motorista)
                    <tr>
                        <td>{{ $motorista->nome }}</td>
                        <td>{{ $motorista->cpf }}</td>
                        <td>{{ $motorista->fantasia }}</td>
                    </tr>
                @endforeach
            </tbody>
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
            $('#motoristas_table').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
                },
            });
        });
    </script>
@stop
