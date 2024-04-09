@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3 class="m-0 text-black" width="100%">Resumo de Notas NFe</h3>
        </div>
    </div>


@stop

@section('content')
    <p><a href="/vendas/nova" class="btn btn-info">&nbsp; + Nova NFe &nbsp;</a></p>

    <table class="table table-hover" id="notas">
        <thead class="table-primary" style="text-align: center">
            <th width="5%">Nº</th>
            <th width="20%">CLIENTE</th>
            <th width="10%">VALOR</th>
            <th width="10%">DATA</th>
            <th width="5%">ESTADO</th>
            @if($empresa == 1)
            <th width="25%">EMPRESA</th>
            @endif
            <th width="25%">AÇÕES</th>
        </thead>
        <tbody>
            @foreach ($pedidos as $index => $pedido)
                <tr>
                    <td style="font-size: 80%">{{ $pedido->numero_nfe }}</td>
                    <td style="font-size: 80%">{{ $pedido->nome }}</td>
                    <td style="font-size: 80%">R${{ number_format($pedido->total, 2, ',', '.') }}</td>
                    <td style="font-size: 80%">{{ date('d/m/Y', strtotime($pedido->data)) }}</td>
                    <td style="font-size: 80%; position: relative;">
                        <span class="<?php echo $pedido->estado == 'Pendente' ? 'pendente' : ($pedido->estado == 'Autorizado' ? 'autorizado' : 'cancelado'); ?>" id="estado">{{ $pedido->estado }}
                        </span>
                    </td>
                    @if($empresa == 1)
                    <td style="font-size: 80%">{{ $pedido->fantasia }}</td>
                    @endif
                    <td>
                        @if ($pedido->estado == 'Pendente' || $pedido->estado == 'Rejeitado')
                            <div class="row">
                                <div class="col-md-3 col-xs-6">
                                    <a target="_blank" href="{{ route('vendas.show', [$pedido->id]) }}" title="Visualizar"
                                        class="text-primary">
                                        <button class="btn btn-primary form-control d-block d-sm-none"
                                            style="margin-bottom: 1%">Visualizar</button>
                                        <i class="fa fa-eye d-none d-sm-block"></i>
                                    </a>
                                </div>
                                <div class="col-md-3 col-xs-6">
                                    <a href="{{ route('vendas.editar', [$pedido->id]) }}" title="Editar" class="text-info">
                                        <button class="btn btn-info form-control d-block d-sm-none"
                                            style="margin-bottom: 1%">Editar</button>
                                        <i class="fa fa-edit d-none d-sm-block"></i>
                                    </a>
                                </div>
                                <div class="col-md-3 col-xs-6">
                                    <a title="Excluir" onclick="setaDadosExcluir({{ $pedido->id }});" class="text-danger">
                                        <button class="btn btn-danger form-control d-block d-sm-none" data-toggle="modal"
                                            data-target="#excluir" style="margin-bottom: 1%">Excluir</button>
                                        <i class="fa fa-trash d-none d-sm-block" data-toggle="modal"
                                            data-target="#excluir"></i>
                                    </a>
                                </div>
                                <div class="col-md-3 col-xs-6">
                                    <a href="{{ route('enviarXML', ['id' => $pedido->id]) }}" onclick="loadPage()"
                                        title="Enviar NFe" class="text-success">
                                        <button class="btn btn-success form-control d-block d-sm-none">Enviar NFe</button>
                                        <i class="fas fa-upload d-none d-sm-block"></i>
                                    </a>
                                </div>
                            </div>
                        @elseif($pedido->estado == 'Autorizado')
                            <div class="row">
                                @if ($pedido->sequencia_evento == 0)
                                    <div class="col-md-3 col-xs-6">
                                        <a target="_blank" href="{{ route('imprimirXML', [$pedido->id]) }}"
                                            title="Visualizar" class="text-primary">
                                            <button class="btn btn-primary form-control d-block d-sm-none"
                                                style="margin-bottom: 1%">Visualizar</button>
                                            <i class="fa fa-eye d-none d-sm-block"></i>
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-xs-6">
                                        <a title="Carta de Correção" href="#">
                                            <button class="btn btn-warning form-control d-block d-sm-none"
                                                style="margin-bottom: 1%">CCe</button>
                                            <i class="text-danger d-none d-sm-block" data-toggle="modal"
                                                data-target="#cceModal"><b>CCe</b></i>
                                        </a>
                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="cceModal" tabindex="-1" role="dialog"
                                        aria-labelledby="cceModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form action="{{ route('cartaCorrecao') }}" method="post">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <div class="row">
                                                            <div class="col">
                                                                <h5 id="exampleModalLabel">Justificativa de CCe</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" value="{{ $pedido->id }}" name="venda_id"
                                                            id="venda_id">
                                                        <textarea name="justificativa" id="justificativa" class="form-control" cols="30" rows="6" required
                                                            minlength="15" placeholder="Informe a justificativa para solicitar a carta de correção... (mínimo de 15 dígitos)"></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Fechar</button>
                                                        <button type="submit" onclick="loadPage()"
                                                            class="btn btn-primary">Enviar CCe</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-md-3 col-xs-6">
                                        <a target='_blank' title="Imprimir CCe" href="/venda/cce/{{ $pedido->id }}"
                                            class="text-dark">
                                            <button class="btn btn-dark form-control d-block d-sm-none"
                                                style="margin-bottom: 1%">Imprimir CCe</button>
                                            <i class="fa fa-print d-none d-sm-block"></i>
                                        </a>
                                    </div>
                                @endif

                                <div class="col-md-3 col-xs-6">
                                    <a title="Cancelar" href="#">
                                        <button class="btn btn-danger form-control d-block d-sm-none">Cancelar</button>
                                        <i data-toggle="modal" data-target="#exampleModal"
                                            class="fa fa-ban text-danger d-none d-sm-block"></i>
                                    </a>
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('cancelar') }}" method="post">
                                                @csrf
                                                <div class="modal-header">
                                                    <div class="row">
                                                        <div class="col">
                                                            <h5 class="modal-title" id="exampleModalLabel">Justificativa
                                                                de
                                                                Cancelamento</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" value="{{ $pedido->id }}" name="venda_id"
                                                        id="venda_id">
                                                    <textarea class="form-control" name="justificativa" id="justificativa" cols="30" rows="6" required
                                                        minlength="15" placeholder="Informe a justificativa para solicitar o cancelamento... (mínimo de 15 dígitos)"></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Fechar</button>
                                                    <button type="submit" onclick="loadPage()"
                                                        class="btn btn-primary">Enviar
                                                        Cancelamento</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-3 col-xs-6">
                                    <a target='_blank' title="Imprimir Cancelamento"
                                        href="{{ route('imprimirCancelamentoXML', ['id' => $pedido->id]) }}"
                                        class="text-dark">
                                        <button class="btn btn-dark form-control d-block d-sm-none">Imprimir
                                            Cancelamento</button>
                                        <i class="fa fa-print d-none d-sm-block"></i>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="modal fade" id="excluir" tabindex="-1" role="dialog" aria-labelledby="excluirLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('pedido.deletar') }}" method="post">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <div class="row">
                            <div class="col">
                                <h5 id="exampleModalLabel">Deletar Pedido</h5>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <h5 class="text-danger">Tem certeza que deseja deletar o pedido? Isso
                            irá excluir todas as informações sobre o mesmo!</h5>
                        <input type="hidden" value="" name="pedido_id" id="pedido_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="submit" onclick="loadPage()" class="btn btn-warning">Deletar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="load" tabindex="-1" aria-labelledby="loadLabel" aria-hidden="true">
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

@endsection

@section('css')
    <style>
        .bloqueado {
            opacity: 0.5;
            pointer-events: none;
        }

        .pendente {
            background-color: orange;

        }

        .autorizado {
            background-color: green;
        }

        .cancelado {
            background-color: red;
        }

        #estado {
            position: relative;
            padding: 2px 8px;
            border-radius: 50px;
            color: #ffffff;
            /* Espaçamento interno para manter a borda longe do texto */
        }

        #estado:before {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            /* Largura do retângulo */
            height: 100%;
            /* Altura do retângulo */
            border-color: inherit;
            /* Usa a mesma cor do texto para a borda */
            box-sizing: border-box;
            /* Mantém o tamanho da borda dentro do retângulo */
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/loading.css') }}">
    <link
        href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.1/b-3.0.0/b-colvis-3.0.0/b-html5-3.0.0/b-print-3.0.0/cr-2.0.0/date-1.5.2/r-3.0.0/sr-1.4.0/datatables.min.css"
        rel="stylesheet">
@endsection

@section('js')
    <script
        src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.1/b-3.0.0/b-colvis-3.0.0/b-html5-3.0.0/b-print-3.0.0/cr-2.0.0/date-1.5.2/r-3.0.0/sr-1.4.0/datatables.min.js">
    </script>

    <script>
        $(document).ready(function() {
            var tabela = $('#notas').DataTable({
                responsive: true,
                ordering: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
                },
            });
        });

        function setaDadosExcluir($id) {
            document.getElementById('pedido_id').value = $id;
        }

        function loadPage() {
            var botoes = document.getElementsByTagName("a");
            for (var i = 0; i < botoes.length; i++) {
                bloquearBotao(botoes[i]);
            }

            var myModal = new bootstrap.Modal(document.getElementById('load'), {
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
