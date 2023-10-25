@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="text-center">
        <h3 class="m-0 text-black" width="100%"><b>Resumo de Notas</b></h3>
    </div>
@stop

@section('content')
    <p><a href="/vendas/nova" class="btn btn-info">&nbsp; + Nova NFe &nbsp;</a></p>

    <table class="table table-striped">
        <thead>
            <th>CLIENTE</th>
            <th>VALOR</th>
            <th>N. NFE</th>
            <th>CHAVE</th>
            <th>STATUS</th>
            <th>EMPRESA</th>
            <th></th>
            <th></th>
        </thead>
        <tbody>
            @foreach($pedidos as $pedido)
                <tr>
                    <td>{{$pedido->nome}}</td>
                    <td>R${{number_format($pedido->total, 2, ',', '.')}}</td>
                    <td>{{$pedido->numero_nfe}}</td>
                    <td><a target='_blank' href="{{route('imprimirXML', ['id' => $pedido->id])}}">{{$pedido->chave}}</a></td>
                    <td>{{$pedido->estado}}</td>
                    <td>{{$pedido->fantasia}}</td>
                    @if($pedido->chave == '')
                      <td><a href="/visualizar/{{$pedido->id}}" class="btn btn-success" >Visualizar</a></td>
                    @else

                    @endif
                    @if($pedido->estado == 'Novo' || $pedido->estado == "Rejeitado")
                        <td><a href="{{route('enviarXML', ['id' => $pedido->id])}}" class="btn btn-success" >Enviar NFe</a></td>
                    @elseif($pedido->estado == "Aprovado")
                      @if($pedido->sequencia_evento == 0)
                        <td><button type="button" class="btn btn-warning" data-toggle="modal" data-target="#cceModal">
                          Carta de Correção
                        </button></td>

                        <!-- Modal -->
                        <div class="modal fade" id="cceModal" tabindex="-1" role="dialog" aria-labelledby="cceModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <form action="{{route("cartaCorrecao")}}" method="post">
                                @csrf
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Justificativa CCe</h5>
                                </div>
                                <div class="modal-body">
                                  <input type="hidden" value="{{$pedido->id}}" name="venda_id" id="venda_id">
                                  <input type="text-area" class="form-control" name="justificativa" id="justificativa">
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                  <button type="submit" class="btn btn-primary">Enviar CCe</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      @else
                      <td><a href="/venda/cce/{{$pedido->id}}" class="btn btn-info">Imprimir CCe</a></td>
                      @endif

                        <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">
                          Cancelar
                        </button></td>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <form action="{{route("cancelar")}}" method="post">
                                @csrf
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Justificativa</h5>
                                </div>
                                <div class="modal-body">
                                  <input type="hidden" value="{{$pedido->id}}" name="venda_id" id="venda_id">
                                  <input type="text-area" class="form-control" name="justificativa" id="justificativa">
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                  <button type="submit" class="btn btn-primary">Cancelar</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                    @else
                        <td><a target='_blank' href="{{route('imprimirCancelamentoXML', ['id' => $pedido->id])}}" class="btn btn-success">Imprimir</a></td>
                        <td></td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
      <div class="col-10"></div>
      <div class="col-2">{{$pedidos->links()}}</div>
    </div>


    <!-- Button trigger modal -->


@endsection
