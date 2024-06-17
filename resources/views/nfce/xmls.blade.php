@extends('adminlte::page')

@section('title', 'XMLS')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3>XMLS de NFCe</h3>
        </div>
    </div>
@stop

@section('content')
    <div class="row" style="margin-bottom: 2%">
        <div class="col">
            <a class="btn btn-info text-light" href="{{ route('nfce.showUnuser') }}">Inutilizar Faixa</a>
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
                'targets' => -1,
            ],
        ],
        'searching' => true,
        'lengthChange' => true,
    ])
        <thead class="table-primary">
            <tr>
                <th>Nº</th>
                <th>Data</th>
                <th>Chave</th>
                <th>Cliente</th>
                <th>Cupom</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            @foreach ($nfces as $nfce)
                <tr>
                    <td>{{ $nfce->nro }}</td>
                    <td>{{ $nfce->data }}</td>
                    <td><a class="text-decoration-none" title="Visualizar" target="_blank" href="{{ route('nfce.show', [$nfce->id]) }}">{{ $nfce->chave }}</a></td>
                    <td>{{ $nfce->cupom->cliente->nome ?? 'CONSUMIDOR FINAL' }}</td>
                    <td>{{ $nfce->cupom->nroCupom }}</td>
                    <td>
                        <div class="row">
                            <div class="col">
                                <a title="Download"
                                    href='{{ route('nfce.downloadXml', [$nfce->id]) }}' class='text-primary'><i
                                        class="far fa-file-code text-success"></i></a>
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
