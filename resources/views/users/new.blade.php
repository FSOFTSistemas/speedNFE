@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
<h4 class="m-0 text-dark" style="text-align: center;">Cadastro de Usuários</h4>
@stop

@section('content')



<div class="card">
    <div class="card-body">
        <form method='post' action="{{route('salvar_usuario')}}">
            @csrf
            <label>Nome</label>
            <input autocomplete="off" type="text" class="form-control" name="name" id="name" />

            <label>E-mail</label>
            <input autocomplete="off" type="text" class="form-control" name="email" id="email" />

            <label>Senha</label>
            <input type="password" autocomplete="off" class="form-control" name="senha" id="senha" />

            <label>Cargo</label>
            <select class="form-control" name="cargo" id="cargo">
                <option>--selecione um cargo--</option>
                <option value="admin">Administrador</option>
                <option value="vendedor">Vendedor</option>
            </select>

            @if($empresa == 0)
            <label>Empresa</label>
            <select class="form-control" name="empresa" id="empresa">
                <option>--escolha uma empresa--</option>
                @foreach($empresas as $emp)
                <option value="{{$emp->id}}">{{$emp->fantasia}}</option>
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
