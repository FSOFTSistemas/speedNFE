@extends('adminlte::page')

@section('title', 'Notas Emitidas')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3>Resumo de Notas MDFe</h3>
        </div>
    </div>
@stop

@section('content')

<div class="row" style="margin-bottom: 2%">
    <div class="col">
        <a class="btn btn-info" href="{{ route('mdfe.create') }}">+ Emitir MDFe</a>
    </div>
</div>

    <div class="container">
        <table class="table table-hover" id="mdfes">
            <thead class="table-primary">
                <tr>
                    <th>Nº</th>
                    <th>Série</th>
                    <th>Data de emissão</th>
                    <th>Situação</th>
                    <th>UF início</th>
                    <th>UF término</th>
                    <th>UF de percurso</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach ($mdfes as $mdfe)
                    <tr>
                        <td>{{ $mdfe->numero }}</td>
                        <td>{{ $mdfe->serie }}</td>
                        <td>{{ $mdfe->data }}</td>
                        <td>{{ $mdfe->situacao }}</td>
                        <td>{{ $mdfe->uf_inicio }}</td>
                        <td>{{ $mdfe->uf_termino }}</td>
                        <td>{{ $mdfe->uf_percurso }}</td>
                        <td>
                            <div class="row">
                                <div class="col">
                                    <a title="Editar" href='{{ route('veiculos.edit', [$mdfe->id]) }}' class='text-warning'><i
                                            class="fa fa-edit"></i></a>
                                </div>
                                <div class="col">
                                    <a title="Excluir" onclick="setaDadosModal({{ $mdfe->id }})" class='text-danger'><i
                                            class="fa fa-trash" data-toggle="modal"
                                            data-target=".bd-delete-modal-lg"></i></a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade bd-delete-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">

                    <div class="col" style="text-align: center">
                        <div class="modal-title" style="text: center">
                            <h4>Apagar esta Nota ?</h4>
                        </div>
                    </div>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <p style="color: red; text-align: center">OBS: Você irá excluir todas as informações sobre esta nota!
                    </p>

                    <div class="" style="text-align: center">

                        <form action="{{ route('veiculos.delete') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('DELETE')
                            <div class="form-group">
                                <input type="hidden" step="0.01" class="form-control" id="mdfeId" name="mdfeId"
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
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script>
        function setaDadosModal(mdfeId) {
            document.getElementById('mdfeId').value = mdfeId;
        }

        $(document).ready(function() {
            $('#mdfes').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
                },
            });
        });
    </script>
@stop
