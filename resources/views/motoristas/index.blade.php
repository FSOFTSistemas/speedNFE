@extends('adminlte::page')

@section('title', 'Motoristas')

@section('content_header')

@stop

@section('content')

    <div class="row" style="padding-top: 1%">
        <div class="col">
            <a class="btn btn-primary" style="margin-bottom: 1%" href="{{ route('motorista.create') }}">&nbsp;+ Novo
                motorista&nbsp;</a>
        </div>
    </div>

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
            ]
        ],
        'searching' => true,
        'lengthChange' => true,
        'pageLength' => 10,
        'ordering' => true
    ])
        <thead class="table-primary" style="text-align: center">
            <tr>
                <th>Nome</th>
                <th>Cpf</th>
                @if ($empresa == 1)
                    <th>Empresa</th>
                @endif
                <th></th>
            </tr>
        </thead>

        <tbody style="text-align: center">
            @foreach ($motoristas as $motorista)
                <tr>
                    <td>{{ $motorista->nome }}</td>
                    <td>{{ $motorista->cpf }}</td>
                    @if ($empresa == 1)
                        <td>{{ $motorista->fantasia }}</td>
                    @endif
                    <td>
                        <div class="row">
                            <div class="col">
                                <a title="Editar" href='{{ route('motorista.edit', [$motorista->id]) }}'
                                    class='text-warning'><i class="fa fa-edit"></i></a>
                            </div>
                            <div class="col">
                                <a title="Excluir" onclick="setaDadosModal({{ $motorista->id }})" class='text-danger'><i
                                        class="fa fa-trash" data-toggle="modal" data-target=".bd-delete-modal-lg"></i></a>
                            </div>
                        </div>
                    </td>
                </tr>
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
                            <h4>Apagar este Motorista?</h4>
                        </div>
                    </div>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <p style="color: red; text-align: center">OBS: Você irá excluir todas as informações sobre este
                        motorista!
                    </p>

                    <div class="" style="text-align: center">

                        <form action="{{ route('motorista.delete') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('DELETE')
                            <div class="form-group">
                                <input type="hidden" step="0.01" class="form-control" id="motoristaId"
                                    name="motoristaId" value="">
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

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        function setaDadosModal(motoristaId) {
            document.getElementById('motoristaId').value = motoristaId;
        }
    </script>
@stop
