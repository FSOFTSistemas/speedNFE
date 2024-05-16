@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h4 class="m-0 text-dark" style="text-align: center;">Cadastro de Usuários</h4>
@stop

@section('content')
    <div class="row">
        <div class="col">
            <a class="btn btn-secondary" href="{{ route('index_usuario') }}" style="margin-bottom: 2%">Cancelar</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method='post' action="{{ route('salvar_usuario') }}">
                @csrf
                <label>Nome</label>
                <input autocomplete="off" type="text" class="form-control" placeholder="Nome..." name="name" id="name" value="{{ old('name') }}"/>

                <label>E-mail</label>
                <input autocomplete="off" type="text" class="form-control" placeholder="E-mail..." name="email" id="email" value="{{ old('email') }}"/>

                <label>Senha</label>
                <input type="password" autocomplete="off" class="form-control" placeholder="Senha..." name="senha" id="senha" value="{{ old('senha') }}"/>

                @if ($empresa == 1)
                    <label>Empresa</label>
                    <select class="form-control" name="empresa" id="empresa">
                        <option>--escolha uma empresa--</option>
                        @foreach ($empresas as $emp)
                            <option value="{{ $emp->id }}" @if(old('empresa') == $emp->id) selected @endif>{{ $emp->fantasia }}</option>
                        @endforeach
                    </select>

                    <label>Permissões</label>
                    <select required class="form-control" name="cargo" id="cargo">
                        <option value="">--Selecione uma permissão--</option>
                        <option value="master" @if(old('cargo') == "master") selected @endif>master</option>
                        <option value="admin" @if(old('cargo') == "admin") selected @endif>admin</option>
                        <option value="client-NFe" @if(old('cargo') == "client-NFe") selected @endif>Apenas NFe</option>
                        <option value="client-NFCe" @if(old('cargo') == "client-NFCe") selected @endif>Apenas NFCe</option>
                        <option value="client-MDFe" @if(old('cargo') == "client-MDFe") selected @endif>Apenas MDFe</option>
                        <option value="client-CTe" @if(old('cargo') == "client-CTe") selected @endif>Apenas CTe</option>
                        <option value="client-advanced1" @if(old('cargo') == "client-advanced1") selected @endif>NFe e MDFe</option>
                        <option value="client-advanced2" @if(old('cargo') == "client-advanced2") selected @endif>NFe e NFCe</option>
                        <option value="client-advanced3" @if(old('cargo') == "client-advanced3") selected @endif>CTe e MDFe</option>
                    </select>
                @else
                    <input hidden name="empresa" id="empresa" value="{{ $empresa }}" />
                    <input hidden name="empresa" id="cargo" value="{{ $empresa->cargo }}" />
                @endif

                <div style="padding-top:2%" class="text-center">
                    <button type="submit" class="form-control btn btn-success">Salvar</button>
                </div>

            </form>
        </div>
    </div>

@endsection
