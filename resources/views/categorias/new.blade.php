@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Categorias</h1>
@stop

@section('content')

<div class="container">
    <form action="{{route('salvar_categoria')}}" method="post">
        @csrf
        <div class="card">
            <div class="card-body">
                @if($empresa == 1)
                    <label>Empresa</label>
                    <select class="form-control" name="empresa" id="empresa">
                        <option>--Selecione uma empresa</option>
                        @foreach ($empresas as $emp)
                            <option value="{{$emp->id}}">{{$emp->fantasia}}</option>
                        @endforeach
                    </select>
                @endif
                <label>Descrição</label>
                <input class="form-control" name="descricao" type="text" id="descricao">
                <input type="hidden" class="form-control" name="status" id="status" value="1">
                <div style="padding-top: 2%;">
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection