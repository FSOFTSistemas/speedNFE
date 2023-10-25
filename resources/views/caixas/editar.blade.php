@extends('layouts.app')
@section('content')

<div class="container">
    <form action="{{ route('abrir_caixa', ['id'=> $caixa->id]) }}" method="post">
        @csrf
        <label>Entrada</label>
        <input class="form-control" type="number" step="0.01" name="saldo" id="saldo">
        <button type="submit" class="btn btn-success">Salvar</button>
    </form>
</div>

@endsection