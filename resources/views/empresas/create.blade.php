@extends('adminlte::page')

@section('title', 'Cadastro de Empresa')

@push('css')
<style>
    /* Estilos do Padrão Visual Definido */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    :root {
        --primary-color: #00033a;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --text-dark: #343a40;
        --text-light: #6c757d;
        --success-color: #28a745;
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
    
    .custom-btn {
        font-weight: 500;
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .custom-btn:hover {
        transform: translateY(-2px);
    }
    .custom-btn-success { background-color: var(--success-color) !important; border-color: var(--success-color) !important; color: #fff !important; }
    .custom-btn-secondary { background-color: #6c757d !important; border-color: #6c757d !important; color: #fff !important; }
    
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: .5rem;
    }
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        height: 48px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 3, 58, .25);
    }
    textarea.form-control {
        height: auto;
    }

    /* Abas customizadas */
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: var(--text-light);
        font-weight: 500;
    }
    .nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
        color: var(--primary-color);
        border-bottom-color: var(--primary-color);
        background-color: transparent;
    }
    
    .input-group-append .btn {
        border-radius: 0 8px 8px 0 !important;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Registrar Empresa</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right">
            <a href="{{ route('empresa.show') }}" class="btn custom-btn custom-btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Voltar
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="card card-main">
    <div class="card-body">
        <ul class="nav nav-tabs" id="tab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="home-tab" data-toggle="pill" href="#home" role="tab" aria-controls="home" aria-selected="true"><b>Empresa</b></a></li>
            <li class="nav-item"><a class="nav-link" id="profile-tab" data-toggle="pill" href="#profile" role="tab" aria-controls="profile" aria-selected="false"><b>Endereço</b></a></li>
            <li class="nav-item"><a class="nav-link" id="fiscal-tab" data-toggle="pill" href="#fiscal" role="tab" aria-controls="fiscal" aria-selected="false"><b>Fiscal</b></a></li>
            <li class="nav-item"><a class="nav-link" id="limite-tab" data-toggle="pill" href="#limite" role="tab" aria-controls="limite" aria-selected="false"><b>Limites</b></a></li>
            <li class="nav-item"><a class="nav-link" id="user-tab" data-toggle="pill" href="#user" role="tab" aria-controls="user" aria-selected="false"><b>Usuário</b></a></li>
        </ul>

        <form class="needs-validation mt-4" novalidate action="{{ route('salvar_empresa') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="tab-content" id="tabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label for="cpf_cnpj" class="form-label">CPF ou CNPJ</label>
                            <div class="input-group">
                                <input required class="form-control" type="text" id="cpf_cnpj" name="cpf_cnpj" onblur="this.value = formatarCpfCnpj(this.value);" maxlength="18" value="{{ old('cpf_cnpj') }}">
                                <div class="input-group-append"><button id="cnpj_button" type="button" class="btn btn-dark"><i class="fa fa-search"></i></button></div>
                                <div class="invalid-feedback">Informe um CPF/CNPJ válido.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7 mb-3"><label for="nome" class="form-label">Razão Social</label><input required class="form-control" type="text" id="nome" name="nome" value="{{ old('nome') }}"><div class="invalid-feedback">Informe uma razão social válida.</div></div>
                        <div class="col-md-5 mb-3"><label for="fantasia" class="form-label">Nome Fantasia</label><input required class="form-control" type="text" id="fantasia" name="fantasia" value="{{ old('fantasia') }}"><div class="invalid-feedback">Informe um nome fantasia válido.</div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label for="rg_ie" class="form-label">RG ou IE</label><input class="form-control" type="text" id="rg_ie" name="rg_ie" value="{{ old('rg_ie') }}"></div>
                        <div class="col-md-4 mb-3"><label for="telefone" class="form-label">Celular</label><input required class="form-control" type="text" id="telefone" name="telefone" maxlength="15" value="{{ old('telefone') }}" onkeyup="handlePhone(event)"><div class="invalid-feedback">Informe um celular válido.</div></div>
                    </div>
                </div>

                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cep" class="form-label">CEP</label>
                            <div class="input-group"><input required class="form-control" type="text" id="cep" name="cep" value="{{ old('cep') }}"><div class="input-group-append"><button class="btn btn-dark" type="button" id="cep_button"><i class="fa fa-search"></i></button></div><div class="invalid-feedback">Informe um CEP válido.</div></div>
                        </div>
                        <div class="col-md-8 mb-3"><label for="rua" class="form-label">Rua</label><input required class="form-control" type="text" id="rua" name="rua" value="{{ old('rua') }}"><div class="invalid-feedback">Informe uma rua válida.</div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3"><label for="numero" class="form-label">Número</label><input required class="form-control" type="text" id="numero" name="numero" value="{{ old('numero') }}"><div class="invalid-feedback">Informe um número válido.</div></div>
                        <div class="col-md-5 mb-3"><label for="bairro" class="form-label">Bairro</label><input required class="form-control" type="text" id="bairro" name="bairro" value="{{ old('bairro') }}"><div class="invalid-feedback">Informe um bairro válido.</div></div>
                        <div class="col-md-4 mb-3"><label for="cidade" class="form-label">Cidade</label><input class="form-control" type="text" name="cidade" id="cidade" required value="{{ old('cidade') }}"><div class="invalid-feedback">Informe uma cidade válida.</div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="uf" class="form-label">UF</label>
                            <select class="form-select" id="uf" name="uf" required>
                                <option value="">Selecione</option>
                                @foreach(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                <option value="{{ $uf }}" @if(old('uf') == $uf) selected @endif>{{ $uf }}</option>
                                @endforeach
                            </select><div class="invalid-feedback">Informe um UF válido.</div>
                        </div>
                        <div class="col-md-3 mb-3"><label for="ibge" class="form-label">Código IBGE</label><input class="form-control" type="number" id="ibge" name="ibge" value="{{ old('ibge') }}"></div>
                        <div class="col-md-6 mb-3"><label for="complemento" class="form-label">Complemento</label><textarea class="form-control" name="complemento" id="complemento" style="height: 48px;">{{ old('complemento') }}</textarea></div>
                    </div>
                </div>

                <div class="tab-pane fade" id="fiscal" role="tabpanel" aria-labelledby="fiscal-tab">
                    <div class="row">
                        <div class="col-md-4 mb-3"><label for="nfe" class="form-label">Nº da Última NFe</label><input required class="form-control" type="number" name="nfe" id="nfe" value="{{ old('nfe') }}"><div class="invalid-feedback">Informe o nº da última NFe.</div></div>
                        <div class="col-md-4 mb-3"><label for="nfce" class="form-label">Nº da Última NFCe</label><input required class="form-control" type="number" name="nfce" id="nfce" value="{{ old('nfce') }}"><div class="invalid-feedback">Informe o nº da última NFCe.</div></div>
                        <div class="col-md-4 mb-3"><label for="mdfe" class="form-label">Nº da Última MDFe</label><input required class="form-control" type="number" name="mdfe" id="mdfe" value="{{ old('mdfe') }}"><div class="invalid-feedback">Informe o nº da última MDFe.</div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3"><label for="nfse" class="form-label">Nº da Última NFS-e</label><input class="form-control" type="number" name="nfse" id="nfse" value="{{ old('nfse', 0) }}"></div>
                        <div class="col-md-3 mb-3"><label for="dps" class="form-label">Nº da Última DPS</label><input class="form-control" type="number" name="dps" id="dps" value="{{ old('dps', 0) }}"></div>
                        <div class="col-md-3 mb-3"><label for="serie_nfse" class="form-label">Série NFS-e/DPS</label><input class="form-control" type="number" name="serie_nfse" id="serie_nfse" value="{{ old('serie_nfse') }}"></div>
                        <div class="col-md-3 mb-3"><label for="inscricao_municipal" class="form-label">Inscrição Municipal</label><input class="form-control" type="text" name="inscricao_municipal" id="inscricao_municipal" maxlength="15" value="{{ old('inscricao_municipal') }}"></div>
                    </div>
<div class="row">
    <div class="col-md-2 mb-3">
        <label for="serie" class="form-label">Série</label>
        <input required class="form-control" type="number" name="serie" id="serie" value="{{ old('serie') }}">
        <div class="invalid-feedback">Informe uma série.</div>
    </div>
    <div class="col-md-3 mb-3">
        <label for="ambiente" class="form-label">Ambiente</label>
        <select required class="form-select" name="ambiente" id="ambiente">
            <option value="">Selecione</option>
            <option value="1" @if(old('ambiente')=='1') selected @endif>Produção</option>
            <option value="2" @if(old('ambiente')=='2') selected @endif>Homologação</option>
        </select>
        <div class="invalid-feedback">Informe o ambiente.</div>
    </div>
    
    {{-- NOVO CAMPO CRT ADICIONADO AQUI --}}
    <div class="col-md-3 mb-3">
        <label for="crt" class="form-label">Regime Tributário (CRT)</label>
        <select required class="form-select" name="crt" id="crt">
            <option value="" disabled selected>Selecione</option>
            <option value="1" @if(old('crt')=='1') selected @endif>1 - Simples Nacional</option>
            <option value="2" @if(old('crt')=='2') selected @endif>2 - Simples Nacional - excesso de sublimite</option>
            <option value="3" @if(old('crt')=='3') selected @endif>3 - Regime Normal (Lucro Presumido/Real)</option>
            <option value="4" @if(old('crt')=='4') selected @endif>4 - Simples Nacional - MEI</option>
        </select>
        <div class="invalid-feedback">Informe o regime tributário.</div>
    </div>

    <div class="col-md-4 mb-3">
        <label for="contador" class="form-label">E-mail do Contador</label>
        <input class="form-control" type="email" name="contador" id="contador" value="{{ old('email') }}">
    </div>
</div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <input type="hidden" name="lancar_nfe_nfce_fluxo_caixa" value="0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="lancar_nfe_nfce_fluxo_caixa"
                                    id="lancar_nfe_nfce_fluxo_caixa" value="1"
                                    {{ old('lancar_nfe_nfce_fluxo_caixa', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="lancar_nfe_nfce_fluxo_caixa">
                                    Lançar NFe e NFCe automaticamente no fluxo de caixa
                                </label>
                            </div>
                            <small class="text-muted">Quando desmarcado, novas NFe e NFCe autorizadas não criarão entrada automática no fluxo de caixa.</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3"><label for="senha" class="form-label">Senha do Certificado</label><input class="form-control" type="password" name="senha" id="senha" required><div class="invalid-feedback">Informe a senha.</div></div>
                        <div class="col-md-9 mb-3"><label for="certificado" class="form-label">Arquivo do Certificado (.pfx)</label><input accept=".pfx" type="file" name="certificado" id="certificado" class="form-control" required><div class="invalid-feedback">Informe um certificado.</div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3"><label for="csc" class="form-label">CSC</label><input required class="form-control" type="text" name="csc" id="csc" value="{{ old('csc') }}"><div class="invalid-feedback">Informe um CSC.</div></div>
                        <div class="col-md-4 mb-3"><label for="idCsc" class="form-label">ID Token CSC</label><input required class="form-control" type="text" value="{{ old('idCsc') }}" name="idCsc" id="idCsc"><div class="invalid-feedback">Informe um id token CSC.</div></div>
                    </div>
                </div>

                <div class="tab-pane fade" id="limite" role="tabpanel" aria-labelledby="limite-tab">
                    <div class="row">
                        <div class="col-md-2 mb-3"><label for="clientes" class="form-label">Limite de Clientes</label><input required class="form-control" type="number" name="clientes" id="clientes" value="{{ old('clientes') }}"><div class="invalid-feedback">Informe o limite.</div></div>
                        <div class="col-md-2 mb-3"><label for="produtos" class="form-label">Limite de Produtos</label><input required class="form-control" type="number" name="produtos" id="produtos" value="{{ old('produtos') }}"><div class="invalid-feedback">Informe o limite.</div></div>
                        <div class="col-md-2 mb-3"><label for="nfes" class="form-label">Limite de NFe</label><input required class="form-control" type="number" name="nfes" id="nfes" value="{{ old('nfes') }}"><div class="invalid-feedback">Informe o limite.</div></div>
                        <div class="col-md-2 mb-3"><label for="nfces" class="form-label">Limite de NFCe</label><input required class="form-control" type="number" name="nfces" id="nfces" value="{{ old('nfces') }}"><div class="invalid-feedback">Informe o limite.</div></div>
                        <div class="col-md-2 mb-3"><label for="nfses" class="form-label">Limite de NFS-e</label><input class="form-control" type="number" name="nfses" id="nfses" value="{{ old('nfses', 0) }}"></div>
                        <div class="col-md-2 mb-3"><label for="mdfes" class="form-label">Limite de MDFe</label><input required class="form-control" type="number" name="mdfes" id="mdfes" value="{{ old('mdfes') }}"><div class="invalid-feedback">Informe o limite.</div></div>
                    </div>
                </div>

                <div class="tab-pane fade" id="user" role="tabpanel" aria-labelledby="user-tab">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="name" class="form-label">Nome do Usuário</label><input class="form-control" type="text" name="name" placeholder=" " id="name" required value="{{ old('name') }}"><div class="invalid-feedback">Informe um nome.</div></div>
                        <div class="col-md-6 mb-3"><label for="email" class="form-label">Email de Acesso</label><input class="form-control" type="email" name="email" placeholder=" " id="email" required value="{{ old('email') }}"><div class="invalid-feedback">Informe um email.</div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="password" class="form-label">Senha</label><input required class="form-control" type="password" name="password" id="password" value="{{ old('password') }}"><div class="invalid-feedback">Informe a senha.</div></div>
                        <div class="col-md-6 mb-3"><label for="confirm_password" class="form-label">Confirmação de Senha</label><input required class="form-control" type="password" name="confirm_password" id="confirm_password" value="{{ old('confirm_password') }}"><div class="invalid-feedback">Confirme a senha.</div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cargo" class="form-label">Permissões</label>
                            <select required class="form-select" name="cargo" id="cargo">
                                <option value="">Selecione uma Permissão</option>
                                <option value="master" @if (old('cargo') == 'master') selected @endif>Master</option>
                                <option value="admin" @if (old('cargo') == 'admin') selected @endif>Admin</option>
                                <option value="client-NFe" @if (old('cargo') == 'client-NFe') selected @endif>Apenas NFe</option>
                                <option value="client-NFCe" @if (old('cargo') == 'client-NFCe') selected @endif>Apenas NFCe</option>
                                <option value="client-MDFe" @if (old('cargo') == 'client-MDFe') selected @endif>Apenas MDFe</option>
                                <option value="client-NFSe" @if (old('cargo') == 'client-NFSe') selected @endif>Apenas NFSe</option>
                                <option value="client-NFCom" @if (old('cargo') == 'client-NFCom') selected @endif>Apenas NFCom</option>
                                <option value="client-CTe" @if (old('cargo') == 'client-CTe') selected @endif>Apenas CTe</option>
                                <option value="client-advanced1" @if (old('cargo') == 'client-advanced1') selected @endif>NFe e MDFe</option>
                                <option value="client-advanced2" @if (old('cargo') == 'client-advanced2') selected @endif>NFe e NFCe</option>
                                <option value="client-advanced3" @if (old('cargo') == 'client-advanced3') selected @endif>CTe e MDFe</option>
                            </select><div class="invalid-feedback">Informe a permissão.</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <button class="btn btn-lg custom-btn custom-btn-success" type="submit">
                    <i class="fas fa-save mr-2"></i> Salvar Empresa
                </button>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
    <script>
        (() => { 'use strict'; const forms = document.querySelectorAll('.needs-validation'); Array.from(forms).forEach(form => { form.addEventListener('submit', event => { if (!form.checkValidity()) { event.preventDefault(); event.stopPropagation(); } form.classList.add('was-validated'); }, false); }); })();
        function formatarCpfCnpj(valor) { valor = valor.replace(/\D/g, ''); if (valor.length === 11) { return valor.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4'); } else if (valor.length === 14) { return valor.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5'); } else { return valor; } }
        function somenteNumeros(valor) { return valor.replace(/\D/g, ""); }
        document.getElementById("cep_button").addEventListener("click", function(event) { event.preventDefault(); const cep = document.getElementById('cep').value; $.ajax({ url: "https://viacep.com.br/ws/" + somenteNumeros(cep) + "/json/", method: 'GET', dataType: 'json', success: function(data) { document.getElementById("cep").value = data.cep; document.getElementById("ibge").value = data.ibge; document.getElementById("rua").value = data.logradouro; document.getElementById("bairro").value = data.bairro; document.getElementById("uf").value = data.uf; document.getElementById("cidade").value = data.localidade.toUpperCase(); }, }); });
        document.getElementById("cnpj_button").addEventListener("click", function(event) { event.preventDefault(); const cnpj = document.getElementById('cpf_cnpj').value; $.ajax({ type: "POST", url: "/clientes/cnpj", headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, data: { cnpj: somenteNumeros(cnpj), }, success: function(resultado) { if (resultado != 0) { document.getElementById('nome').value = resultado.nome; document.getElementById('fantasia').value = resultado.fantasia; document.getElementById('telefone').value = resultado.telefone; document.getElementById("bairro").value = resultado.bairro; document.getElementById("cidade").value = resultado.municipio.toUpperCase(); document.getElementById("rua").value = resultado.logradouro; document.getElementById("uf").value = resultado.uf; document.getElementById("cep").value = resultado.cep; document.getElementById("numero").value = resultado.numero; } else { alert("Cnpj não encontrado!"); } } }); });
        const handlePhone = (event) => { let input = event.target; input.value = phoneMask(input.value); }
        const phoneMask = (value) => { if (!value) return ""; value = value.replace(/\D/g, ''); value = value.replace(/(\d{2})(\d)/, "($1) $2"); value = value.replace(/(\d)(\d{4})$/, "$1-$2"); return value; }
    </script>
@endsection
