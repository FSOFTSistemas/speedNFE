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
    <a class="btn btn-primary" style="margin-bottom: 2%" href='/cliente/cadastro'>&nbsp; + Cliente &nbsp;</a>

    @component('components.dataTable', [
        'responsive' => [
            [
                'responsivePriority' => 1,
                'targets' => 0,
            ],
            [
                'responsivePriority' => 2,
                'targets' => 1,
            ],
            [
                'responsivePriority' => 3,
                'targets' => 2,
            ],
            [
                'responsivePriority' => 4,
                'targets' => -1,
            ],
        ],
        'searching' => true,
        'lengthChange' => true,
        'pageLength' => 10,
        'ordering' => true
    ])
        <thead class="table-primary">
            <tr>
                <th>NOME</th>
                <th>CNPJ</th>
                @if ($empresa == 1)
                    <th>EMPRESA</th>
                @endif
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clientes as $cliente)
                @if ($cliente->situacao == 0)
                    {{-- situacao = 0 é ativo --}}
                    <tr>
                        <td>{{ $cliente->nome }}</td>
                        <td>{{ $cliente->cpf_cnpj }}</td>
                        @if ($empresa == 1)
                            <td>{{ $cliente->fantasia }}</td>
                        @endif
                        <td>
                            <div class="row">
                                <div class="col">
                                    <a title="Editar" href='{{ route('editar_cliente', ['id' => $cliente->id]) }}'
                                        class='text-warning'><i class="fa fa-edit"></i></a>
                                </div>

                                <div class="col">
                                    <a title="Excluir" onclick="setaDadosModal({{ $cliente->id }})" class='text-danger'><i
                                            class="fa fa-trash" data-toggle="modal" data-target=".bd-delete-modal-lg"></i></a>
                                </div>

                                <div class="col">
                                    <a title="Visualizar" href='{{ route('cliente.view', ['id' => $cliente->id]) }}'
                                        class='text-primary'><i class="fa fa-eye"></i></a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    @endcomponent

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




@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.7.0.js" integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM="
        crossorigin="anonymous"></script>

    <script>
        function setaDadosModal(idCliente) {
            document.getElementById('idCliente').value = idCliente;
        }
    </script>
@stop
