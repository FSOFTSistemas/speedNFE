@extends('adminlte::page')

@section('title', 'Visualizar NFe')

@section('content_header')
    <div class="row">
        <div class="col" style="text-align: end">
            <a href="{{ route('entradas.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
@stop

@section('content')
    <main>
        <section>
            <header>
                <div class="row text-center">
                    <div class="col">
                        <h5>{{ $entrada->fornecedor }}</h5>
                    </div>
                </div>
            </header>
        </section>

        <section>
            @component('components.dataTable', [
                'responsive' => [
                    ['responsivePriority' => 1, 'targets' => 0],
                    ['responsivePriority' => 2, 'targets' => 1]
                ],
                'searching' => true,
                'lengthChange' => true,
                'pageLength' => 10,
                'ordering' => true
            ])
                <thead class="table-primary">
                    <tr>
                        <th>Produto</th>
                        <th>Qtde.</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($entrada->itensEntradas as $item)
                        <tr>
                            <td>{{ $item->produto->produto }}</td>
                            <td>{{ $item->qtde }}</td>
                        </tr>
                    @endforeach
                </tbody>
            @endcomponent
        </section>
    </main>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
@stop
