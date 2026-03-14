@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Contas a Receber</h1>
@stop

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('update_recebimento', ['id' => $recebimento->id]) }}" method="post">
                            @csrf
                            <div for="valor_cheio"><label>VALOR ORIGINAL</label><br>
                            <input class="form-control" disabled type="number" step="0.01" name="valor_original" id="valor_original" value="{{ $recebimento->valor_original }}"></div>
                            <div for="valor_cheio"><label>VALOR ATUAL</label><br>
                            <input class="form-control" disabled type="number" step="0.01" name="valor_atual" id="valor_atual" value="{{ $recebimento->valor_atual }}"></div>
                            <div for="valor_cheio"><label>VALOR RECEBIDO</label><br>
                            <input class="form-control" type="number" step="0.01" max="{{ $recebimento->valor_atual }}" name="valor_pago" id="valor_pago"></div>
                            <div for="valor_cheio"><label>NOVO VENCIMENTO</label><br>
                            <input class="form-control" type="date" step="0.01" value="{{$recebimento->vencimento}}" name="vencimento" id="vencimento"></div>

                            {{-- <div for="forma_pagamento">
                                <label>Forma de Pagamento</label>
                                <select class="form-control" name="forma_pagamento" id="forma_pagamento">
                                    <option>--escolha o meio de pagamento--</option>
                                    <option value="Cartao Credito">Cartão Crédito</option>
                                    <option value="Cartao Debito">Cartão Débito</option>
                                    <option value="Dinheiro">Dinheiro</option>
                                    <option value="PIX">PIX</option>
                                </select>
                            </div> --}}
                            <div class="text-center" style="padding-top: 2%">
                                <button class="btn btn-success" type="submit">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-2"></div>
        </div>
    </div>
@endsection