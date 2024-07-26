@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h1 class="m-0 text-dark">Categorias</h1>
        </div>
    </div>
@stop

@section('content')

    <div class="row" style="margin-bottom: 2%">
        <div class="col">
            <a class="btn btn-primary" href="{{ route('cadastrar_categoria') }}">&nbsp; + Categoria
                &nbsp;</a>
        </div>
        <div class="col text-right">
            <a class="btn btn-secondary" href="{{ route('produto.index') }}">Voltar</a>
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
        'showFooter' => false,
    ])
        <thead class="table-primary">
            <tr>
                <th>DESCRIÇÃO</th>
                <th>STATUS</th>
                @if ($empresa == 1)
                    <th>EMPRESA</th>
                @endif
                <th style="text-align: center">ATIVAR/DESATIVAR</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categorias as $categoria)
                <tr>
                    <td>{{ $categoria->descricao }}</td>
                    @if ($categoria->status == 1)
                        <td>Ativa</td>
                    @else
                        <td>Inativa</td>
                    @endif
                    @if ($empresa == 1)
                        <td>{{ $categoria->fantasia }}</td>
                    @endif
                    <td style="text-align: center">
                        <a href="{{ route('desativarReativar_categoria', ['id' => $categoria->id]) }}">
                            @if ($categoria->status == 1)
                                <i title="Desativar" class="fa fa-ban text-danger"></i>
                            @else
                                <i title="Reativar" class="fa fa-check text-success"></i>
                            @endif
                        </a>
                    </td>

                </tr>
            @endforeach
        </tbody>
    @endcomponent

@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script>
        function setaDadosModal(idCategoria) {
            document.getElementById('idCategoria').value = idCategoria;
        }
    </script>
@stop
