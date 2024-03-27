@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
<h4 class="m-0 text-dark" style="text-align: center;">Cadastro de Usuários</h4>
@stop

@section('content')





<div class="card">
    <div class="card-body">
        <form method='post' action="{{route('update_usuario', ['id' => $user->id])}}">
            @csrf
            <label>Nome</label>
            <input autocomplete="off" type="text" class="form-control" name="name" id="name" value="{{$user->name}}" />

            <label>E-mail</label>
            <input autocomplete="off" type="text" class="form-control" name="email" id="email" value="{{$user->email}}" />

            <label>Senha</label>
            <input type="password" autocomplete="off" class="form-control" name="senha" id="senha" />

            <label>Cargo</label>
            <select class="form-control" name="cargo" id="cargo">
                <option>--selecione um cargo--</option>
                @if($user->cargo == 'admin')
                <option selected value="admin">Administrador</option>
                <option value="vendedor">Vendedor</option>
                @else
                <option value="admin">Administrador</option>
                <option selected value="vendedor">Vendedor</option>
                @endif
            </select>

            @if($empresa == 0)
            <label>Empresa</label>
            <select class="form-control" name="empresa" id="empresa">
                <option>--escolha uma empresa--</option>
                @foreach($empresas as $emp)
                @if($user->empresa_id == $emp->id)
                <option selected value="{{$emp->id}}">{{$emp->fantasia}}</option>
                @else
                <option value="{{$emp->id}}">{{$emp->fantasia}}</option>
                @endif
                @endforeach
            </select>
            @else
            <input hidden name="empresa" id="empresa" value="{{$empresa}}" />
            @endif

            <div style="padding-top:2%" class="text-center">
                <button type="submit" class="btn btn-success">Salvar</button>
            </div>

        </form>
    </div>
</div>





@endsection
