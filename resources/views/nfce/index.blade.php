@extends('adminlte::page')

@section('title', 'Notas Emitidas')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3>Resumo de Notas NFCe</h3>
        </div>
    </div>
@stop

@section('content')
    <div class="row" style="margin-bottom: 2%">
        <div class="col">
            <a class="btn btn-primary" href="{{ route('nfce.create') }}">+ Emitir NFCe</a>
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
            [
                'responsivePriority' => 5,
                'targets' => 3,
            ],
            [
                'responsivePriority' => 6,
                'targets' => 4,
            ]
        ],
        'searching' => true,
        'lengthChange' => true
    ])
        <thead class="table-primary">
            <tr>
                <th>Nº</th>
                <th>Data</th>
                <th>Série</th>
                <th>Chave</th>
                <th>Situação</th>
                <th>Cupom</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            @foreach ($cupoms as $cpm)
                <tr>
                    <td>{{ $cpm->nfce->nro ?? null }}</td>
                    <td>{{ $cpm->data }}</td>
                    <td>{{ $cpm->nfce->serie ?? null }}</td>
                    <td>{{ $cpm->nfce->chave ?? null }}</td>
                    <td>{{ $cpm->situacao }}</td>
                    <td>{{ $cpm->nroCupom }}</td>
                    <td>
                        <div class="row">
                            <div class="col">
                                <a title="Visualizar" target="_blank" href='{{ route('mdfe.view', [$cpm->id]) }}'
                                    class='text-primary'><i class="fa fa-eye"></i></a>
                            </div>

                            <div class="col">
                                <a title="Cancelar" href='{{ route('mdfe.view', [$cpm->id]) }}'
                                    class='text-danger'><i class="fa fa-trash"></i></a>
                            </div>

                            <div class="col">
                                <a title="Inutilizar" href='{{ route('mdfe.view', [$cpm->id]) }}'
                                    class='text-info'><i class="fa fa-trash"></i></a>
                            </div>

                            <div class="col">
                                <a title="Enviar" href='{{ route('mdfe.view', [$cpm->id]) }}'
                                    class='text-success'><i class="fa fa-upload"></i></a>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    @endcomponent
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
@stop
