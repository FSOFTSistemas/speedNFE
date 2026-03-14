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
                        <form action="{{route('salvar_recebimento')}}" method="POST">
                            @csrf
                            <div for="cliente_id"><label>CLIENTE</label>
                                <select class="form-control" id='cliente' name='cliente'>
                                @foreach ($clientes as $cliente)
                                    <option value="{{$cliente->id}}">
                                        {{$cliente->nome}}
                                    </option>
                                @endforeach
                                </select>
                            </div>
                            <div for="valor"><label>VALOR</label><br>
                            <input class="form-control" type="number" step="0.01" name="total" id="total"></div>
                            <div for="vencimento"><label>VENCIMENTO</label><br>
                            <input class="form-control" type="date" name="vencimento" id="vencimento"></div>
                            <button class="btn btn-success" type="submit">Salvar</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-2"></div>
        </div>
    </div>

@endsection