@extends('layouts.app')
@section('content')
    <div class="container">
        <form action="{{ route('salvar_produto') }}" method="post">
            @csrf
            <div for="descricao">
            <input class="form-control" type="text" name="descricao" id="descricao" placeholder="Descrição"></div>
            <div for="valor_cheio">
            <input class="form-control" type="number" step="0.01" name="valor_cheio" id="valor_cheio" placeholder="Valor Cheio"></div>
            <div for="valor_vazio">
            <input class="form-control" type="number" step="0.01" name="valor_vazio" id="valor_vazio" placeholder="Valor Vazio"></div>
            <div for="estoque_cheio">
            <input class="form-control" type="number" step="1" name="estoque_cheio" id="estoque_cheio" placeholder="Estoque Cheio"></div>
            <div for="estoque_vazio">
            <input class="form-control" type="number" step="1" name="estoque_vazio" id="estoque_vazio" placeholder="Estoque Vazio"></div>
            <div for="custo_cheio">
            <input class="form-control" type="number" step="1" name="custo_cheio" id="custo_cheio" placeholder="Custo Cheio"></div>
            <div for="custo_vazio">
            <input class="form-control" type="number" step="1" name="custo_vazio" id="custo_vazio" placeholder="Custo Vazio"></div>
            <button class="btn btn-success" type="submit">Salvar</button>
        </form>
    </div>
@endsection