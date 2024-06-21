@extends('adminlte::page')

@section('title', 'Veículos')

@section('content_header')
    {{-- <div class="row" style="text-align: center">
        <div class="col">
            <h3>Veículos</h3>
        </div>
    </div> --}}
@stop

@section('content')

    <div class="row" style="padding-top: 1%;">
        <div class="col">
            <a class="btn btn-primary" style="margin-bottom: 1%;" href="{{ route('veiculos.create') }}">&nbsp;+ Novo
                veículo&nbsp;</a>
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
                'targets' => 3,
            ],
            [
                'responsivePriority' => 5,
                'targets' => 4,
            ],
            [
                'responsivePriority' => 6,
                'targets' => 5,
            ],
            [
                'responsivePriority' => 7,
                'targets' => 6,
            ],
            [
                'responsivePriority' => 8,
                'targets' => 7,
            ],
            [
                'responsivePriority' => 9,
                'targets' => -1,
            ],
        ],
        'searching' => true,
        'lengthChange' => true,
        'pageLength' => 10,
    ])
        <thead class="table-primary" style="width: 100%">
            <tr>
                <th style="width: 5%">Id</th>
                <th style="width: 10%">Placa</th>
                <th style="width: 10%">CPF/CNPJ</th>
                @if ($empresa == 1)
                    <th style="width: 20%">Empresa</th>
                @endif
                <th style="width: 10%">Tp. de propriedade</th>
                <th style="width: 10%">Tara(Kg)</th>
                <th style="width: 10%">Capacidade(M³)</th>
                <th style="width: 15%">Tp. de veículo</th>
                <th style="width: 10%"></th>
            </tr>
        </thead>
        <tbody style="width: 100%">
            @foreach ($veiculos as $veiculo)
                <tr>
                    <td>#{{ $veiculo->id }}</td>
                    <td>{{ $veiculo->placa }}</td>
                    <td>{{ $veiculo->cpf_cnpj }}</td>
                    @if ($empresa == 1)
                        <td>{{ $veiculo->fantasia }}</td>
                    @endif
                    <td>{{ $veiculo->tipo_propriedade }}</td>
                    <td>{{ number_format($veiculo->tara, 1) }}</td>
                    <td>{{ number_format($veiculo->capacidade_m3, 1) }}</td>
                    <td>{{ $veiculo->tipo_veiculo }}</td>
                    <td>
                        <div class="row">
                            <div class="col">
                                <a title="Editar" href='{{ route('veiculos.edit', [$veiculo->id]) }}'
                                    class='text-warning'><i class="fa fa-edit"></i></a>
                            </div>
                            <div class="col">
                                <a title="Excluir" onclick="setaDadosModal({{ $veiculo->id }})" class='text-danger'><i
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
                            <h4>Apagar este Veículo ?</h4>
                        </div>
                    </div>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <p style="color: red; text-align: center">OBS: Você irá excluir todas as informações sobre este veículo!
                    </p>

                    <div class="" style="text-align: center">

                        <form action="{{ route('veiculos.delete') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('DELETE')
                            <div class="form-group">
                                <input type="hidden" step="0.01" class="form-control" id="veiculoID" name="veiculoID"
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
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        function setaDadosModal(veiculoID) {
            document.getElementById('veiculoID').value = veiculoID;
        }
    </script>
@stop
