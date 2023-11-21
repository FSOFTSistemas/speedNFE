@extends('adminlte::page')

@section('title', 'Cadastrar Motorista')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3>Cadastrar Motorista</h3>
        </div>
    </div>
@stop

@section('content')
    <div class="row" style="margin-bottom: 2%;">
        <div class="col">
            <a class="btn btn-secondary" href="{{ route('motorista.index') }}">Voltar</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="row" style="text-align: center">
                    <div class="col">
                        <h2>Informações de Identificação</h2>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('motorista.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="">Nome</label>
                                <input class="form-control" type="text" name="nome" id="nome" required
                                    placeholder="Nome...">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="">CPF</label>
                                <input class="form-control" onblur="this.value = formatarCpf(this.value);" maxlength="11"
                                    type="text" name="cpf" id="cpf" required placeholder="Cpf...">
                            </div>
                        </div>

                        @if (Auth::user()->empresa_id == 1)
                            <div class="col">
                                <div class="row">
                                    <label for="">Empresa</label>
                                    <select class="form-control" name="empresaId" id="empresaId" required>
                                        <option value="">-- Selecione uma Empresa --</option>
                                        @foreach ($empresas as $empresa)
                                            <option value="{{ $empresa->id }}">{{ $empresa->fantasia }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                    </div>

                    <div class="row" style="text-align: center">
                        <div class="col">
                            <button class="btn btn-success" type="submit" style="width: 25%">Salvar</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        function formatarCpf(valor) {
            // Remove qualquer caracter que não seja número
            valor = valor.replace(/\D/g, '');

            // Verifica se é CPF (11 dígitos)
            if (valor.length === 11) {
                // Formata o CPF ###.###.###-##
                return valor.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            }
            // Verifica se é CNPJ (14 dígitos)
            // else if (valor.length === 14) {
            //     // Formata o CNPJ ##.###.###/####-##
            //     return valor.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
            // }
            // Não é CPF nem CNPJ
            else {
                return valor;
            }
        }
    </script>
@stop
