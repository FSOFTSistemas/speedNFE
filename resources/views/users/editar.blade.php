@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h4 class="m-0 text-dark" style="text-align: center;">Atualizar Usuário</h4>
@stop

@section('content')
    <div class="row">
        <div class="col">
            <a class="btn btn-secondary" href="{{ route('index_usuario') }}" style="margin-bottom: 2%">Cancelar</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method='post' action="{{ route('update_usuario', ['id' => $user->id]) }}">
                @csrf
                <label>Nome</label>
                <input autocomplete="off" type="text" class="form-control" name="name" id="name"
                    value="{{ $user->name }}" />

                <label>Permissões</label>
                <select required class="form-control" name="cargo" id="cargo">
                    <option value="">--Selecione uma permissão--</option>
                    <option value="master" @if ($user->cargo == 'master') selected @endif>master</option>
                    <option value="admin" @if ($user->cargo == 'admin') selected @endif>admin</option>
                    <option value="client-NFe" @if ($user->cargo == 'client-NFe') selected @endif>Apenas NFe</option>
                    <option value="client-MDFe" @if ($user->cargo == 'client-MDFe') selected @endif>Apenas MDFe</option>
                    <option value="client-advanced" @if ($user->cargo == 'client-advanced') selected @endif>NFe e MDFe</option>
                </select>

                @if ($empresa == 0)
                    <label>Empresa</label>
                    <select class="form-control" name="empresa" id="empresa">
                        <option>--escolha uma empresa--</option>
                        @foreach ($empresas as $emp)
                            @if ($user->empresa_id == $emp->id)
                                <option selected value="{{ $emp->id }}">{{ $emp->fantasia }}</option>
                            @else
                                <option value="{{ $emp->id }}">{{ $emp->fantasia }}</option>
                            @endif
                        @endforeach
                    </select>
                @else
                    <input hidden name="empresa" id="empresa" value="{{ $empresa }}" />
                @endif

                <div style="padding-top:2%" class="text-center">
                    <button type="submit" class="form-control btn btn-success">Salvar</button>
                </div>

            </form>
        </div>
    </div>

@endsection
