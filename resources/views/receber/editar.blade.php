@extends('layouts.app')
@section('content')
    <div class="container">
        <form action="{{ route('receber', ['id' => $recebimento->id]) }}" method="post">
            @csrf
            <div for="valor_cheio"><label>VALOR ORIGINAL</label><br>
            <input class="form-control" disabled type="number" step="0.01" name="valor_original" id="valor_original" value="{{ $recebimento->valor_original }}"></div>
            <div for="valor_cheio"><label>VALOR ATUAL</label><br>
            <input class="form-control" disabled type="number" step="0.01" name="valor_atual" id="valor_atual" value="{{ $recebimento->valor_atual }}"></div>
            <div for="valor_cheio"><label>VALOR RECEBIDO</label><br>
            <input class="form-control" type="number" step="0.01" max="{{ $recebimento->valor_atual }}" name="valor_pago" id="valor_pago"></div>
            <button class="btn btn-success" type="submit">Salvar</button>
        </form>
    </div>
@endsection