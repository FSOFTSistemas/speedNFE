@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Cadastro de Clientes</h1>
@stop

@section('content')

    <div class="container">
        <div>
            @if ($logged->cargo == 'admin')
                <a href="{{route('cadastrar_usuario')}}" class='btn btn-info'>&nbsp;+ Usuário&nbsp;</a>
            @else
                <a disabled href="{{route('cadastrar_usuario')}}" class='btn btn-info'>&nbsp;+ Usuário&nbsp;</a>
            @endif
        </div>
        <table class="table table-striped">
            <thead>
                <th>ID</th>
                <th>LOGIN</th>
                <th>CARGO</th>
                <th>EMPRESA</th>
                <th></th>
                <th></th>
            </thead>

            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>#{{$user->id}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->cargo}}</td>
                        <td>{{$user->fantasia}}</td>
                        @if ($logged->cargo == 'admin')
                            <td><a class="btn btn-warning" href="{{route('editar_usuario', ['id' => $user->id])}}">Editar</a></td>
                            <td><a class="btn btn-danger" href="route{{route('excluir_usuario', ['id' => $user->id])}}">Excluir</a></td>
                        @else
                            <td><a disabled class="btn btn-warning" href="{{route('editar_usuario', ['id' => $user->id])}}" >Editar</a></td>
                            <td><a disabled class="btn btn-danger" href="{{route('excluir_usuario', ['id' => $user->id])}}">Excluir</a></td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection