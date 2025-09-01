@extends('adminlte::page')

@section('title', 'Cadastrar Veículo')

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

    .header-buttons .btn { display: block; margin-bottom: 8px; }
    @media (min-width: 992px) {
        .header-buttons .btn { display: inline-block; margin-bottom: 0; }
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: .5rem;
    }
    .form-control { /* A classe .form-select foi removida e unificada aqui */
        border-radius: 8px;
        border: 1px solid var(--border-color);
        height: 48px;
        width: 100%;
        padding: .375rem .75rem;
    }
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 3, 58, .25);
    }
    textarea.form-control {
        height: auto;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Cadastrar Novo Veículo</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            <a class="btn custom-btn custom-btn-secondary" href="{{ route('veiculos.index') }}">
                <i class="fas fa-arrow-left mr-1"></i> Voltar
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="card card-main">
    <div class="card-body">
        <form action="{{ route('veiculos.salvar') }}" method="post" name="veiculo">
            @csrf
            <h5 class="mb-3 font-weight-bold">Informações do Veículo</h5>
            <div class="row">
                <div class="col-md-4 mb-3"><label for="placa" class="form-label">Placa *</label><input type="text" class="form-control" required name="placa" id="placa" onkeyup="validarPlaca(this, event)" maxlength="8" value="{{ old('placa') }}"></div>
                <div class="col-md-4 mb-3"><label for="capacidade" class="form-label">Capacidade (Kg) *</label><input type="number" class="form-control" step="0.1" required min="0" name="capacidade" id="capacidade" value="{{ old('capacidade') }}"></div>
                <div class="col-md-4 mb-3"><label for="renavan" class="form-label">Renavam *</label><input type="number" class="form-control" required name="renavan" id="renavan" value="{{ old('renavan') }}"></div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3"><label for="tara" class="form-label">Tara (kg) *</label><input type="number" class="form-control" step="0.1" required min="0" name="tara" id="tara" value="{{ old('tara') }}"></div>
                <div class="col-md-3 mb-3"><label for="capacidade_m3" class="form-label">Capacidade (M³) *</label><input type="text" class="form-control" required name="capacidade_m3" id="capacidade_m3" min="0" value="{{ old('capacidade_m3') }}"></div>
                <div class="col-md-3 mb-3"><label for="tipo_carroceria" class="form-label">Tipo de Carroceria *</label><select class="form-control" required name="tipo_carroceria" id="tipo_carroceria"><option value="">Selecione</option>@foreach ($tiposCarrocerias as $tipoCarroceria)<option value="{{ $tipoCarroceria }}" @if(old('tipo_carroceria') == $tipoCarroceria->value) selected @endif>{{ $tipoCarroceria->value }}</option>@endforeach</select></div>
                <div class="col-md-3 mb-3"><label for="tipo_veiculo" class="form-label">Tipo de Veículo *</label><select class="form-control" required name="tipo_veiculo" id="tipo_veiculo"><option value="">Selecione</option>@foreach ($tiposVeiculos as $tipoVeiculo)<option value="{{ $tipoVeiculo }}" @if(old('tipo_veiculo') == $tipoVeiculo->value) selected @endif>{{ $tipoVeiculo->value }}</option>@endforeach</select></div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3"><label for="tipo_rodado" class="form-label">Tipo Rodado *</label><select class="form-control" required name="tipo_rodado" id="tipo_rodado"><option value="">Selecione</option>@foreach ($tiposRodados as $tipo_rodado)<option value="{{ $tipo_rodado }}" @if(old('tipo_rodado') == $tipo_rodado->value) selected @endif>{{ $tipo_rodado->value }}</option>@endforeach</select></div>
                <div class="col-md-3 mb-3"><label for="uf_veiculo" class="form-label">UF do Veículo *</label><select class="form-control" required name="uf_veiculo" id="uf_veiculo"><option value="">Selecione</option>@foreach ($ufs as $uf)<option value="{{ $uf }}" @if(old('uf_veiculo') == $uf->value) selected @endif>{{ $uf->value }}</option>@endforeach</select></div>
                <div class="col-md-3 mb-3"><label for="tipo_propriedade" class="form-label">Tipo Propriedade *</label><select class="form-control" required name="tipo_propriedade" id="tipo_propriedade" onchange="tipoProp(this.value)"><option value="">Selecione</option>@foreach ($tiposPropriedades as $tipoPropriedade)<option value="{{ $tipoPropriedade }}" @if(old('tipo_propriedade') == $tipoPropriedade->value) selected @endif>{{ $tipoPropriedade->value }}</option>@endforeach</select></div>
                @if (Auth::user()->empresa_id == 1)
                    <div class="col-md-3 mb-3"><label for="empresaId" class="form-label">Empresa *</label><select class="form-control" name="empresaId" id="empresaId" required><option value="">Selecione</option>@foreach ($empresas as $empresa)<option value="{{ $empresa->id }}" @if(old('empresaId') == $empresa->id) selected @endif>{{ $empresa->fantasia }}</option>@endforeach</select></div>
                @else
                    <input type="hidden" name="empresaId" value="{{ Auth::user()->empresa_id }}">
                @endif
            </div>
            
            <hr class="my-4">

            <div id="proprietario" style="display: {{ old('tipo_propriedade') == 'Terceiro' ? 'block' : 'none' }}">
                <h5 class="mb-3 font-weight-bold">Informações do Proprietário</h5>
                <div class="row">
                    <div class="col-md-4 mb-3"><label for="cpf_cnpj_prop" class="form-label">CPF/CNPJ</label><input class="form-control" type="text" name="cpf_cnpj" id="cpf_cnpj_prop" onblur="this.value = formatarCpfCnpj(this.value);" maxlength="18" value="{{ old('cpf_cnpj') }}"></div>
                    <div class="col-md-4 mb-3"><label for="nome_prop" class="form-label">Nome Proprietário</label><input class="form-control" type="text" name="nome" id="nome_prop" value="{{ old('nome') }}"></div>
                    <div class="col-md-4 mb-3"><label for="ie_prop" class="form-label">Inscrição Estadual</label><input class="form-control" type="text" name="ie" id="ie_prop" value="{{ old('ie') }}"></div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3"><label for="uf_prop" class="form-label">UF Proprietário</label><select class="form-control" name="uf_prop" id="uf_prop"><option value="">Selecione</option>@foreach ($ufs as $uf)<option value="{{ $uf }}" @if(old('uf_prop') == $uf->value) selected @endif>{{ $uf }}</option>@endforeach</select></div>
                    <div class="col-md-3 mb-3"><label for="rntrc" class="form-label">RNTRC</label><input class="form-control" type="number" name="rntrc" id="rntrc" value="{{ old('rntrc') }}"></div>
                    <div class="col-md-3 mb-3"><label for="tipo_proprietario" class="form-label">Tipo Proprietário</label><select class="form-control" name="tipo_proprietario" id="tipo_proprietario"><option value="">Selecione</option>@foreach ($tipoProprietarios as $tpProp)<option value="{{ $tpProp }}" @if(old('tipo_proprietario') == $tpProp->value) selected @endif>{{ $tpProp }}</option>@endforeach</select></div>
                    <div class="col-md-3 mb-3"><label for="tipo_transportador" class="form-label">Tipo Transportador</label><select class="form-control" name="tipo_transportador" id="tipo_transportador"><option value="">Selecione</option>@foreach ($tipoTransportadores as $tpTransp)<option value="{{ $tpTransp }}" @if(old('tipo_transportador') == $tpTransp->value) selected @endif>{{ $tpTransp }}</option>@endforeach</select></div>
                </div>
                 <div class="form-check mb-3"><input type="checkbox" class="form-check-input" name="isento" id="isento" @if(old('isento')) checked @endif><label class="form-check-label" for="isento">Isento de Inscrição Estadual</label></div>
            </div>
            
            <div class="row">
                <div class="col-12 mb-3"><label for="descricao" class="form-label">Descrição</label><textarea class="form-control" maxlength="512" name="descricao" id="descricao" rows="3">{{ old('descricao') }}</textarea></div>
            </div>
            
            <div class="text-center mt-4">
                <button class="btn btn-lg custom-btn custom-btn-success" type="submit"><i class="fas fa-save mr-2"></i> Salvar Veículo</button>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
    <script>
        function validarPlaca(entradaDoUsuario, tecla) {
            if (event.keyCode != 8) {
                var placa = entradaDoUsuario.value;
                placaMaiuscula = placa.toUpperCase();
                document.forms['veiculo']['placa'].value = placaMaiuscula;
                if (placa.length === 3) {
                    placa += "-";
                    document.forms['veiculo']['placa'].value = placa;
                    return true;
                }
            }
        }
        function tipoProp(value) {
            var prop = document.getElementById('proprietario');
            var inputs = prop.querySelectorAll('input, select, textarea');
            if (value === "Terceiro") {
                prop.style.display = 'block';
                inputs.forEach(function(input) {
                    if (input.type != 'checkbox') {
                        input.required = true;
                    }
                });
            } else {
                prop.style.display = 'none';
                inputs.forEach(function(input) {
                    input.required = false;
                });
            }
        }
        function formatarCpfCnpj(valor) {
            valor = valor.replace(/\D/g, '');
            if (valor.length === 11) {
                return valor.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            }
            else if (valor.length === 14) {
                return valor.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
            }
            else {
                return valor;
            }
        }
        document.getElementById('capacidade_m3').addEventListener('input', function() {
            var inputValue = this.value;
            var numericValue = inputValue.replace(/[^0-9.]/g, '');
            if (numericValue.indexOf('.') === -1 && numericValue.length > 0) {
                numericValue += '.';
            }
            var parts = numericValue.split('.');
            if (parts.length > 1) {
                parts[1] = parts[1].substring(0, 2);
            }
            if (parts[0].length > 1) {
                parts[0] = parts[0].substring(0, 1);
            }
            var formattedValue = parts.join('.');
            this.value = formattedValue;
        });
    </script>
@stop