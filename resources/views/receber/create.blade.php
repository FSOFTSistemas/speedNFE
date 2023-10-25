@extends('layouts.app')
@section('content')
    <div class="container">
        <form action="{{ route('salvar_recebimento') }}" method="post">
            @csrf
            <div for="cliente_id"><label>CLIENTE</label>
            <select class="form-select" id='cliente_id' name='cliente_id'>
            @foreach ($clientes as $cliente)
                <option value="{{$cliente->id}}">
                    {{$cliente->nome}}
                </option>                
            @endforeach
            </select>
            </div>
            <div for="descricao"><label>DESCRICAO</label><br>
            <input class="form-control" type="text" name="descricao" id="descricao"></div>
            <div for="valor"><label>VALOR</label><br>
            <input class="form-control" type="number" step="0.01" name="valor_original" id="valor_original"></div>
            <div for="vencimento"><label>VENCIMENTO</label><br>
            <input class="form-control" type="date" name="vencimento" id="vencimento"></div>
            <button class="btn btn-success" type="submit">Salvar</button>
        </form>
    </div>
@endsection