@extends('layouts.app')
@section('content')

<div class="container">
    <div for="descricao"><label>Tipo</label><br>
    <input disabled class="form-control" type="number" name="tipo" id="tipo" value="{{$user->tipo}}"></div>
    <div for="descricao"><label>Nome</label><br>
    <input disabled class="form-control" type="text" name="name" id="name"value="{{$user->name}}"> </div>
    <div for="descricao"><label>CPF</label><br>
    <input disabled class="form-control" type="text" name="cpf" id="cpf" value="{{$user->cpf}}"></div>
    <div for="descricao"><label>Email</label><br>
    <input disabled class="form-control" type="text" name="email" id="email" value="{{$user->email}}"></div>
    <div class="row">
        <div class="col">
            <a href="/usuario" class="btn btn-info">Voltar</a>
        </div>
        <div class="col">
            <a class="btn btn-warning" href="{{ route('editar_user', ['id' => $user->id]) }}">Editar</a>
        </div>
    </div>
</div>

@endsection