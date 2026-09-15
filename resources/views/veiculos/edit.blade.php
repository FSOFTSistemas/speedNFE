@extends('adminlte::page')

@section('title', 'Editar Veículo')

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
        --warning-color: #ffc107;
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
    .custom-btn-warning { background-color: var(--warning-color) !important; border-color: var(--warning-color) !important; color: #212529 !important; }
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
    .form-control[readonly] {
        background-color: #e9ecef;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Editar Veículo</h1>
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
        <form action="{{ route('veiculos.update', [$veiculo->id]) }}" method="post" name="veiculo">
            @csrf
            @method('PUT')
            <h5 class="mb-3 font-weight-bold">Informações do Veículo</h5>
            <div class="row">
                <div class="col-md-4 mb-3"><label for="placa" class="form-label">Placa *</label><input type="text" class="form-control" required name="placa" id="placa" onkeyup="validarPlaca(this)" maxlength="8" value="{{ $veiculo->placa }}"></div>
                <div class="col-md-4 mb-3"><label for="capacidade" class="form-label">Capacidade (Kg) *</label><input type="number" class="form-control" step="0.1" required name="capacidade" id="capacidade" value="{{ $veiculo->capacidade }}"></div>
                <div class="col-md-4 mb-3"><label for="renavan" class="form-label">Renavam *</label><input type="number" class="form-control" required name="renavan" id="renavan" value="{{ $veiculo->renavan }}"></div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3"><label for="tara" class="form-label">Tara (kg) *</label><input type="number" class="form-control" step="0.1" required name="tara" id="tara" value="{{ $veiculo->tara }}"></div>
                <div class="col-md-3 mb-3"><label for="capacidade_m3" class="form-label">Capacidade (M³) *</label><input type="text" class="form-control" required name="capacidade_m3" id="capacidade_m3" value="{{ $veiculo->capacidade_m3 }}"></div>
                <div class="col-md-3 mb-3"><label for="tipo_carroceria" class="form-label">Tipo de Carroceria *</label><select class="form-control" required name="tipo_carroceria" id="tipo_carroceria">@foreach ($tiposCarrocerias as $tipoCarroceria)<option value="{{ $tipoCarroceria->value }}" @if($veiculo->tipo_carroceria == $tipoCarroceria->value) selected @endif>{{ $tipoCarroceria->value }}</option>@endforeach</select></div>
                <div class="col-md-3 mb-3"><label for="tipo_veiculo" class="form-label">Tipo de Veículo *</label><select class="form-control" required name="tipo_veiculo" id="tipo_veiculo">@foreach ($tiposVeiculos as $tipoVeiculo)<option value="{{ $tipoVeiculo->value }}" @if($veiculo->tipo_veiculo == $tipoVeiculo->value) selected @endif>{{ $tipoVeiculo->value }}</option>@endforeach</select></div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3"><label for="tipo_rodado" class="form-label">Tipo Rodado *</label><select class="form-control" required name="tipo_rodado" id="tipo_rodado">@foreach ($tiposRodados as $tipo_rodado)<option value="{{ $tipo_rodado->value }}" @if($veiculo->tipo_rodado == $tipo_rodado->value) selected @endif>{{ $tipo_rodado->value }}</option>@endforeach</select></div>
                <div class="col-md-3 mb-3"><label for="uf_veiculo" class="form-label">UF do Veículo *</label><select class="form-control" required name="uf_veiculo" id="uf_veiculo">@foreach ($ufs as $uf)<option value="{{ $uf->value }}" @if($veiculo->uf_veiculo == $uf->value) selected @endif>{{ $uf->value }}</option>@endforeach</select></div>
                <div class="col-md-3 mb-3"><label for="tipo_propriedade" class="form-label">Tipo Propriedade *</label><select class="form-control" required name="tipo_propriedade" id="tipo_propriedade" readonly><option value="{{ $veiculo->tipo_propriedade }}">{{ $veiculo->tipo_propriedade }}</option></select></div>
                <div class="col-md-3 mb-3"><label for="empresaId" class="form-label">Empresa</label><select class="form-control" name="empresaId" id="empresaId" required readonly><option value="{{ $veiculo->empresaId }}">{{ $veiculo->fantasia }}</option></select></div>
            </div>
            
            @if ($veiculo->tipo_propriedade == 'Terceiro' && isset($veiculo->proprietario))
                <hr class="my-4">
                <h5 class="mb-3 font-weight-bold">Informações do Proprietário</h5>
                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label">CPF/CNPJ</label><input class="form-control" type="text" name="cpf_cnpj" onblur="this.value = formatarCpfCnpj(this.value);" maxlength="18" required value="{{ $veiculo->proprietario->cpf_cnpj }}"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Nome Proprietário</label><input class="form-control" type="text" name="nome" required value="{{ $veiculo->proprietario->nome_proprietario }}"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Inscrição Estadual</label><input class="form-control" type="text" name="ie" required value="{{ $veiculo->proprietario->ie }}"></div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3"><label class="form-label">UF Proprietário</label><select class="form-control" name="uf_prop" required>@foreach ($ufs as $uf)<option value="{{ $uf->value }}" @if($veiculo->proprietario->uf_proprietario == $uf->value) selected @endif>{{ $uf->value }}</option>@endforeach</select></div>
                    <div class="col-md-3 mb-3"><label class="form-label">RNTRC</label><input class="form-control" type="text" name="rntrc" required value="{{ $veiculo->proprietario->rntrc }}"></div>
                    <div class="col-md-3 mb-3"><label class="form-label">Tipo Proprietário</label><select class="form-control" name="tipo_proprietario" required>@foreach ($tipoProprietarios as $tpProp)<option value="{{ $tpProp->value }}" @if($veiculo->proprietario->tipo_proprietario == $tpProp->value) selected @endif>{{ $tpProp->value }}</option>@endforeach</select></div>
                    <div class="col-md-3 mb-3"><label class="form-label">Tipo Transportador</label><select class="form-control" name="tipo_transportador" required>@foreach ($tipoTransportadores as $tpTransp)<option value="{{ $tpTransp->value }}" @if($veiculo->proprietario->tipo_transportador == $tpTransp->value) selected @endif>{{ $tpTransp->value }}</option>@endforeach</select></div>
                </div>
                 <div class="form-check mb-3"><input type="checkbox" class="form-check-input" name="isento" id="isento" {{ $veiculo->proprietario->isento ? 'checked' : '' }}><label class="form-check-label" for="isento">Isento de Inscrição Estadual</label></div>
            @endif
            
            <div class="row mt-3">
                <div class="col-12 mb-3"><label for="descricao" class="form-label">Descrição</label><textarea class="form-control" maxlength="512" name="descricao" id="descricao" rows="3">{{ $veiculo->descricao }}</textarea></div>
            </div>
            
            <div class="text-center mt-4">
                <button class="btn btn-lg custom-btn custom-btn-warning" type="submit"><i class="fas fa-save mr-2"></i> Salvar Alterações</button>
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
        function formatarCpfCnpj(valor) {
            valor = valor.replace(/\D/g, '');
            if (valor.length === 11) {
                return valor.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            } else if (valor.length === 14) {
                return valor.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
            } else {
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