@extends('adminlte::page')

@section('title', 'Cadastrar Cliente')

@push('css')
<style>
    /* Estilos importados da tela de listagem para consistência */
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
    .custom-btn-primary:disabled {
        background-color: #a0a1b8;
        border-color: #a0a1b8;
        cursor: not-allowed;
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

    /* Estilo para os formulários com label acima */
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
    .input-group-text {
        border-radius: 8px;
    }

    /* Abas customizadas */
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

    /* Animação de pulso para o botão */
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(0, 3, 58, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(0, 3, 58, 0); }
        100% { box-shadow: 0 0 0 0 rgba(0, 3, 58, 0); }
    }
    .pulse-animation {
        animation: pulse 2s infinite;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-md-10">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Cadastro de Cliente</h1>
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
            <form class="needs-validation mt-4" novalidate action="{{ route('criar_cliente') }}" method="POST">
                @csrf
                {{-- Inputs hidden para empresa e código, conforme lógica original --}}
                <input type="hidden" name="empresa" id="empresa" value="{{ $user->empresa_id }}">
                <input type="hidden" name="codigo" id="codigo" value="0">
                
                <div class="tab-content" id="tabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="row">
                             <div class="col-md-4 mb-3">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select class="form-select" name="tipo" id="tipo" required>
                                    <option value="" selected disabled>Selecione...</option>
                                    <option value="2" @if (old('tipo') == '2') selected @endif>Pessoa Jurídica</option>
                                    <option value="1" @if (old('tipo') == '1') selected @endif>Pessoa Física</option>
                                </select>
                                <div class="invalid-feedback">Informe um tipo válido.</div>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label for="cpf_cnpj" class="form-label">CPF/CNPJ</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj" placeholder="Digite o CPF ou CNPJ" required value="{{ old('cpf_cnpj') }}">
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
                                <input class="form-control" type="text" name="nome" id="nome" placeholder="Nome completo ou Razão Social" value="{{ old('nome') }}" required>
                                <div class="invalid-feedback">Informe um nome válido.</div>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label for="apelido" class="form-label">Apelido / Nome Fantasia</label>
                                <input class="form-control" type="text" name="apelido" id="apelido" placeholder="Apelido ou Nome Fantasia" value="{{ old('apelido') }}" required>
                                <div class="invalid-feedback">Informe um apelido válido.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="rg_ie" class="form-label">RG / Inscrição Estadual</label>
                                <input class="form-control" type="text" name="rg_ie" id="rg_ie" required placeholder="RG ou Inscrição Estadual" value="{{ old('rg_ie') }}">
                                <div class="invalid-feedback">Informe um RG/IE válido.</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="text" id="telefone" name="telefone" class="form-control" placeholder="Telefone com DDD" maxlength="15" onkeyup="handlePhone(event)" value="{{ old('telefone') }}" required>
                                <div class="invalid-feedback">Informe um telefone válido.</div>
                            </div>
                             <div class="col-md-4 mb-3">
                                <label for="limite" class="form-label">Limite de Crédito</label>
                                <input class="form-control" type="text" name="limite" id="limite" placeholder="0,00" value="{{ old('limite') ?? 0 }}" required>
                                <div class="invalid-feedback">Informe um limite válido.</div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 text-right">
                                <button type="button" class="btn custom-btn-primary" id="btn-proximo">
                                    Próximo <i class="fas fa-arrow-right ml-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                       <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="cep" class="form-label">CEP</label>
                                <div class="input-group">
                                    <input placeholder="Digite o CEP" class="form-control" type="text" id="cep" name="cep" value="{{ old('cep') }}" />
                                     <div class="input-group-append">
                                        <button class="btn btn-dark" type="button" id="cep_button" style="border-radius: 0 8px 8px 0;"><i class="fa fa-search"></i></button>
                                    </div>
                                    <div class="invalid-feedback">Informe um CEP válido.</div>
                                </div>
                                <div id="cep-guidance" class="form-text text-primary font-weight-bold mt-2" style="display: none;">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Clique na lupa para validar e liberar o botão Salvar.
                                </div>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="rua" class="form-label">Logradouro</label>
                                <input class="form-control" type="text" name="rua" id="rua" value="{{ old('rua') }}" placeholder="Rua, Avenida, etc.">
                                <div class="invalid-feedback">Informe uma rua válida.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="numero" class="form-label">Número</label>
                                <input class="form-control" type="text" name="numero" id="numero" value="{{ old('numero') }}" placeholder="Nº">
                                <div class="invalid-feedback">Informe um nº válido.</div>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label for="bairro" class="form-label">Bairro</label>
                                <input class="form-control" type="text" name="bairro" id="bairro" value="{{ old('bairro') }}" placeholder="Bairro">
                                <div class="invalid-feedback">Informe um bairro válido.</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input class="form-control" type="text" name="cidade" id="cidade" value="{{ old('cidade') }}" placeholder="Cidade">
                                <div class="invalid-feedback">Informe uma cidade válida.</div>
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-3 mb-3">
                                <label for="uf" class="form-label">Estado</label>
                                <select name="uf" id="uf" class="form-select">
                                    <option value="">UF</option>
                                    <option value="AC" @if(old('uf') == 'AC') selected @endif>AC</option>
                                    <option value="AL" @if(old('uf') == 'AL') selected @endif>AL</option>
                                    <option value="AP" @if(old('uf') == 'AP') selected @endif>AP</option>
                                    <option value="AM" @if(old('uf') == 'AM') selected @endif>AM</option>
                                    <option value="BA" @if(old('uf') == 'BA') selected @endif>BA</option>
                                    <option value="CE" @if(old('uf') == 'CE') selected @endif>CE</option>
                                    <option value="DF" @if(old('uf') == 'DF') selected @endif>DF</option>
                                    <option value="ES" @if(old('uf') == 'ES') selected @endif>ES</option>
                                    <option value="GO" @if(old('uf') == 'GO') selected @endif>GO</option>
                                    <option value="MA" @if(old('uf') == 'MA') selected @endif>MA</option>
                                    <option value="MT" @if(old('uf') == 'MT') selected @endif>MT</option>
                                    <option value="MS" @if(old('uf') == 'MS') selected @endif>MS</option>
                                    <option value="MG" @if(old('uf') == 'MG') selected @endif>MG</option>
                                    <option value="PA" @if(old('uf') == 'PA') selected @endif>PA</option>
                                    <option value="PB" @if(old('uf') == 'PB') selected @endif>PB</option>
                                    <option value="PR" @if(old('uf') == 'PR') selected @endif>PR</option>
                                    <option value="PE" @if(old('uf') == 'PE') selected @endif>PE</option>
                                    <option value="PI" @if(old('uf') == 'PI') selected @endif>PI</option>
                                    <option value="RJ" @if(old('uf') == 'RJ') selected @endif>RJ</option>
                                    <option value="RN" @if(old('uf') == 'RN') selected @endif>RN</option>
                                    <option value="RS" @if(old('uf') == 'RS') selected @endif>RS</option>
                                    <option value="RO" @if(old('uf') == 'RO') selected @endif>RO</option>
                                    <option value="RR" @if(old('uf') == 'RR') selected @endif>RR</option>
                                    <option value="SC" @if(old('uf') == 'SC') selected @endif>SC</option>
                                    <option value="SP" @if(old('uf') == 'SP') selected @endif>SP</option>
                                    <option value="SE" @if(old('uf') == 'SE') selected @endif>SE</option>
                                    <option value="TO" @if(old('uf') == 'TO') selected @endif>TO</option>
                                </select>
                                <div class="invalid-feedback">Informe um estado válido.</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ibge" class="form-label">Cód. IBGE</label>
                                <input placeholder="Código IBGE" class="form-control" type="number" id="ibge" name="ibge" value="{{ old('ibge') }}" />
                                <div class="invalid-feedback">Informe um código IBGE válido.</div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6 mx-auto text-center">
                                <button type="submit" class="btn custom-btn-primary btn-block" id="btn-salvar" disabled>Salvar Cliente</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.needs-validation');
            const cepInput = document.getElementById('cep');
            const salvarButton = document.getElementById('btn-salvar');
            const proximoButton = document.getElementById('btn-proximo');
            const profileTabLink = document.querySelector('#profile-tab');
            const homeTabLink = document.querySelector('#home-tab');
            const cpfCnpjInput = document.getElementById('cpf_cnpj');
            const cepButton = document.getElementById('cep_button');
            const cepGuidance = document.getElementById('cep-guidance');

            let guidanceTimeout;

            function hideGuidance() {
                cepGuidance.style.display = 'none';
                cepButton.classList.remove('pulse-animation');
                if (guidanceTimeout) clearTimeout(guidanceTimeout);
            }
            
            // Validação inteligente ao submeter
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    const firstInvalidField = form.querySelector(':invalid');
                    if (firstInvalidField) {
                        const tabPane = firstInvalidField.closest('.tab-pane');
                        if (tabPane) {
                            const tabId = tabPane.id;
                            const tabLink = document.querySelector(`.nav-tabs .nav-link[href="#${tabId}"]`);
                            if (tabLink) {
                                $(tabLink).tab('show');
                                setTimeout(() => {
                                    firstInvalidField.focus();
                                }, 200); // pequeno delay para garantir que a aba trocou
                            }
                        }
                    }
                }
                form.classList.add('was-validated');
            }, false);

            // Funções de máscara
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

            // Habilita/desabilita botão salvar com base no CEP
            if (cepInput && salvarButton) {
                cepInput.addEventListener('input', function() {
                    salvarButton.disabled = cepInput.value.trim() === '';
                    if (cepInput.value.trim() !== '') {
                        hideGuidance();
                    }
                });
            }

            // Botão próximo para navegar para a próxima aba
            if (proximoButton && profileTabLink) {
                proximoButton.addEventListener('click', function() {
                    $(profileTabLink).tab('show');
                });
            }
            
            // Listener para quando a aba de endereço é mostrada
            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                if(e.target.id === 'profile-tab' && cepInput.value.trim() !== '' && salvarButton.disabled) {
                    cepGuidance.style.display = 'block';
                    cepButton.classList.add('pulse-animation');
                    guidanceTimeout = setTimeout(hideGuidance, 7000);
                }
            });


            // Nova máscara para CPF/CNPJ
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

            // Event Listeners para buscas
            cepButton.addEventListener("click", function(event) {
                event.preventDefault();
                hideGuidance();
                const cep = cepInput.value;
                fetch(`https://viacep.com.br/ws/${somenteNumeros(cep)}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById("rua").value = data.logradouro;
                            document.getElementById("bairro").value = data.bairro;
                            document.getElementById("cidade").value = data.localidade;
                            document.getElementById("uf").value = data.uf;
                            document.getElementById("ibge").value = data.ibge;
                            if (salvarButton) salvarButton.disabled = false;
                        } else {
                            alert("CEP não encontrado!");
                        }
                    })
                    .catch(error => console.error('Erro ao buscar CEP:', error));
            });

            document.getElementById("cnpj_button").addEventListener("click", function(event) {
                event.preventDefault();
                const cnpj = cpfCnpjInput.value;
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

