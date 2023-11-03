@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h1 class="m-0 text-dark">Categorias</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="row" style="margin-bottom: 2%">
        <div class="col">
                <a href="{{ route('categoria.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
    <div class="container">
        <form action="{{ route('salvar_categoria') }}" method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="row" style="text-align: center">
                        <div class="col">
                            <h3>Informações da Categoria</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($empresa == 1)
                        <label>Empresa</label>
                        <select class="form-control" name="empresa" id="empresa" required>
                            <option value="">--Selecione uma empresa</option>
                            @foreach ($empresas as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->fantasia }}</option>
                            @endforeach
                        </select>
                    @endif

                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea class="form-control" name="descricao" id="descricao" cols="30" rows="5" required></textarea>
                    </div>

                    <div class="row" style="margin-top: 2%">
                        <div class="col">
                            <button type="submit" class="btn btn-success form-control">Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('css')
    <style>
        label {
            margin-top: 2%;
        }
    </style>
@stop
