@extends('layouts.app')
@section('content')
    <div class="container">
            <label>Tipo</label>
            <div name="tipo" id="tipo" class="mb-3" for="tipo">
                <select disabled class="form-select">
                    <option>
                        -- tipo de cliente --
                    </option>
                    @if ($cliente->tipo == 1)
                        <option selected value='1'>
                            Pessoa Física 
                        </option>
                        <option value='2'>
                            Pessoa Jurídica
                        </option>
                    @else
                        <option value='1'>
                            Pessoa Física 
                        </option>
                        <option selected value='2'>
                            Pessoa Jurídica
                        </option>
                    @endif
                </select>
            </div>
            <div class="mb-3" for="nome"><label>Nome</label>
            <input disabled class="form-control" type="text" name="nome" id="nome" value="{{ $cliente->nome }}"></div>
            <div class="mb-3" for="cpf"><label>CPF</label>
            <input disabled class="form-control" type="text" name="cpf" id="cpf" value="{{ $cliente->cpf }}"></div>
            <div class="mb-3" for="telefone"><label>Telefone</label>
            <input disabled class="form-control" type="text" name="telefone" id="telefone" value="{{ $cliente->telefone }}"></div>
            <div class="mb-3" for="logradouro"><label>Logradoouro</label>
            <input disabled class="form-control" type="text" name="logradouro" id="logradouro" value="{{ $cliente->logradouro }}"></div>
            <div class="mb-3" for="numero"><label>Numero</label>
            <input disabled class="form-control" class="mb-3" type="text" name="numero" id="numero" value="{{ $cliente->numero }}"></div>
            <div class="mb-3" for="bairro"><label>Bairro</label>
            <input disabled class="form-control" class="mb-3" type="text" name="bairro" id="bairro" value="{{ $cliente->bairro }}"></div>
            <div class="mb-3" for="cep"><label>CEP</label>
            <input disabled class="form-control" type="text" name="cep" id="cep" value="{{ $cliente->cep }}"></div>
            <div class="mb-3" for="cidade"><label>Cidade</label>
            <input disabled class="form-control" type="text" name="cidade" id="cidade" value="{{ $cliente->cidade }}"></div>
            <div class="mb-3" for="referencia"><label>Referencia</label>
            <input disabled class="form-control" type="text" name="referencia" id="referencia" value="{{ $cliente->referencia }}"></div>
            <div class="mb-3" for="nascimento"><label>Nascimento</label>
            <input disabled class="form-control" type="date" name="nascimento" id="nascimento" value="{{ $cliente->nascimento }}"></div>
            <div class="row">
                <div class="col">
                    <a class="btn btn-info" href="{{route('clientes')}}" title="Voltar para Clientes">Voltar</a>
                </div>
                <div class="col">
                <td><a class="btn btn-warning" href="{{ route('editar_cliente', ['id'=>$cliente->id]) }}" title="Editar cliente {{ $cliente->nome }}">Editar</a></td>
                </div>
            </div>
    </div>
@endsection