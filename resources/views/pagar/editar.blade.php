@extends('layouts.app')
@section('content')
    <div class="container">
        <form action="{{ route('atualizar_pagamento', ['id' => $recebimento->id]) }}" method="post">
            @csrf
            <div for="descricao"><label>DESCRICAO</label><br>
            <input class="form-control" type="text" name="descricao" id="descricao" value="{{ $recebimento->descricao }}"></div>
            <div for="valor_cheio"><label>VALOR</label><br>
            <input class="form-control" type="number" step="0.01" name="valor" id="valor" value="{{ $recebimento->valor }}"></div>
            <div for="valor_vazio"><label>VENCIMENTO</label><br>
            <input class="form-control" type="date" name="vencimento" id="vencimento" value="{{ $recebimento-> vencimento}}"></div>
            <div for="valor_vazio"><label>SITUACAO</label><br>
            <input class="form-control" type="number" name="situacao" id="situacao" value="{{ $recebimento->situacao }}"></div>
            <button class="btn btn-success" type="submit">Salvar</button>
        </form>
    </div>
@endsection