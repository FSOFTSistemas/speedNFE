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
            <a class="btn btn-info text-light" href="{{ route('mdfe.create') }}">+ Emitir MDFe</a>
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
    ])
        <thead class="table-primary">
            <tr>
                <th>Nº</th>
                <th>Série</th>
                <th>Data de emissão</th>
                <th>Situação</th>
                <th>UF início</th>
                <th>UF término</th>
                <th>UF de percurso</th>
                @if ($empresa == 1)
                    <th>Empresa</th>
                @endif
                <th></th>
            </tr>
        </thead>

        <tbody>
            @foreach ($mdfes as $mdfe)
                <tr>
                    <td>{{ $mdfe->numero }}</td>
                    <td>{{ $mdfe->serie }}</td>
                    <td>{{ date('d/m/Y', strtotime($mdfe->data)) }}</td>
                    <td>{{ $mdfe->situacao }}</td>
                    <td>{{ $mdfe->uf_inicio }}</td>
                    <td>{{ $mdfe->uf_termino }}</td>
                    <td>{{ $mdfe->uf_percurso }}</td>
                    @if ($empresa == 1)
                        <td>{{ $mdfe->fantasia }}</td>
                    @endif
                    <td>
                        <div class="row">
                            @if ($mdfe->situacao->value === 'Pendente' || $mdfe->situacao->value === 'Rejeitado')
                                <div class="col">
                                    <a title="Visualizar" target="_blank" href='{{ route('mdfe.view', [$mdfe->id]) }}'
                                        class='text-primary'><i class="fa fa-eye"></i></a>
                                </div>
                                <div class="col">
                                    <a title="Editar" href='{{ route('mdfe.edit', [$mdfe->id]) }}' class='text-warning'><i
                                            class="fa fa-edit"></i></a>
                                </div>
                                <div class="col">
                                    <a title="Excluir" onclick="setaDadosModalExcluir({{ $mdfe->id }})"
                                        class='text-danger'><i class="fa fa-trash" data-toggle="modal"
                                            data-target=".bd-delete-modal-lg"></i></a>
                                </div>
                                <div class="col">
                                    <a title="Enviar MDFe" onclick="loadPage()" href="{{ route('mdfe.enviar', [$mdfe->id]) }}"
                                        class='text-success'><i class="fa fa-upload"></i></a>
                                </div>
                            @elseif ($mdfe->situacao->value === 'Autorizado')
                                <div class="col">
                                    <a title="Cancelar" onclick="setaDadosModalCancelar({{ $mdfe->id }})"
                                        class='text-danger'><i class="fa fa-ban" data-toggle="modal"
                                            data-target=".bd-cancel-modal-lg"></i></a>
                                </div>
                                <div class="col">
                                    <a title="Encerrar" onclick="loadPage()" href='{{ route('mdfe.close', [$mdfe->id]) }}'
                                        class='text-info'><i class="fas fa-truck-loading"></i></a>
                                </div>
                                <div class="col">
                                    <a title="Imprimir" target="_blank" href='{{ route('mdfe.print', [$mdfe->id, 0]) }}'
                                        class='text-dark'><i class="fa fa-print"></i></a>
                                </div>
                                <div class="col">
                                    <a title="Baixar" onclick="loadPage()" href="{{ route('mdfe.downloadXML', [$mdfe->id]) }}"
                                        class="text-primary"><i class="fas fa-download"></i></a>
                                </div>
                            @elseif ($mdfe->situacao->value === 'Encerrado')
                                <div class="col">
                                    <a title="Imprimir Encerramento" target="_blank"
                                        href='{{ route('mdfe.print', [$mdfe->id, 1]) }}' class='text-dark'><i
                                            class="fa fa-print"></i></a>
                                </div>
                                <div class="col">
                                    <a title="Baixar" onclick="loadPage()" href="{{ route('mdfe.downloadXML', [$mdfe->id]) }}"
                                        class="text-primary"><i class="fas fa-download"></i></a>
                                </div>
                            @else
                                <div class="col">
                                    <a title="Imprimir Cancelamento" target="_blank"
                                        href='{{ route('mdfe.print', [$mdfe->id, 2]) }}' class='text-dark'><i
                                            class="fa fa-print"></i></a>
                                </div>
                                <div class="col">
                                    <a title="Baixar" onclick="loadPage()" href="{{ route('mdfe.downloadXML', [$mdfe->id]) }}"
                                        class="text-primary"><i class="fas fa-download"></i></a>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    @endcomponent

    {{-- MODAL PARA EXCLUIR --}}
    <div class="modal fade bd-delete-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">

                    <div class="col" style="text-align: center">
                        <div class="modal-title" style="text: center">
                            <h4>Apagar esta Nota?</h4>
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

                        <form action="{{ route('mdfe.delete') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('DELETE')
                            <div class="form-group">
                                <input type="hidden" class="form-control" id="mdfeId" name="mdfeId"
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

    {{-- MODAL PARA CANCELAR --}}
    <div class="modal fade bd-cancel-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">

                    <div class="col" style="text-align: center">
                        <div class="modal-title" style="text: center">
                            <h4>Cancelar esta Nota?</h4>
                        </div>
                    </div>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <p style="color: red; text-align: center">OBS: Você irá cancelar esta nota!
                    </p>

                    <div class="" style="text-align: center">

                        <form action="{{ route('mdfe.cancel') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <label for="">Justificativa *</label>
                                    <textarea class="form-control" required name="justificativa" id="justificativa" placeholder="Justificativa..."
                                        maxlength="255" cols="10" rows="8"></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <input type="hidden" required class="form-control" id="mdfe_id" name="mdfe_id"
                                    value="">
                            </div>

                            <div class="text-center">
                                <button type="submit" style="width: 50%;" onclick="loadPage()"
                                    class="btn btn-warning">CONFIRMAR</button>
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

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5" id="exampleModalLabel" style="text-align: center;">Aguarde...</h3>
                </div>
                <div class="modal-content" style="min-height: 200px;">
                    <div class="banter-loader">
                        <div class="banter-loader__box"></div>
                        <div class="banter-loader__box"></div>
                        <div class="banter-loader__box"></div>
                        <div class="banter-loader__box"></div>
                        <div class="banter-loader__box"></div>
                        <div class="banter-loader__box"></div>
                        <div class="banter-loader__box"></div>
                        <div class="banter-loader__box"></div>
                        <div class="banter-loader__box"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .bloqueado {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
@stop

@section('js')
    <script>
        function setaDadosModalExcluir(mdfeId) {
            document.getElementById('mdfeId').value = mdfeId;
        }

        function setaDadosModalCancelar(mdfeId) {
            document.getElementById('mdfe_id').value = mdfeId;
        }

        function loadPage() {
            var botoes = document.getElementsByTagName("a");
            for (var i = 0; i < botoes.length; i++) {
                bloquearBotao(botoes[i]);
            }

            var myModal = new bootstrap.Modal(document.getElementById('exampleModal'), {
                keyboard: false,
                backdrop: 'static'

            });
            myModal.show();
        }

        function bloquearBotao(botao) {
            botao.disabled = true;
            botao.classList.add("bloqueado");
        }
    </script>
@stop
