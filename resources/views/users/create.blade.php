@extends('layouts.app')
@section('content')

<div class="container">
    <form action="{{ route('salvar_user') }}" method="post">
        @csrf
            <div for="descricao"><label>Nome</label><br>
            <input class="form-control" type="text" name="name" id="name"></div>
            <div for="descricao"><label>Tipo</label><br>
            <input class="form-control" type="number" name="tipo" id="tipo"></div>
            <div for="descricao"><label>CPF</label><br>
            <input class="form-control" type="text" name="cpf" id="cpf"></div>
            <div for="descricao"><label>Email</label><br>
            <input class="form-control" type="text" name="email" id="email"></div>
            <div for="descricao"><label>Senha</label><br>
            <input class="form-control" type="text" name="password" id="password"></div>
            <button class="btn btn-success" type="submit">Salvar</button>
    </form>

</div>

@endsection