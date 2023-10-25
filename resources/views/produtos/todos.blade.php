@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Produtos</h1>
@stop

@section('content')

    <div class="container">
        <a class="btn btn-info" href="/produto/cadastro">&nbsp;+ Produto&nbsp;</a>
        <table class="table table-striped">
            <thead>
                <th>CODIGO</th>
                <th>PRODUTO</th>
                <th>PRECO CUSTO</th>
                <th>PRECO VENDA</th>
                <th>CATEGORIA</th>
                @if($empresa == 1)
                    <th>EMPRESA</th>
                @endif
                {{-- <th>estoque atual</th> --}}
                <th></th>
                <th></th>
            </thead>

            <tbody>
                @foreach($produtos as $produto)
                    <tr>
                        <td>{{$produto->codigo}}</td>
                        <td>{{$produto->produto}}</td>
                        <td>{{$produto->precocusto}}</td>
                        <td>{{$produto->precovenda}}</td>
                        <td>{{$produto->descricao}}</td>
                        @if($empresa == 1)
                            <td>{{$produto->fantasia}}</td>
                        @endif
                        {{-- <td>estoque</td> --}}
                        <td><a class="btn btn-warning" href="{{route('editar_produto', ['id' => $produto->id])}}">Editar</a></td>
                        <td><a class="btn btn-danger" href="{{route('excluir_produto', ['id' => $produto->id])}}">Excluir</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

@endsection