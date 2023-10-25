@extends('layouts.app')
@section('content')
    <div class="container">
    <form action="{{ route('save_config')}}" method="post">
    @csrf
        @if(isset($config)) 
                <div class="card">                
                    <div class="card-body">
                        <h3 class="card-title">Empresa</h3>

                        <input type='hidden' name="action" id="action" value="{{ $config->id }}"/>

                        <label>Razão Social</label>
                        <input class="form-control" type="text" id="nome" name="nome" value="{{ $config->nome }}"/>

                        <label>Nome Fantasia</label>
                        <input class="form-control" type="text" id="fantasia" name="fantasia" value="{{ $config->fantasia }}"/>

                        <label>Endereço</label>
                        <input class="form-control" type="text" id="endereco" name="endereco" value="{{ $config->endereco }}"/>

                        <label>Número</label>
                        <input class="form-control" type="text" id="numero" name="numero" value="{{ $config->numero }}"/>

                        <label>Complemento</label>
                        <input class="form-control" type="text" id="complemento" name="complemento" value="{{ $config->complemento }}"/>

                        <label>Bairro</label>
                        <input class="form-control" type="text" id="bairro" name="bairro" value="{{ $config->bairro }}"/>

                        <label>Código Município</label>
                        <input class="form-control" type="text" id="cod_mun" name="cod_mun" value="{{ $config->cod_municipio }}"/>

                        <label>Código IBGE</label>
                        <input class="form-control" type="text" id="ibge" name="ibge" value="{{ $config->IBGE }}"/>

                        <label>Cidade</label>
                        <input class="form-control" type="text" id="cidade" name="cidade" value="{{ $config->cidade }}"/>

                        <label>Unidade Federativa</label>
                        <input class="form-control" type="text" id="uf" name="uf" value="{{ $config-> uf}}"/>

                        <label>CEP</label>
                        <input class="form-control" type="text" id="cep" name="cep" value="{{ $config->cep }}"/>

                        <label>Telefone</label>
                        <input class="form-control" type="text" id="telefone" name="telefone" value="{{ $config->telefone }}"/>

                        <label>Responsável</label>
                        <input class="form-control" type="text" id="responsavel" name="responsavel" value="{{ $config->responsavel }}"/>

                        <label>CNAE</label>
                        <input class="form-control" type="text" id="cnae" name="cnae" value="{{ $config->cnae }}"/>

                        <label>Inscrição Estadual</label>
                        <input class="form-control" type="text" id="ie" name="ie" value="{{ $config->inscricao_estadual }}"/>

                        <label>Inscrição Municipal</label>
                        <input class="form-control" type="text" id="im" name="im" value="{{ $config->inscricao_municipal }}"/>

                        <label>CNPJ</label>
                        <input class="form-control" type="text" id="cnpj" name="cnpj" value="{{ $config->cnpj }}"/>

                        <label>Atividade</label>
                        <input class="form-control" type="text" id="atividade" name="atividade" value="{{ $config->atividade }}"/>

                        <label>E-mail</label>
                        <input class="form-control" type="text" id="email" name="email" value="{{ $config->email }}"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Financeiro</h3>
                                <label>Juros (%)</label>
                                <input class="form-control" type="number" step="0.01" id="juros" name="juros" value="{{ $config->juros }}"/>
                                <label>Multa (%)</label>
                                <input class="form-control" type="number" step="0.01" id="multa" name="multa" value="{{ $config->multa }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="col"></div>
                    <div class="col"></div>
                    <div class="col">
                        <br>
                        <br>
                        <br>
                        <button class="btn btn-success" type="submit">Salvar</button>
                        <button class="btn btn-danger" type="submit">Cancelar</button>
                    </div>
                </div>
            @else
                <div class="card">                
                    <div class="card-body">
                        <h3 class="card-title">Empresa</h3>

                        <input type='hidden' name="action" id="action" value="new"/>

                        <label>Razão Social</label>
                        <input class="form-control" type="text" id="nome" name="nome"/>

                        <label>Nome Fantasia</label>
                        <input class="form-control" type="text" id="fantasia" name="fantasia"/>

                        <label>Endereço</label>
                        <input class="form-control" type="text" id="endereco" name="endereco"/>

                        <label>Número</label>
                        <input class="form-control" type="text" id="numero" name="numero"/>

                        <label>Complemento</label>
                        <input class="form-control" type="text" id="complemento" name="complemento"/>

                        <label>Bairro</label>
                        <input class="form-control" type="text" id="bairro" name="bairro"/>

                        <label>Código Município</label>
                        <input class="form-control" type="text" id="cod_mun" name="cod_mun"/>

                        <label>Código IBGE</label>
                        <input class="form-control" type="text" id="ibge" name="ibge"/>

                        <label>Cidade</label>
                        <input class="form-control" type="text" id="cidade" name="cidade"/>

                        <label>Unidade Federativa</label>
                        <input class="form-control" type="text" id="uf" name="uf"/>

                        <label>CEP</label>
                        <input class="form-control" type="text" id="cep" name="cep"/>

                        <label>Telefone</label>
                        <input class="form-control" type="text" id="telefone" name="telefone"/>

                        <label>Responsável</label>
                        <input class="form-control" type="text" id="responsavel" name="responsavel"/>

                        <label>CNAE</label>
                        <input class="form-control" type="text" id="cnae" name="cnae"/>

                        <label>Inscrição Estadual</label>
                        <input class="form-control" type="text" id="ie" name="ie"/>

                        <label>Inscrição Municipal</label>
                        <input class="form-control" type="text" id="im" name="im"/>

                        <label>CNPJ</label>
                        <input class="form-control" type="text" id="cnpj" name="cnpj"/>

                        <label>Atividade</label>
                        <input class="form-control" type="text" id="atividade" name="atividade"/>

                        <label>E-mail</label>
                        <input class="form-control" type="text" id="email" name="email"/>

                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Financeiro</h3>
                                <label>Juros (%)</label>
                                <input class="form-control" type="number" step="0.01" id="juros" name="juros" value="0"/>
                                <label>Multa (%)</label>
                                <input class="form-control" type="number" step="0.01" id="multa" name="multa" value="0"/>
                            </div>
                        </div>
                    </div>
                    <div class="col"></div>
                    <div class="col"></div>
                    <div class="col">
                        <br>
                        <br>
                        <br>
                        <button class="btn btn-success" type="submit">Salvar</button>
                        <a class="btn btn-danger" href="/home">Cancelar</a>
                    </div>
                </div>
        @endif
        <form>
    </div>
@endsection