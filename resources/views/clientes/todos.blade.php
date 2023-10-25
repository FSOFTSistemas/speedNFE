@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Clientes</h1>
@stop

@section('content')

    <div class="container">
        <a class="btn btn-info" href='/cliente/cadastro'>&nbsp; + Cliente &nbsp;</a>
        <table class="table table-striped">
            <thead>
                <th>NOME</th>
                <th>CPF OU CNPJ</th>
                {{-- <th>CELULAR</th>
                <th>TIPO</th>
                <th>LIMITE</th> --}}
                @if($empresa == 1)
                    <th>EMPRESA</th>
                @endif
                <th></th>
                <th></th>
            </thead>
            <tbody>
                @foreach($clientes as $cliente)
                    @if($cliente->situacao == 0) {{-- situacao = 0 é ativo --}}
                        <tr>
                            <td>{{$cliente->nome}}</td>
                            <td>{{$cliente->cpf_cnpj}}</td>
                            {{-- <td>{{$cliente->celular}}</td>
                            @if ($cliente->tipo = 1)
                                <td>Pessoa Física</td>
                            @else
                                <td>Pessoa Jurídica</td>
                            @endif
                            <td>{{$cliente->limite}}</td> --}}
                            @if($empresa == 1)
                                <td>{{$cliente->fantasia}}</td>
                            @endif
                            <td><a href='{{route('editar_cliente', ['id' => $cliente->id])}}' class='btn btn-warning'>Editar</a></td>
                            <td><a href='{{route('excluir_cliente', ['id' => $cliente->id])}}' class='btn btn-danger'>Excluir</a></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

    </div>
@endsection