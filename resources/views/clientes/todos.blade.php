@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h1 class="m-0 text-dark">Clientes</h1>
        </div>
    </div>
@stop

@section('content')

    <div class="container">
        <a class="btn btn-info" style="margin-bottom: 2%" href='/cliente/cadastro'>&nbsp; + Cliente &nbsp;</a>
        <table class="table table-striped" id="clientes">
            <thead>
                <th>NOME</th>
                <th>CPF OU CNPJ</th>
                {{-- <th>CELULAR</th>
                <th>TIPO</th>
                <th>LIMITE</th> --}}
                @if ($empresa == 1)
                    <th>EMPRESA</th>
                @endif
                <th></th>
                <th></th>
            </thead>
            <tbody>
                @foreach ($clientes as $cliente)
                    @if ($cliente->situacao == 0)
                        {{-- situacao = 0 é ativo --}}
                        <tr>
                            <td>{{ $cliente->nome }}</td>
                            <td>{{ $cliente->cpf_cnpj }}</td>
                            {{-- <td>{{$cliente->celular}}</td>
                            @if ($cliente->tipo = 1)
                                <td>Pessoa Física</td>
                            @else
                                <td>Pessoa Jurídica</td>
                            @endif
                            <td>{{$cliente->limite}}</td> --}}
                            @if ($empresa == 1)
                                <td>{{ $cliente->fantasia }}</td>
                            @endif
                            <td><a title="Editar" href='{{ route('editar_cliente', ['id' => $cliente->id]) }}'
                                    class='text-warning'><i class="fa fa-edit"></i></a></td>
                            <td><a title="Excluir" href='{{ route('excluir_cliente', ['id' => $cliente->id]) }}'
                                    class='text-danger'><i class="fa fa-ban"></i></a></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#clientes').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
                },
            });
        });
    </script>
@stop
