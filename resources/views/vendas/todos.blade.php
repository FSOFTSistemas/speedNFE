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
        <th width="10%">Nº</th>
        <th width="20%">CLIENTE</th>
        <th width="10%">VALOR</th>
        <th width="10%">DATA</th>
        <th width="10%">ESTADO</th>
        <th width="20%">EMPRESA</th>
        <th width="20%">AÇÕES</th>
    </thead>
    <tbody style="text-align: center">
        @foreach ($pedidos as $pedido)
        <tr>
            <td>{{ $pedido->numero_nfe }}</td>
            <td>{{ $pedido->nome }}</td>
            <td>R${{ number_format($pedido->total, 2, ',', '.') }}</td>
            <td>{{ date('d/m/Y', strtotime($pedido->data)) }}</td>
            <td>{{ $pedido->estado }}</td>
            <td>{{ $pedido->fantasia }}</td>
            <td>
                <div class="row">
                    @if ($pedido->estado == 'Pendente' || $pedido->estado == 'Rejeitado')
                    <div class="col-md-3 col-xs-2">
                        <a target="_blank" href="{{ route('vendas.show', [$pedido->id]) }}" title="Visualizar" class="text-primary"><i class="fa fa-eye"></i></a>
                    </div>

                    {{-- EDITAR --}}
                    <div class="col-md-3 col-xs-2">
                        <a href="{{ route('vendas.editar', [$pedido->id]) }}" title="Editar" class="text-info"><i class="fa fa-edit"></i></a>
                    </div>

                    {{-- EXCLUIR --}}
                    <div class="col-md-3 col-xs-2">
                        <a title="Excluir" onclick="setaDadosExcluir({{ $pedido->id }});" class="text-danger"><i class="fa fa-trash" data-toggle="modal" data-target="#excluir"></i></a>
                    </div>

                    <div class="modal fade" id="excluir" tabindex="-1" role="dialog" aria-labelledby="excluirLabel" aria-hidden="true">
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
                    {{-- EMITIR --}}
                    <div class="col-md-3 col-xs-2">
                        <a href="{{ route('enviarXML', ['id' => $pedido->id]) }}" onclick="loadPage()" title="Enviar NFe" class="text-success"><i class="fas fa-upload"></i></a>
                    </div>
                    @elseif($pedido->estado == 'Autorizado')
                    @if ($pedido->sequencia_evento == 0)
                    <div class="col-md-3 col-xs-2">
                        <a target="_blank" href="{{ route('imprimirXML', [$pedido->id]) }}" title="Visualizar" class="text-primary"><i class="fa fa-eye"></i></a>
                    </div>
                    <div class="col-md-3 col-xs-2">
                        <a title="Carta de Correção" href="#">
                            <i class="fa fa-envelope text-warning" data-toggle="modal" data-target="#cceModal"></i>
                        </a>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="cceModal" tabindex="-1" role="dialog" aria-labelledby="cceModalLabel" aria-hidden="true">
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
                                        <input type="hidden" value="{{ $pedido->id }}" name="venda_id" id="venda_id">
                                        <textarea name="justificativa" id="justificativa" class="form-control" cols="30" rows="6" required minlength="15" placeholder="Informe a justificativa para solicitar a carta de correção... (mínimo de 15 dígitos)"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                        <button type="submit" onclick="loadPage()" class="btn btn-primary">Enviar CCe</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="col-md-3 col-xs-2">
                        <a target='_blank' title="Imprimir CCe" href="/venda/cce/{{ $pedido->id }}" class="text-dark"><i class="fa fa-print"></i></a>
                    </div>
                    @endif

                    <div class="col-md-3 col-xs-2">
                        <a title="Cancelar" href="#">
                            <i data-toggle="modal" data-target="#exampleModal" class="fa fa-ban text-danger"></i>
                        </a>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                        <input type="hidden" value="{{ $pedido->id }}" name="venda_id" id="venda_id">
                                        <textarea class="form-control" name="justificativa" id="justificativa" cols="30" rows="6" required minlength="15" placeholder="Informe a justificativa para solicitar o cancelamento... (mínimo de 15 dígitos)"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                        <button type="submit" onclick="loadPage()" class="btn btn-primary">Enviar
                                            Cancelamento</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="col-md-3 col-xs-2">
                        <a target='_blank' title="Imprimir Cancelamento" href="{{ route('imprimirCancelamentoXML', ['id' => $pedido->id]) }}" class="text-dark"><i class="fa fa-print"></i></a>
                    </div>
                    @endif
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="load" tabindex="-1" aria-labelledby="loadLabel" aria-hidden="true" >
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
</style>
<link rel="stylesheet" href="{{ asset('css/loaging.css') }}">
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script>
    $(document).ready(function() {
        $('#notas').DataTable({
            responsive: true,
            "ordering": false,
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
