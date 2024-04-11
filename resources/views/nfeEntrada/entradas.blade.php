@extends('adminlte::page')

@section('title', 'Entradas')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h4 class="text-dark">Entradas</h4>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <a href="{{ route('produto.index') }}" class="btn btn-primary">Importar NFe</a>
        </div>
        <div class="col" style="text-align: end">
            <a href="{{ route('produto.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
@stop

@section('content')
    <main>
        <section>
            @component('components.dataTable')
                <thead>
                    <tr>
                        <th>Emissão</th>
                        <th>Entrada</th>
                        <th>Nº</th>
                        <th>Fornecedor</th>
                        <th>Chave</th>
                        <th>Valor</th>
                        <th>Empresa</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($entradas as $etd)
                        <tr>
                            <td>{{ date('d/m/Y', strtotime($etd->dataEmissao)) }}</td>
                            <td>{{ date('d/m/Y', strtotime($etd->dataEntrada)) }}</td>
                            <td>{{ $etd->numeroNota }}</td>
                            <td>{{ $etd->fornecedor }}</td>
                            <td>{{ $etd->chave }}</td>
                            <td>R$ {{ number_format($etd->valor, 2) }}</td>
                            <td>{{ $etd->empresa_id }}</td>
                        </tr>
                    @endforeach
                </tbody>
            @endcomponent
        </section>
    </main>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}
@stop
