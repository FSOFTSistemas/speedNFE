@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Estoques</h1>
@stop

@section('content')
    <div class="container">
        <div style="padding-bottom:1%">
            <a href='/estoque/cadastro' class="btn btn-info">&nbsp;+ Estoque&nbsp;</a>
        </div>
        <table class="table table-striped">
            <thead>
                <th>ID</th>
                <th>PRODUTO</th>
                <th>ESTOQUE</th>
                <th></th>
                <th></th>
            </thead>

            <tbody>
                @foreach($estoques as $estoque)
                    <tr>
                        <td><b>#{{$estoque->id}}</b></td>
                        <td>{{$estoque->produto}}</td>
                        <td>{{$estoque->estoque}}</td>
                        <td><a class='btn btn-warning' href="{{route('editar_estoque', ['id' => $estoque->id])}}">Editar</a></td>
                        <td><a class='btn btn-danger' href="{{route('excluir_estoque', ['id' => $estoque->id])}}">Excluir</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection