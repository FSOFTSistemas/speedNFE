@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    {{-- <div class="row" style="text-align: center">
        <div class="col-md-6 col-xs-10">
            <h5 class="m-0 text-dark">Editar produto</h5>
        </div>
    </div> --}}
    <br>
@stop

@section('content')
    <div class="content">
        <a class="btn btn-secondary" style="margin-bottom: 2%" href="{{ route('produto.index') }}">Voltar</a>
        <div class="container-fluid">
            <div class="col-xs-12 col-sm-12" style="width: 100%">

                <div class="card card-primary card-outline card-tabs">

                    <div class="card-header p-0 pt-1 border-bottom-0">
                        <ul class="nav nav-tabs" id="tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="pill" href="#home" role="tab"
                                    aria-controls="home" aria-selected="true">Informações do Produto</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="pill" href="#profile" role="tab"
                                    aria-controls="profile" aria-selected="false">Informações Fiscais</a>
                            </li>
                            <li class="nav-item" style="display: {{ $produto->tpProd ? 'block' : 'none' }}" id="veicTab">
                                <a class="nav-link" id="veic-tab" data-toggle="pill" href="#veic" role="tab"
                                    aria-controls="veic" aria-selected="false">Informações de Veículo</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('update_produto', ['id' => $produto->id]) }}">
                            @csrf
                            @method('PUT')
                            <div class="tab-content" id="tabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel"
                                    aria-labelledby="home-tab">


                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label>Empresa</label>
                                            <select class="form-control" name="empresa" id="empresa" disabled>
                                                <option>
                                                    {{ $produto->fantasia }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label>Categoria</label>
                                            <select class="form-control" name="categoria" id="categoria" required>
                                                <option value="{{ $produto->categoria_id }}">
                                                    {{ $produto->descricao }}</option>
                                                @foreach ($categorias as $categoria)
                                                    <option value="{{ $categoria->id }}">
                                                        {{ $categoria->descricao }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="codigo">Código de Barras</label>
                                            <input class="form-control" type="text" name="codigo" id="codigo"
                                                value="{{ $produto->codigo }}">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="produto">Produto</label>
                                            <input class="form-control" type="text" name="produto" id="produto"
                                                value="{{ $produto->produto }}" required placeholder="Produto...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="ncm">NCM</label>
                                            <input class="form-control" type="text" name="ncm" id="ncm"
                                                value="{{ $produto->ncm }}" required placeholder="Ncm...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="precocusto">Preço Custo</label>
                                            <input class="form-control" type="number" name="precocusto" id="precocusto"
                                                value="{{ $produto->precocusto }}" required placeholder="Preço Custo...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5 col-xs-10">
                                            <label for="precovenda">Preço de Venda</label>
                                            <div class="row">
                                                <input class="form-control" type="number" name="precovenda"
                                                    id="precovenda" value="{{ $produto->precovenda }}" required
                                                    placeholder="Preço Venda...">
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-10">
                                            <label for="un">Unidade</label>
                                            <select name="un" id="un" class="form-control" required>
                                                <option value="{{ $produto->un }}">{{ $produto->un }}</option>
                                                <option value="un">UN</option>
                                                <option value="cx">CX</option>
                                                <option value="kg">KG</option>
                                                <option value="l">L</option>
                                                <option value="ml">ML</option>
                                                <option value="m">M</option>
                                                <option value="cm">CM</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-xs-2">
                                            <label for="veic">Veículo?</label>
                                            <br>
                                            <input type="checkbox" readonly name="tpProd" id="tpProd" @if($produto->tpProd) checked @endif>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cfopinterno">CFOP Interno</label>
                                            <input class="form-control" type="text" name="cfopinterno"
                                                id="cfopinterno" value="{{ $produto->cfop_interno }}" required
                                                placeholder="Cfop Interno...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cfopexterno">CFOP Externo</label>
                                            <input class="form-control" type="text" name="cfopexterno"
                                                id="cfopexterno" value="{{ $produto->cfop_externo }}" required
                                                placeholder="Cfop Externo...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst">CST</label>
                                            <input class="form-control" type="text" name="cst" id="cst"
                                                value="{{ $produto->cst }}" required placeholder="Cst...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst_pis">CST/PIS</label>
                                            <input class="form-control" type="text" name="cst_pis" id="cst_pis"
                                                value="{{ $produto->cst_pis }}" required placeholder="Cst Pis...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst_cofins">CST/COFINS</label>
                                            <input class="form-control" type="text" name="cst_cofins" id="cst_cofins"
                                                value="{{ $produto->cst_cofins }}" required placeholder="Cst Cofins...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cofins">COFINS</label>
                                            <div class="row">
                                                <div class="col-md-6 col-xs-10">
                                                    <input class="form-control" type="text" name="cofins"
                                                        id="cofins" value="{{ $produto->cofins }}" required
                                                        placeholder="Cofins...">
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-6 col-xs-10">
                                            <label for="icms">ICMS</label>
                                            <input class="form-control" type="text" name="icms" id="icms"
                                                value="{{ $produto->icms }}" required placeholder="Icms...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="cst_csosn">CST/CSOSN</label>
                                            <input type="text" class="form-control" name="cst_csosn" id="cst_csosn"
                                                value="{{ $produto->cst_csosn }}" required placeholder="Cst Csosn...">
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-6 col-xs-10">
                                            <label for="pis">PIS</label>
                                            <input class="form-control" type="text" name="pis" id="pis"
                                                value="{{ $produto->pis }}" required placeholder="Pis...">
                                        </div>
                                        <div class="col-md-6 col-xs-10">
                                            <label for="ipi">IPI</label>
                                            <input type="text" class="form-control" name="ipi" id="ipi"
                                                value="{{ $produto->ipi }}" required placeholder="Ipi...">
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="veic" role="tabpanel" aria-labelledby="veic-tab">
                                    @if($produto->tpProd)
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="tpVeic" class="col-sm-6 col-form-label">Tipo de Veículo</label>
                                            <select class="form-control" name="tpVeic" id="tpVeic" required>
                                                <option value="02" {{ $produto->tpVeic == '02' ? 'selected' : '' }}>
                                                    CICLOMOTOR</option>
                                                <option value="03" {{ $produto->tpVeic == '03' ? 'selected' : '' }}>
                                                    MOTONETA</option>
                                                <option value="04" {{ $produto->tpVeic == '04' ? 'selected' : '' }}>
                                                    MOTOCICLO</option>
                                                <option value="05" {{ $produto->tpVeic == '05' ? 'selected' : '' }}>
                                                    TRICICLO</option>
                                                <option value="06" {{ $produto->tpVeic == '06' ? 'selected' : '' }}>
                                                    AUTOMÓVEL</option>
                                                <option value="07" {{ $produto->tpVeic == '07' ? 'selected' : '' }}>
                                                    MICROÔNIBUS</option>
                                                <option value="08" {{ $produto->tpVeic == '08' ? 'selected' : '' }}>
                                                    ÔNIBUS</option>
                                                <option value="10" {{ $produto->tpVeic == '10' ? 'selected' : '' }}>
                                                    REBOQUE</option>
                                                <option value="11" {{ $produto->tpVeic == '11' ? 'selected' : '' }}>
                                                    SEMIREBOQUE</option>
                                                <option value="13" {{ $produto->tpVeic == '13' ? 'selected' : '' }}>
                                                    CAMINHONETA</option>
                                                <option value="14" {{ $produto->tpVeic == '14' ? 'selected' : '' }}>
                                                    CAMINHÃO</option>
                                                <option value="17" {{ $produto->tpVeic == '17' ? 'selected' : '' }}>
                                                    C.TRATOR</option>
                                                <option value="22" {{ $produto->tpVeic == '22' ? 'selected' : '' }}>
                                                    ESP/ÔNIBUS</option>
                                                <option value="23" {{ $produto->tpVeic == '23' ? 'selected' : '' }}>
                                                    MISTO/CAM</option>
                                                <option value="24" {{ $produto->tpVeic == '24' ? 'selected' : '' }}>
                                                    CARGA/CAM</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="chassiVeic" class="col-sm-2 col-form-label">Chassi</label>
                                            <input type="text" class="form-control" id="chassiVeic" required name="chassiVeic"
                                                oninput="this.value = this.value.toUpperCase()"
                                                value="{{ $produto->chassiVeic }}" placeholder="Chassi...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="renavanVeic" class="col-sm-4 col-form-label">Renavan</label>
                                            <input type="text" class="form-control" id="renavanVeic" required
                                                name="renavanVeic" value="{{ $produto->renavanVeic }}"
                                                placeholder="Renavan...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="anoFabVeic" class="col-sm-6 col-form-label">Ano de
                                                Fabricação</label>
                                            <input type="number" class="form-control" id="anoFabVeic" required name="anoFabVeic"
                                                min="1950" max="{{ date('Y') }}"
                                                value="{{ $produto->anoFabVeic }}" placeholder="Ano de Fabricação...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="anoModVeic" class="col-sm-6 col-form-label">Ano de Modelo</label>
                                            <input type="number" class="form-control" id="anoModVeic" required name="anoModVeic"
                                                min="1950" max="{{ date('Y') + 1 }}"
                                                value="{{ $produto->anoModVeic }}" placeholder="Ano de Modelo...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="pesoLVeic" class="col-sm-4 col-form-label">Peso Líquido</label>
                                            <input type="number" class="form-control" step="0.01" id="pesoLVeic" required
                                                name="pesoLVeic" value="{{ $produto->pesoLVeic }}"
                                                placeholder="Peso Líquido...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="pesoBVeic" class="col-sm-4 col-form-label">Peso Bruto</label>
                                            <input type="number" class="form-control" id="pesoBVeic" required name="pesoBVeic"
                                                value="{{ $produto->pesoBVeic }}" placeholder="Peso Bruto...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="distVeic" class="col-sm-4 col-form-label">Distância</label>
                                            <input type="text" class="form-control" id="distVeic" required name="distVeic"
                                                value="{{ $produto->distVeic }}" placeholder="Distância...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-5">
                                            <label for="combVeic" class="col-sm-4 col-form-label">Combustível</label>
                                            <select class="form-control" name="combVeic" id="combVeic" required>
                                                <option value="1" {{ $produto->combVeic == '1' ? 'selected' : '' }}>
                                                    ÁLCOOL</option>
                                                <option value="2" {{ $produto->combVeic == '2' ? 'selected' : '' }}>
                                                    GASOLINA</option>
                                                <option value="3" {{ $produto->combVeic == '3' ? 'selected' : '' }}>
                                                    DIESEL</option>
                                                <option value="16" {{ $produto->combVeic == '16' ? 'selected' : '' }}>
                                                    ÁLCOOL/GASOLINA</option>
                                                <option value="17" {{ $produto->combVeic == '17' ? 'selected' : '' }}>
                                                    GASOLINA/ÁLCOOL/GNV</option>
                                                <option value="18" {{ $produto->combVeic == '18' ? 'selected' : '' }}>
                                                    GASOLINA/ELÉTRICO</option>
                                            </select>
                                        </div>
                                        <div class="col-md-7">
                                            <label for="nMotorVeic" class="col-sm-4 col-form-label">Número do
                                                Motor</label>
                                            <input type="text" class="form-control" id="nMotorVeic" required name="nMotorVeic"
                                                value="{{ $produto->nMotorVeic }}" placeholder="Número do Motor...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="cvVeic" class="col-sm-4 col-form-label">Potência</label>
                                            <input type="number" step="0.01" class="form-control" id="cvVeic" required
                                                name="cvVeic" value="{{ $produto->cVVeic }}" placeholder="Potência...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cm3Veic" class="col-sm-6 col-form-label">Cilindradas</label>
                                            <input type="number" step="0.01" class="form-control" id="cm3Veic" required
                                                name="cm3Veic" value="{{ $produto->cm3Veic }}"
                                                placeholder="Cilindradas...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="serieVeic" class="col-sm-4 col-form-label">Série</label>
                                            <input type="text" class="form-control" id="serieVeic" required name="serieVeic"
                                                value="{{ $produto->serieVeic }}" placeholder="Série...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="tpPVeic" class="col-sm-6 col-form-label">Tipo de Pintura</label>
                                            <input type="text" class="form-control" id="tpPVeic" required name="tpPVeic"
                                                value="{{ $produto->tpPVeic }}" placeholder="Tipo de Pintura...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="corVeic" class="col-sm-2 col-form-label">Cor</label>
                                            <input type="text" class="form-control" id="corVeic" required name="corVeic"
                                                value="{{ $produto->corVeic }}"
                                                oninput="this.value = this.value.toUpperCase()" placeholder="Cor...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cCorVeic" class="col-sm-6 col-form-label">Código de Cor</label>
                                            <select class="form-control" name="cCorVeic" id="cCorVeic" required>
                                                <option value="01" {{ $produto->cCorVeic == '01' ? 'selected' : '' }}>
                                                    AMARELO</option>
                                                <option value="02" {{ $produto->cCorVeic == '02' ? 'selected' : '' }}>
                                                    AZUL</option>
                                                <option value="03" {{ $produto->cCorVeic == '03' ? 'selected' : '' }}>
                                                    BEGE</option>
                                                <option value="04" {{ $produto->cCorVeic == '04' ? 'selected' : '' }}>
                                                    BRANCA</option>
                                                <option value="05" {{ $produto->cCorVeic == '05' ? 'selected' : '' }}>
                                                    CINZA</option>
                                                <option value="06" {{ $produto->cCorVeic == '06' ? 'selected' : '' }}>
                                                    DOURADA</option>
                                                <option value="07" {{ $produto->cCorVeic == '07' ? 'selected' : '' }}>
                                                    GRENAR</option>
                                                <option value="08" {{ $produto->cCorVeic == '08' ? 'selected' : '' }}>
                                                    LARANJA</option>
                                                <option value="09" {{ $produto->cCorVeic == '09' ? 'selected' : '' }}>
                                                    MARROM</option>
                                                <option value="10" {{ $produto->cCorVeic == '10' ? 'selected' : '' }}>
                                                    PRATA</option>
                                                <option value="11" {{ $produto->cCorVeic == '11' ? 'selected' : '' }}>
                                                    PRETA</option>
                                                <option value="12" {{ $produto->cCorVeic == '12' ? 'selected' : '' }}>
                                                    ROSA</option>
                                                <option value="13" {{ $produto->cCorVeic == '13' ? 'selected' : '' }}>
                                                    ROXA</option>
                                                <option value="14" {{ $produto->cCorVeic == '14' ? 'selected' : '' }}>
                                                    VERDE</option>
                                                <option value="15" {{ $produto->cCorVeic == '15' ? 'selected' : '' }}>
                                                    VERMELHA</option>
                                                <option value="16" {{ $produto->cCorVeic == '16' ? 'selected' : '' }}>
                                                    FANTASIA</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="cCorMontVeic" class="col-sm-8 col-form-label">Código de Cor
                                                Montadora</label>
                                            <input type="text" class="form-control" id="cCorMontVeic" required
                                                name="cCorMontVeic" value="{{ $produto->cCorMontVeic }}"
                                                placeholder="Código de Cor Montadora...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cMarcaVeic" class="col-sm-8 col-form-label">Código da
                                                Marca</label>
                                            <input type="text" class="form-control" id="cMarcaVeic" required name="cMarcaVeic"
                                                value="{{ $produto->cMarcaVeic }}" placeholder="Código da Marca...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="condVeic" class="col-sm-8 col-form-label">Condição do
                                                Veículo</label>
                                            <select class="form-control" name="condVeic" id="condVeic" required>
                                                <option value="0" {{ $produto->condVeic == '0' ? 'selected' : '' }}>
                                                    ACABADO</option>
                                                <option value="1" {{ $produto->condVeic == '1' ? 'selected' : '' }}>
                                                    INACABADO</option>
                                                <option value="2" {{ $produto->condVeic == '2' ? 'selected' : '' }}>
                                                    SEMIACABO</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="espVeic" class="col-sm-8 col-form-label">Especificação do
                                                Veículo</label>
                                            <select class="form-control" name="espVeic" id="espVeic" required>
                                                <option value="1" {{ $produto->espVeic == '1' ? 'selected' : '' }}>
                                                    PASSAGEIRO</option>
                                                <option value="2" {{ $produto->espVeic == '2' ? 'selected' : '' }}>
                                                    CARGA</option>
                                                <option value="3" {{ $produto->espVeic == '3' ? 'selected' : '' }}>
                                                    MISTO</option>
                                                <option value="4" {{ $produto->espVeic == '4' ? 'selected' : '' }}>
                                                    CORRIDA</option>
                                                <option value="5" {{ $produto->espVeic == '5' ? 'selected' : '' }}>
                                                    TRAÇÃO</option>
                                                <option value="6" {{ $produto->espVeic == '6' ? 'selected' : '' }}>
                                                    ESPECIAL</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="vinVeic" class="col-sm-6 col-form-label">VIN do Veículo</label>
                                            <select class="form-control" name="vinVeic" id="vinVeic" required>
                                                <option value="N" {{ $produto->vinVeic == 'N' ? 'selected' : '' }}>
                                                    NORMAL</option>
                                                <option value="R" {{ $produto->vinVeic == 'R' ? 'selected' : '' }}>
                                                    REMARCADO</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="lotVeic" class="col-sm-6 col-form-label">Lotação Máxima</label>
                                            <input type="text" class="form-control" id="lotVeic" required name="lotVeic"
                                                value="{{ $produto->lotVeic }}" placeholder="Lotação Máxima...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-5">
                                            <label for="restriVeic" class="col-sm-6 col-form-label">Restrição do
                                                Veículo</label>
                                            <select class="form-control" name="restriVeic" id="restriVeic" required>
                                                <option value="0"
                                                    {{ $produto->restriVeic == '0' ? 'selected' : '' }}>NÃO HÁ</option>
                                                <option value="1"
                                                    {{ $produto->restriVeic == '1' ? 'selected' : '' }}>ALIENAÇÃO
                                                    FIDUNCIÁRIA</option>
                                                <option value="2"
                                                    {{ $produto->restriVeic == '2' ? 'selected' : '' }}>ARRENDAMENTO
                                                    MERCANTIL</option>
                                                <option value="3"
                                                    {{ $produto->restriVeic == '3' ? 'selected' : '' }}>RESERVA DE DOMÍNIO
                                                </option>
                                                <option value="4"
                                                    {{ $produto->restriVeic == '4' ? 'selected' : '' }}>PENHOR DE VEÍCULOS
                                                </option>
                                                <option value="9"
                                                    {{ $produto->restriVeic == '9 ' ? 'selected' : '' }}>OUTRAS</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="cargaVeic" class="col-sm-12 col-form-label">Carga Máxima</label>
                                            <input type="number" class="form-control" id="cargaVeic" required name="cargaVeic"
                                                value="{{ $produto->cargaVeic }}" placeholder="Carga Máxima...">
                                        </div>
                                        <div class="col-md-5">
                                            <label for="operVeic" class="col-sm-8 col-form-label">Tipo de Operação</label>
                                            <select class="form-control" name="operVeic" id="operVeic" required>
                                                <option value="1" {{ $produto->operVeic == '1' ? 'selected' : '' }}>
                                                    VENDA CONCERSSIONÁRIA</option>
                                                <option value="2" {{ $produto->operVeic == '2' ? 'selected' : '' }}>
                                                    FATURAMENTO DIRETO PARA CONSUMIDOR FINAL</option>
                                                <option value="3" {{ $produto->operVeic == '3' ? 'selected' : '' }}>
                                                    VENDA DIRETO PARA GRANDES CONSUMIDORES</option>
                                                <option value="0" {{ $produto->operVeic == '0' ? 'selected' : '' }}>
                                                    OUTRAS</option>
                                            </select>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row" style="text-align: center; margin-top: 2%;">
                                <div class="col">
                                    <button class="btn btn-success form-control" type="submit">Salvar Produto</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
