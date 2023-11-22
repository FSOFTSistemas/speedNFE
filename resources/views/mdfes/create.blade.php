@extends('adminlte::page')

@section('title', 'Emitir MDFe')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3>Emitir MDFe</h3>
        </div>
    </div>
@stop

@section('content')

    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="row" style="text-align: center">
                    <div class="col">
                        <h2>Informações da MDFe</h2>
                    </div>
                </div>
            </div>

            <div class="card-body">

                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col">
                            <div class="form-group">

                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <div class="modal fade bd-delete-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        id="meuModal" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">

                    <div class="col" style="text-align: center">
                        <div class="modal-title" style="text: center">
                            <h4>Adicionar Documento</h4>
                        </div>
                    </div>

                </div>
                <div class="modal-body">

                    <div class="container">

                        <form action="" method="GET">

                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="">Tipo de Documento *</label>
                                        <select class="form-control" name="tipo_documento" id="tipo_documento" required>
                                            <option value="">-- Selecione um tipo de Documento --</option>
                                            @foreach ($tiposDocumentos as $tipoDocumento)
                                                <option value="{{ $tipoDocumento }}">{{ $tipoDocumento->value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-group">
                                        <label for="">Local de descarregamento *</label>
                                        <select class="form-control" name="local_descarregamento" id="local_descarregamento"
                                            required>
                                            <option value="">-- Selecione um Local --</option>
                                            @foreach ($ufs as $uf)
                                                <option value="{{ $uf }}">{{ $uf->value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-group">
                                        <label for="">Cidade *</label>
                                        <input class="form-control" type="text" name="cidade" id="cidade"
                                            placeholder="Cidade..." required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="">Valor Total *</label>
                                        <input class="form-control" type="number" step="0.01" name="valor_total"
                                            id="valor_total" placeholder="Valor..." required>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-group">
                                        <label for="">Peso (Kg) *</label>
                                        <input class="form-control" type="number" step="0.1" name="peso"
                                            id="peso" placeholder="Peso..." required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="">Chave de acesso*</label>
                                        <input class="form-control" type="number" name="chave_acesso" id="chave_acesso"
                                            minlength="44" placeholder="Chave..." required>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="text-align: center">
                                <div class="col">
                                    <button class="btn btn-success" type="submit" style="width: 25%"
                                        onclick="setaDadosMDFe();">Salvar</button>
                                </div>
                            </div>

                        </form>

                    </div>

                </div>

                <div class="modal-footer">
                    <a class="btn btn-secondary" href="{{ route('mdfe.index') }}">Cancelar</a>
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
        $(document).ready(function() {
            $('#meuModal').modal('show');
        });

        function setaDadosMDFe(tipoDocumento, local, cidade, valorTotal, peso, chave) {
            if (tipoDocumento && local && cidade && valorTotal && peso && chave) {
                alert(tipoDocumento);
            }
        }
    </script>
@stop
