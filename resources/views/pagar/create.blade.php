@extends('layouts.app')
@section('content')
    <div class="container">
        <form action="{{ route('salvar_pagamento') }}" method="post">
            @csrf
            <div for="descricao">
            <input class="form-control" type="text" name="descricao" id="descricao" placeholder="Descrição"></div>
            <div for="valor">
            <input class="form-control" type="number" step="0.01" name="valor" id="valor" placeholder="Valor"></div>
            <div for="vencimento">
            <input class="form-control" type="date" name="vencimento" id="vencimento"></div>
            <button class="btn btn-success" type="submit">Salvar</button>
        </form>
    </div>
@endsection
