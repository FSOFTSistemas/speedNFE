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
                            <td><a title="Excluir" onclick="setaDadosModal({{ $cliente->id }})" class='text-danger'><i class="fa fa-trash" data-toggle="modal"
                                        data-target=".bd-delete-modal-lg"></i></a></td>
                            <td><a title="Visualizar" href='{{ route('cliente.view', ['id' => $cliente->id]) }}'
                                    class='text-primary'><i class="fa fa-eye"></i></a></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade bd-delete-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">

                    <div class="col" style="text-align: center">
                        <div class="modal-title" style="text: center">
                            <h4>Apagar este Cliente?</h4>
                        </div>
                    </div>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <p style="color: red; text-align: center">OBS: Você irá excluir todas as informações sobre este cliente!
                    </p>

                    <div class="" style="text-align: center">

                        <form action="{{ route('excluir_cliente') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('DELETE')
                            <div class="form-group">
                                <input type="hidden" step="0.01" class="form-control" id="idCliente" name="idCliente"
                                    value="">
                            </div>

                            <div class="text-center">
                                <button type="submit" style="width: 50%;" class="btn btn-danger">EXCLUIR</button>
                            </div>
                            <br>
                        </form>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
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
            $('#clientes').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
                },
            });
        });
    </script>
@stop
