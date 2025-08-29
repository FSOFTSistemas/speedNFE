@extends('adminlte::page')

@section('title', 'Editar Cliente')

@push('css')
<style>
    /* Estilos importados para consistência */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    :root {
        --primary-color: #00033a;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --text-dark: #343a40;
        --input-focus-border: #80bdff;
        --input-focus-shadow: rgba(0, 123, 255, .25);
    }
    body {
        font-family: 'Poppins', sans-serif;
    }
    .card-main {
        background: var(--card-bg);
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px var(--shadow-color);
        padding: 30px;
    }
    .custom-btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: #fff;
        font-weight: 500;
        border-radius: 8px;
        padding: 12px 20px;
        transition: all 0.3s ease;
    }
    .custom-btn-primary:hover {
        background-color: #00045e;
        border-color: #00045e;
        transform: translateY(-2px);
    }
    .custom-btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        color: #fff;
        font-weight: 500;
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .custom-btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: .5rem;
    }
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        padding: 10px 15px;
        height: auto;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--input-focus-border);
        box-shadow: 0 0 0 0.2rem var(--input-focus-shadow);
    }
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: var(--text-light, #6c757d);
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
        color: var(--primary-color);
        border-bottom: 3px solid var(--primary-color);
        background-color: transparent;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-md-10">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Edição de Cliente</h1>
        </div>
        <div class="col-md-2 text-md-right mt-2 mt-md-0">
            <a href="{{ route('cliente.index') }}" class="btn custom-btn-secondary btn-block">Voltar</a>
        </div>
    </div>
@stop

@section('content')
<div class="card card-main">
    <div class="card-body">
        <ul class="nav nav-tabs" id="tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="home-tab" data-toggle="pill" href="#home" role="tab"
                    aria-controls="home" aria-selected="true"><b>Informações do Cliente</b></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="profile-tab" data-toggle="pill" href="#profile" role="tab"
                    aria-controls="profile" aria-selected="false"><b>Endereço</b></a>
            </li>
        </ul>
        <form class="needs-validation mt-4" novalidate action="{{ route('salvar_cliente', [$cliente->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="codigo" id="codigo" value="{{ $cliente->codigo }}">
            
            <div class="tab-content" id="tabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row">
                         <div class="col-md-4 mb-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select class="form-select" name="tipo" id="tipo" required>
                                <option value="1" @if($cliente->tipo == 1) selected @endif>Pessoa Física</option>
                                <option value="2" @if($cliente->tipo == 2) selected @endif>Pessoa Jurídica</option>
                            </select>
                            <div class="invalid-feedback">Informe um tipo válido.</div>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="cpf_cnpj" class="form-label">CPF/CNPJ</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj" placeholder="Digite o CPF ou CNPJ" required value="{{ $cliente->cpf_cnpj }}">
                                <div class="input-group-append">
                                    <button id="cnpj_button" type="button" class="btn btn-dark" style="border-radius: 0 8px 8px 0;"><i class="fa fa-search"></i></button>
                                </div>
                                <div class="invalid-feedback">Informe um CPF/CNPJ válido.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label for="nome" class="form-label">Nome / Razão Social</label>
                            <input class="form-control" type="text" name="nome" id="nome" placeholder="Nome completo ou Razão Social" value="{{ $cliente->nome }}" required>
                            <div class="invalid-feedback">Informe um nome válido.</div>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="apelido" class="form-label">Apelido / Nome Fantasia</label>
                            <input class="form-control" type="text" name="apelido" id="apelido" placeholder="Apelido ou Nome Fantasia" value="{{ $cliente->apelido }}" required>
                            <div class="invalid-feedback">Informe um apelido válido.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="rg_ie" class="form-label">RG / Inscrição Estadual</label>
                            <input class="form-control" type="text" name="rg_ie" id="rg_ie" required placeholder="RG ou Inscrição Estadual" value="{{ $cliente->rg_ie }}">
                            <div class="invalid-feedback">Informe um RG/IE válido.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" id="telefone" name="telefone" class="form-control" placeholder="Telefone com DDD" maxlength="15" onkeyup="handlePhone(event)" value="{{ $cliente->telefone }}" required>
                            <div class="invalid-feedback">Informe um telefone válido.</div>
                        </div>
                         <div class="col-md-4 mb-3">
                            <label for="limite" class="form-label">Limite de Crédito</label>
                            <input class="form-control" type="text" name="limite" id="limite" placeholder="0,00" value="{{ $cliente->limite ?? 0 }}" required>
                            <div class="invalid-feedback">Informe um limite válido.</div>
                        </div>
                    </div>

                        <input type="hidden" name="empresa" value="{{ $cliente->empresa_id }}">
     
                </div>

                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                   <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cep" class="form-label">CEP</label>
                            <div class="input-group">
                                <input placeholder="Digite o CEP" class="form-control" type="text" id="cep" name="cep" value="{{ $cliente->cep }}" />
                                 <div class="input-group-append">
                                    <button class="btn btn-dark" type="button" id="cep_button" style="border-radius: 0 8px 8px 0;"><i class="fa fa-search"></i></button>
                                </div>
                                <div class="invalid-feedback">Informe um CEP válido.</div>
                            </div>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label for="rua" class="form-label">Logradouro</label>
                            <input class="form-control" type="text" name="rua" id="rua" value="{{ $cliente->rua }}" placeholder="Rua, Avenida, etc.">
                            <div class="invalid-feedback">Informe uma rua válida.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="numero" class="form-label">Número</label>
                            <input class="form-control" type="text" name="numero" id="numero" value="{{ $cliente->numero }}" placeholder="Nº">
                            <div class="invalid-feedback">Informe um nº válido.</div>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input class="form-control" type="text" name="bairro" id="bairro" value="{{ $cliente->bairro }}" placeholder="Bairro">
                            <div class="invalid-feedback">Informe um bairro válido.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input class="form-control" type="text" name="cidade" id="cidade" value="{{ $cliente->cidade }}" placeholder="Cidade">
                            <div class="invalid-feedback">Informe uma cidade válida.</div>
                        </div>
                    </div>
                    <div class="row">
                         <div class="col-md-3 mb-3">
                            <label for="uf" class="form-label">Estado</label>
                            <select name="uf" id="uf" class="form-select">
                                <option value="">UF</option>
                                @foreach(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $estado)
                                    <option value="{{ $estado }}" @if($cliente->uf == $estado) selected @endif>{{ $estado }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Informe um estado válido.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ibge" class="form-label">Cód. IBGE</label>
                            <input placeholder="Código IBGE" class="form-control" type="number" id="ibge" name="ibge" value="{{ $cliente->codigoIBGE }}" />
                            <div class="invalid-feedback">Informe um código IBGE válido.</div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="row mt-4">
                <div class="col-md-6 mx-auto text-center">
                    <button type="submit" class="btn custom-btn-primary btn-block">Salvar Alterações</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@push('js')
<script>
    // Script de validação e máscaras (similar ao de cadastro)
    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })();
    function somenteNumeros(valor) { return valor.replace(/\D/g, ""); }
    const handlePhone = (event) => {
        let input = event.target;
        input.value = phoneMask(input.value);
    }
    const phoneMask = (value) => {
        if (!value) return "";
        value = value.replace(/\D/g, '');
        value = value.replace(/(\d{2})(\d)/, "($1) $2");
        value = value.replace(/(\d)(\d{4})$/, "$1-$2");
        return value;
    }
    document.addEventListener('DOMContentLoaded', function() {
        const cpfCnpjInput = document.getElementById('cpf_cnpj');
        if (cpfCnpjInput) {
            cpfCnpjInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) {
                    value = value.substring(0, 14).replace(/^(\d{2})(\d)/, '$1.$2').replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3').replace(/\.(\d{3})(\d)/, '.$1/$2').replace(/(\d{4})(\d{1,2})$/, '$1-$2');
                } else {
                    value = value.substring(0, 11).replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                }
                e.target.value = value;
            });
        }
        document.getElementById("cep_button").addEventListener("click", function(event) {
            event.preventDefault();
            const cep = document.getElementById('cep').value;
            fetch(`https://viacep.com.br/ws/${somenteNumeros(cep)}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById("rua").value = data.logradouro;
                        document.getElementById("bairro").value = data.bairro;
                        document.getElementById("cidade").value = data.localidade;
                        document.getElementById("uf").value = data.uf;
                        document.getElementById("ibge").value = data.ibge;
                    } else {
                        alert("CEP não encontrado!");
                    }
                })
                .catch(error => console.error('Erro ao buscar CEP:', error));
        });
        document.getElementById("cnpj_button").addEventListener("click", function(event) {
            event.preventDefault();
            const cnpj = document.getElementById('cpf_cnpj').value;
            const tipo = document.getElementById('tipo').value;
            if (tipo == 2) {
                fetch('/clientes/cnpj', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ cnpj: somenteNumeros(cnpj) })
                })
                .then(response => response.json())
                .then(resultado => {
                    if (resultado && resultado.nome) {
                        document.getElementById('nome').value = resultado.nome;
                        document.getElementById('apelido').value = resultado.fantasia || '';
                        document.getElementById("bairro").value = resultado.bairro;
                        document.getElementById("cidade").value = resultado.municipio;
                        document.getElementById("rua").value = resultado.logradouro;
                        document.getElementById("uf").value = resultado.uf;
                        document.getElementById("cep").value = resultado.cep.replace(/\D/g, '');
                        document.getElementById("numero").value = resultado.numero;
                    } else {
                        alert("CNPJ não encontrado!");
                    }
                })
                .catch(error => console.error('Erro ao buscar CNPJ:', error));
            } else {
                alert("Para busca automática, o tipo deve ser Pessoa Jurídica!");
            }
        });
    });
</script>
@endpush

