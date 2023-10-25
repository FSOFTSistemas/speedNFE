@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Cadastro de Estoque</h1>
@stop

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="{{route('update_estoque', ['id' => $estoque->id])}}">
                            @csrf

                            <label>Estoque</label>
                            <input class="form-control" name="estoque" id="estoque" type="number" step="0.01" value="{{$estoque->estoque}}" />

                            <div class="text-center" style="padding-top: 2%">
                                <button class="btn btn-success">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-2"></div>
        </div>
    </div>

@endsection