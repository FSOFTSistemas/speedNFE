@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3 class="m-0 text-black" width="100%">Resumo de Notas</h3>
        </div>
    </div>
@stop

@section('content')
    <p><a href="/vendas/nova" class="btn btn-info">&nbsp; + Nova NFe &nbsp;</a></p>

    <table class="table table-hover" id="notas">
        <thead class="table-primary" style="text-align: center">
            <th>Nº</th>
            <th>CLIENTE</th>
            <th>VALOR</th>
            <th>CHAVE</th>
            <th>STATUS</th>
            <th>EMPRESA</th>
            <th></th>
            <th></th>
        </thead>
        <tbody style="text-align: center">
            @foreach ($pedidos as $pedido)
                <tr>
                    <td>{{ $pedido->numero_nfe }}</td>
                    <td>{{ $pedido->nome }}</td>
                    <td>R${{ number_format($pedido->total, 2, ',', '.') }}</td>
                    <td><a target='_blank' href="{{ route('imprimirXML', ['id' => $pedido->id]) }}">{{ $pedido->chave }}</a>
                    </td>
                    <td>{{ $pedido->status == 0 ? "Autorizada" : "Rejeitada" }}</td>
                    <td>{{ $pedido->fantasia }}</td>
                    @if ($pedido->chave == '')
                        <td><a href="/visualizar/{{ $pedido->id }}" title="Visualizar" class="text-primary"><i
                                    class="fa fa-eye"></i></a></td>
                    @else
                    @endif
                    @if ($pedido->estado == 'Novo' || $pedido->estado == 'Rejeitado')
                        <td><a href="{{ route('enviarXML', ['id' => $pedido->id]) }}" title="Enviar NFe"
                                class="text-success"><i class="fas fa-upload"></i></a>
                        </td>
                    @elseif($pedido->estado == 'Aprovado')
                        @if ($pedido->sequencia_evento == 0)
                            <td><a title="Carta de Correção" href="#">
                                    <i class="fa fa-envelope text-warning" data-toggle="modal" data-target="#cceModal"></i>
                                </a></td>

                            <!-- Modal -->
                            <div class="modal fade" id="cceModal" tabindex="-1" role="dialog"
                                aria-labelledby="cceModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('cartaCorrecao') }}" method="post">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Justificativa CCe</h5>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" value="{{ $pedido->id }}" name="venda_id"
                                                    id="venda_id">
                                                <input type="text-area" class="form-control" name="justificativa"
                                                    id="justificativa">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Fechar</button>
                                                <button type="submit" class="btn btn-primary">Enviar CCe</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <td><a href="/venda/cce/{{ $pedido->id }}" class="btn btn-info">Imprimir CCe</a></td>
                        @endif

                        <td><a title="Cancelar" href="#">
                                <i data-toggle="modal" data-target="#exampleModal" class="fa fa-ban text-danger"></i>
                            </a></td>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('cancelar') }}" method="post">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Justificativa</h5>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" value="{{ $pedido->id }}" name="venda_id"
                                                id="venda_id">
                                            <input type="text-area" class="form-control" name="justificativa"
                                                id="justificativa">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Fechar</button>
                                            <button type="submit" class="btn btn-primary">Cancelar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <td><a target='_blank' href="{{ route('imprimirCancelamentoXML', ['id' => $pedido->id]) }}"
                                class="btn btn-success"><i class="fa fa-print"></i></a></td>
                        <td></td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

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
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
                },
            });
        });
    </script>
@stop
