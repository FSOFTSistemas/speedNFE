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
            <a class="btn btn-primary" data-toggle="modal" data-target="#modalImportarNFe">Importar NFe</a>
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

    @component('components.modal', ['modalId' => 'modalImportarNFe', 'modalTitle' => 'Importar NFe', 'sizeModal' => 'modal-md'])
        @component('components.custom-form', ['route' => 'entradas.index'])
            <div class="row mt-4">
                <div class="col">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="chaveNota" name="chaveNota" placeholder="" minlength="44"
                            maxlength="44" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                        <label for="chaveNota">Chave da NFe</label>
                        <div id="passwordHelpBlock" class="form-text">
                            A chave deve ter 44 caracteres, contém apenas números e não deve conter espaços, caracteres especiais ou
                            emoji.
                        </div>
                        <div class="invalid-feedback">
                            Chave inválida, tente novamente!
                        </div>
                    </div>
                </div>
            </div>

            <div class="position-relative">
                <button type="submit"
                    class="w-25 btn btn-outline-success btn-lg position-relative top-50 start-50 translate-middle">Importar</button>
            </div>
        @endcomponent
    @endcomponent
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}
@stop
