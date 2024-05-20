@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h1 class="m-0 text-dark">Empresas</h1>
        </div>
    </div>
@stop

@section('content')

        <a class="btn btn-info" style="margin-bottom: 2%" href="/empresa/cadastro">&nbsp; + Empresa &nbsp;</a>
        <a class="btn btn-info" style="margin-bottom: 2%" href="/usuarios">&nbsp; Usuários &nbsp;</a>

        <table class="table table-hover" id="empresas">
            <thead class="table-primary">
                <th>ID</th>
                <th>RAZÃO SOCIAL</th>
                <th>CPF OU CNPJ</th>
                <th></th>
                <th></th>
                <th></th>
            </thead>
            <tbody>
                @foreach ($empresas as $empresa)
                    <tr>
                        <td><b>#{{ $empresa->id }}</b></td>
                        <td>{{ $empresa->fantasia }}</td>
                        <td>{{ $empresa->cpf_cnpj }}</td>
                        @if ($empresa->status == 1)
                            <td><a class="text-danger"
                                    href="{{ route('desativarReativar_empresa', ['id' => $empresa->id]) }}"><i
                                        class="fas fa-ban"></i></a></td>
                        @else
                            <td><a class="text-success"
                                    href="{{ route('desativarReativar_empresa', ['id' => $empresa->id]) }}"><i
                                        class="fas fa-check"></i></a></td>
                        @endif
                        <td>
                            <a href="{{ route('editar_empresa', ['id' => $empresa->id]) }}"><i class="fa fa-edit"></i></a>
                        </td>
                        <td>
                            <a href="{{ route('empresa.view', [$empresa->id]) }}"><i class="fa fa-eye"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script>
        function setaDadosModal(idCliente) {
            document.getElementById('idCliente').value = idCliente;
        }

        $(document).ready(function() {
            $('#empresas').DataTable({
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json',
                },
            });
        });
    </script>
@stop
