@extends('layouts.app')
@section('content')
    <div class="container">
        <div>
            <a class="btn btn-primary" href="/pagar/new">Cadastrar nova Conta a Pagar</a>
        </div>
        <br>
        <table class="table table-striped">
            <tr>
                <th>USUARIO</th>
                <th>DESCRICAO</th>
                <th>VALOR</th>
                <th>VENCIMENTO</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>

        @foreach($recebimentos as $recebimento)
            <tr>
                <td>{{$recebimento->user_id}}</td>
                <td>{{$recebimento->descricao}}</td>
                <td>{{$recebimento->valor}}</td>
                <td>{{$recebimento->vencimento}}</td>
                <td>{{$recebimento->situacao}}</td>
                <td><a class="btn btn-warning" href="{{ route('editar_pagamento', ['id'=>$recebimento->id]) }}" title="Editar pagamento {{ $recebimento->descricao }}">Editar</a></td>
                <td><a class="btn btn-danger" href="{{ route('deletar_pagamento', ['id'=>$recebimento->id]) }}" title="Excluir pagamento {{ $recebimento->descricao }}">Excluir</a></td>
            </tr>
        @endforeach
        </table>
    </div>
@endsection