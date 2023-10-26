@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Empresas</h1>
@stop

@section('content')

    <div class="container">
        <a class="btn btn-info" href="/empresa/cadastro">Empresa</a>
        <table class="table table-striped">
            <thead>
                <th>ID</th>
                <th>RAZÃO SOCIAL</th>
                <th>CPF OU CNPJ</th>
                <th></th>
                <th></th>
            </thead>
            <tbody>
                @foreach($empresas as $empresa)
                    <tr>
                        <td><b>#{{$empresa->id}}</b></td>
                        <td>{{$empresa->fantasia}}</td>
                        <td>{{$empresa->cpf_cnpj}}</td>
                        @if($empresa->status == 1)
                            <td><a class="text-danger" href="{{route('desativarReativar_empresa', ['id' => $empresa->id])}}"><i class="fas fa-ban"></i></a></td>
                        @else
                            <td><a class="text-success" href="{{route('desativarReativar_empresa', ['id' => $empresa->id])}}"><i class="fas fa-check"></i></a></td>
                        @endif
                        <td>
                            <a href="{{route('editar_empresa', ['id' => $empresa->id])}}"><i class="fa fa-edit"></i></a>
                        </td>
                        <td>
                            <a href="{{ route('empresa.view', [$empresa->id]) }}"><i class="fa fa-eye"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection