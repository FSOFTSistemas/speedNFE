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
    <div class="row mb-3">
        <div class="col">
            <a class="btn btn-primary mb-1" href="{{ route('empresa.create') }}">&nbsp;+ Empresa &nbsp;</a>
            <a class="btn btn-info text-light mb-1" href="{{ route('index_usuario') }}">&nbsp;Usuários&nbsp;</a>
        </div>

        <div class="col text-right">
            <a class="btn btn-secondary" href="{{ route('empresa.index') }}">Voltar</a>
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
            ],
        ],
        'searching' => true,
        'lengthChange' => true,
        'pageLength' => 10,
        'ordering' => true,
    ])
        <thead class="table-primary">
            <th>ID</th>
            <th>RAZÃO SOCIAL</th>
            <th>CPF OU CNPJ</th>
            <th></th>
        </thead>
        <tbody>
            @foreach ($empresas as $empresa)
                <tr>
                    <td><b>#{{ $empresa->id }}</b></td>
                    <td>{{ $empresa->fantasia }}</td>
                    <td>{{ $empresa->cpf_cnpj }}</td>
                    <td>
                        <div class="row">
                            @if ($empresa->status == 1)
                                <div class="col">
                                    <a title="Desativar" class="text-danger"
                                        href="{{ route('desativarReativar_empresa', ['id' => $empresa->id]) }}"><i
                                            class="fas fa-ban"></i></a>
                                </div>
                            @else
                                <div class="col">
                                    <a title="Ativar" class="text-success"
                                        href="{{ route('desativarReativar_empresa', ['id' => $empresa->id]) }}"><i
                                            class="fas fa-check"></i></a>
                                </div>
                            @endif
                            <div class="col">
                                <a title="Editar" href="{{ route('editar_empresa', [$empresa->id]) }}"><i class="far fa-edit text-teal"></i></a>
                            </div>

                            <div class="col">
                                <a title="Visualizar" href="{{ route('empresa.view', [$empresa->id]) }}"><i class="far fa-eye"></i></a>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    @endcomponent
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
        function setaDadosModal(idCliente) {
            document.getElementById('idCliente').value = idCliente;
        }
    </script>
@stop
