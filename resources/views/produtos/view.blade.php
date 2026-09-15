@extends('adminlte::page')

@php
    $ehRamoMotos = ($empresa->ramo_atividade ?? null) === 'motos';
@endphp

@section('title', $ehRamoMotos ? 'Visualizar Veículo' : 'Visualizar Produto')

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
        --text-light: #6c757d;
        --label-color: #495057;
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
    .custom-btn-edit {
        background-color: #28a745;
        border-color: #28a745;
        color: #fff;
        font-weight: 500;
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
     .custom-btn-edit:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    
    /* Estilo para a visualização dos dados */
    .data-item {
        margin-bottom: 1.5rem;
    }
    .data-label {
        font-weight: 600;
        color: var(--label-color);
        font-size: 0.9rem;
        display: block;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
    }
    .data-value {
        font-size: 1.1rem;
        color: var(--text-dark);
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        word-wrap: break-word;
    }

    /* Abas customizadas */
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: var(--text-light);
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
        <div class="col-md-8">
            <h1 class="m-0 text-dark" style="font-weight: 600;">{{ $ehRamoMotos ? 'Visualizar Veículo' : 'Visualizar Produto' }}</h1>
        </div>
        <div class="col-md-4 text-md-right mt-2 mt-md-0">
            <a href="{{ route('editar_produto', ['id' => $produto->id]) }}" class="btn custom-btn-edit mr-2">
                <i class="far fa-edit mr-1"></i> Editar
            </a>
            <a href="{{ route('produto.index') }}" class="btn custom-btn-secondary">Voltar</a>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-main">
        <div class="card-body">
            <ul class="nav nav-tabs" id="tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="pill" href="#home" role="tab"
                        aria-controls="home" aria-selected="true"><b>{{ $ehRamoMotos ? 'Informações do Veículo' : 'Informações do Produto' }}</b></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="pill" href="#profile" role="tab"
                        aria-controls="profile" aria-selected="false"><b>Informações Fiscais</b></a>
                </li>
                @if($produto->tpProd)
                <li class="nav-item" id="veicTab">
                    <a class="nav-link" id="veic-tab" data-toggle="pill" href="#veic" role="tab"
                        aria-controls="veic" aria-selected="false"><b>Informações de Veículo</b></a>
                </li>
                @endif
            </ul>
            <div class="tab-content mt-4" id="tabContent">
                {{-- ABA 1: INFORMAÇÕES DO PRODUTO --}}
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row">
                        <div class="col-md-6 data-item">
                            <span class="data-label">{{ $ehRamoMotos ? 'Veículo' : 'Produto' }}</span>
                            <p class="data-value">{{ $produto->produto }}</p>
                        </div>
                        <div class="col-md-6 data-item">
                            <span class="data-label">Categoria</span>
                            <p class="data-value">{{ $produto->categoria->descricao }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 data-item">
                            <span class="data-label">Cód. de Barras</span>
                            <p class="data-value">{{ $produto->codigo ?: 'N/A' }}</p>
                        </div>
                        <div class="col-md-3 data-item">
                            <span class="data-label">NCM</span>
                            <p class="data-value">{{ $produto->ncm }}</p>
                        </div>
                        <div class="col-md-3 data-item">
                            <span class="data-label">Preço de Custo</span>
                            <p class="data-value">R$ {{ number_format($produto->precocusto, 2, ',', '.') }}</p>
                        </div>
                        <div class="col-md-3 data-item">
                            <span class="data-label">Preço de Venda</span>
                            <p class="data-value">R$ {{ number_format($produto->precovenda, 2, ',', '.') }}</p>
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-3 data-item">
                            <span class="data-label">Unidade</span>
                            <p class="data-value">{{ strtoupper($produto->un) }}</p>
                        </div>
                        {{-- <div class="col-md-3 data-item">
                            <span class="data-label">Empresa</span>
                            <p class="data-value">{{ $produto->fantasia }}</p>
                        </div> --}}
                        <div class="col-md-3 data-item">
                            <span class="data-label">É Veículo?</span>
                            <p class="data-value">{{ $produto->tpProd ? 'Sim' : 'Não' }}</p>
                        </div>
                    </div>
                </div>

                {{-- ABA 2: INFORMAÇÕES FISCAIS --}}
{{-- ABA 2: INFORMAÇÕES FISCAIS --}}
<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
    
    {{-- Identificador do Regime --}}
    <div class="row mb-3">
        <div class="col-12">
            <span class="badge badge-primary p-2" style="font-size: 0.9rem; background-color: var(--primary-color)">
                <i class="fas fa-university mr-2"></i> 
                REGIME: {{ in_array($empresa->crt, [1, 2, 4]) ? 'SIMPLES NACIONAL / MEI' : 'REGIME NORMAL' }}
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 data-item">
            <span class="data-label">CFOP Interno</span>
            <p class="data-value">{{ $produto->cfop_interno }}</p>
        </div>
        <div class="col-md-6 data-item">
            <span class="data-label">CFOP Externo</span>
            <p class="data-value">{{ $produto->cfop_externo }}</p>
        </div>
    </div>

    <div class="row">
        @if(in_array($empresa->crt, [1, 2, 4]))
            {{-- Visualização para Simples Nacional / MEI --}}
            <div class="col-md-12 data-item">
                <span class="data-label">CSOSN (Simples Nacional)</span>
                <p class="data-value">
                    @switch($produto->cst_csosn)
                        @case('101') 101 - Tributada com permissão de crédito @break
                        @case('102') 102 - Tributada sem permissão de crédito @break
                        @case('103') 103 - Isenção do ICMS @break
                        @case('201') 201 - Tributada com perm. crédito e cobrança de ST @break
                        @case('400') 400 - Não tributada @break
                        @case('500') 500 - ICMS cobrado anteriormente por ST @break
                        @case('900') 900 - Outros @break
                        @default {{ $produto->cst_csosn }}
                    @endswitch
                </p>
            </div>
        @else
            {{-- Visualização para Regime Normal (CRT 3) --}}
            <div class="col-md-12 data-item">
                <span class="data-label">CST (ICMS - Regime Normal)</span>
                <p class="data-value">
                    @switch($produto->cst)
                        @case('00') 00 - Tributação integral @break
                        @case('10') 10 - Tributação com ICMS e acréscimo de ST @break
                        @case('20') 20 - Redução de base de cálculo @break
                        @case('40') 40 - Isenta @break
                        @case('60') 60 - ICMS cobrado anteriormente por ST @break
                        @case('90') 90 - Outras operações @break
                        @default {{ $produto->cst }}
                    @endswitch
                </p>
            </div>
        @endif
    </div>

    <div class="row">
        <div class="col-md-6 data-item">
            <span class="data-label">CST/PIS</span>
            <p class="data-value">
                @if($produto->cst_pis == '1') 01 - Operação Tributável (Alíquota Básica) 
                @elseif($produto->cst_pis == '49') 49 - Outras Operações de Saída
                @elseif($produto->cst_pis == '99') 99 - Outras Operações
                @else {{ $produto->cst_pis }} @endif
            </p>
        </div>
        <div class="col-md-6 data-item">
            <span class="data-label">CST/COFINS</span>
            <p class="data-value">
                @if($produto->cst_cofins == '1') 01 - Operação Tributável (Alíquota Básica)
                @elseif($produto->cst_cofins == '49') 49 - Outras Operações de Saída
                @elseif($produto->cst_cofins == '99') 99 - Outras Operações
                @else {{ $produto->cst_cofins }} @endif
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 data-item">
            <span class="data-label">ICMS (%)</span>
            <p class="data-value">{{ number_format($produto->icms, 2, ',', '.') }}</p>
        </div>
        <div class="col-md-3 data-item">
            <span class="data-label">PIS (%)</span>
            <p class="data-value">{{ number_format($produto->pis, 2, ',', '.') }}</p>
        </div>
        <div class="col-md-3 data-item">
            <span class="data-label">COFINS (%)</span>
            <p class="data-value">{{ number_format($produto->cofins, 2, ',', '.') }}</p>
        </div>
        <div class="col-md-3 data-item">
            <span class="data-label">IPI (%)</span>
            <p class="data-value">{{ number_format($produto->ipi, 2, ',', '.') }}</p>
        </div>
    </div>

    {{-- BLOCO: REFORMA TRIBUTÁRIA --}}
    <hr class="my-4" style="border-top: 2px dashed var(--border-color);">
    <div class="row">
        <div class="col-12 mb-3">
            <h5 style="color: var(--primary-color); font-weight: 600; text-transform: uppercase;">
                <i class="fas fa-file-invoice-dollar mr-2"></i>Reforma Tributária (IBS / CBS)
            </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 data-item">
            <span class="data-label">Classificação Tributária</span>
            <p class="data-value">{{ $produto->cClassTrib ?: 'Não Informado' }}</p>
        </div>
        <div class="col-md-6 data-item">
            <span class="data-label">CST IBS/CBS</span>
            <p class="data-value">{{ $produto->cst_ibs_cbs ?: 'Não Informado' }}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 data-item">
            <span class="data-label">Alíquota IBS (%)</span>
            <p class="data-value">{{ number_format($produto->pIBS ?? 0, 2, ',', '.') }}</p>
        </div>
        <div class="col-md-4 data-item">
            <span class="data-label">Alíquota CBS (%)</span>
            <p class="data-value">{{ number_format($produto->pCBS ?? 0, 2, ',', '.') }}</p>
        </div>
        <div class="col-md-4 data-item">
            <span class="data-label">Imposto Seletivo (%)</span>
            <p class="data-value">{{ number_format($produto->pIS_imposto ?? 0, 2, ',', '.') }}</p>
        </div>
    </div>
</div>

               {{-- ABA 3: INFORMAÇÕES DO VEÍCULO --}}
@if($produto->tpProd)
<div class="tab-pane fade" id="veic" role="tabpanel" aria-labelledby="veic-tab">
    <div class="row">
        <div class="col-md-4 data-item">
            <span class="data-label">Tipo de Veículo</span>
            <p class="data-value">
                @switch($produto->tpVeic)
                    @case('02') CICLOMOTOR @break
                    @case('03') MOTONETA @break
                    @case('04') MOTOCICLO @break
                    @case('05') TRICICLO @break
                    @case('06') AUTOMÓVEL @break
                    @case('07') MICROÔNIBUS @break
                    @case('08') ÔNIBUS @break
                    @case('10') REBOQUE @break
                    @case('11') SEMIREBOQUE @break
                    @case('13') CAMINHONETA @break
                    @case('14') CAMINHÃO @break
                    @case('17') C.TRATOR @break
                    @case('22') ESP/ÔNIBUS @break
                    @case('23') MISTO/CAM @break
                    @case('24') CARGA/CAM @break
                    @default {{ $produto->tpVeic }}
                @endswitch
            </p>
        </div>
        <div class="col-md-8 data-item">
            <span class="data-label">Chassi</span>
            <p class="data-value">{{ $produto->chassiVeic }}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 data-item">
            <span class="data-label">Renavam</span>
            <p class="data-value">{{ $produto->renavanVeic }}</p>
        </div>
        <div class="col-md-4 data-item">
            <span class="data-label">Ano Fabricação</span>
            <p class="data-value">{{ $produto->anoFabVeic }}</p>
        </div>
        <div class="col-md-4 data-item">
            <span class="data-label">Ano Modelo</span>
            <p class="data-value">{{ $produto->anoModVeic }}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 data-item">
            <span class="data-label">Peso Líquido (kg)</span>
            <p class="data-value">{{ number_format($produto->pesoLVeic, 2, ',', '.') }}</p>
        </div>
        <div class="col-md-4 data-item">
            <span class="data-label">Peso Bruto (kg)</span>
            <p class="data-value">{{ number_format($produto->pesoBVeic, 2, ',', '.') }}</p>
        </div>
        <div class="col-md-4 data-item">
            <span class="data-label">Distância Eixos (mm)</span>
            <p class="data-value">{{ $produto->distVeic }}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5 data-item">
            <span class="data-label">Combustível</span>
            <p class="data-value">
                @switch($produto->combVeic)
                    @case('01') ÁLCOOL @break
                    @case('02') GASOLINA @break
                    @case('03') DIESEL @break
                    @case('16') ÁLCOOL/GASOLINA @break
                    @case('17') GASOLINA/ÁLCOOL/GNV @break
                    @case('18') GASOLINA/ELÉTRICO @break
                    @default {{ $produto->combVeic }}
                @endswitch
            </p>
        </div>
        <div class="col-md-7 data-item">
            <span class="data-label">Nº do Motor</span>
            <p class="data-value">{{ $produto->nMotorVeic }}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 data-item">
            <span class="data-label">Potência (CV)</span>
            <p class="data-value">{{ number_format($produto->cvVeic, 2, ',', '.') }}</p>
        </div>
        <div class="col-md-4 data-item">
            <span class="data-label">Cilindradas (cm³)</span>
            <p class="data-value">{{ number_format($produto->cm3Veic, 2, ',', '.') }}</p>
        </div>
        <div class="col-md-4 data-item">
            <span class="data-label">Série</span>
            <p class="data-value">{{ $produto->serieVeic }}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 data-item">
            <span class="data-label">Tipo de Pintura</span>
            <p class="data-value">{{ $produto->tpPVeic }}</p>
        </div>
        <div class="col-md-4 data-item">
            <span class="data-label">Cor</span>
            <p class="data-value">{{ $produto->corVeic }}</p>
        </div>
        <div class="col-md-4 data-item">
            <span class="data-label">Código Cor (DENATRAN)</span>
            <p class="data-value">
                @switch($produto->cCorVeic)
                    @case('01') AMARELO @break
                    @case('02') AZUL @break
                    @case('03') BEGE @break
                    @case('04') BRANCA @break
                    @case('05') CINZA @break
                    @case('06') DOURADA @break
                    @case('07') GRENAR @break
                    @case('08') LARANJA @break
                    @case('09') MARROM @break
                    @case('10') PRATA @break
                    @case('11') PRETA @break
                    @case('12') ROSA @break
                    @case('13') ROXA @break
                    @case('14') VERDE @break
                    @case('15') VERMELHA @break
                    @case('16') FANTASIA @break
                    @default {{ $produto->cCorVeic }}
                @endswitch
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 data-item">
            <span class="data-label">Cód. Cor Montadora</span>
            <p class="data-value">{{ $produto->cCorMontVeic }}</p>
        </div>
        <div class="col-md-4 data-item">
            <span class="data-label">Código da Marca</span>
            <p class="data-value">{{ $produto->cMarcaVeic }}</p>
        </div>
        <div class="col-md-4 data-item">
            <span class="data-label">Condição do Veículo</span>
            <p class="data-value">
                @switch($produto->condVeic)
                    @case('1') ACABADO @break
                    @case('2') INACABADO @break
                    @case('3') SEMIACABO @break
                    @default {{ $produto->condVeic }}
                @endswitch
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 data-item">
            <span class="data-label">Espécie do Veículo</span>
            <p class="data-value">
                @switch($produto->espVeic)
                    @case('1') PASSAGEIRO @break
                    @case('2') CARGA @break
                    @case('3') MISTO @break
                    @case('4') CORRIDA @break
                    @case('5') TRAÇÃO @break
                    @case('6') ESPECIAL @break
                    @default {{ $produto->espVeic }}
                @endswitch
            </p>
        </div>
        <div class="col-md-4 data-item">
            <span class="data-label">Condição do Chassi (VIN)</span>
            <p class="data-value">
                @switch($produto->vinVeic)
                    @case('N') NORMAL @break
                    @case('R') REMARCADO @break
                    @default {{ $produto->vinVeic }}
                @endswitch
            </p>
        </div>
        <div class="col-md-4 data-item">
            <span class="data-label">Lotação Máxima</span>
            <p class="data-value">{{ $produto->lotVeic }}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5 data-item">
            <span class="data-label">Restrição</span>
            <p class="data-value">
                @switch($produto->restriVeic)
                    @case('0') NÃO HÁ @break
                    @case('1') ALIENAÇÃO FIDUNCIÁRIA @break
                    @case('2') ARRENDAMENTO MERCANTIL @break
                    @case('3') RESERVA DE DOMÍNIO @break
                    @case('4') PENHOR DE VEÍCULOS @break
                    @case('9') OUTRAS @break
                    @default {{ $produto->restriVeic }}
                @endswitch
            </p>
        </div>
        <div class="col-md-2 data-item">
            <span class="data-label">Carga Máxima (kg)</span>
            <p class="data-value">{{ $produto->cargaVeic }}</p>
        </div>
        <div class="col-md-5 data-item">
            <span class="data-label">Tipo de Operação</span>
            <p class="data-value">
                @switch($produto->operVeic)
                    @case('1') VENDA CONCESSIONÁRIA @break
                    @case('2') FATURAMENTO DIRETO PARA CONSUMIDOR FINAL @break
                    @case('3') VENDA DIRETO PARA GRANDES CONSUMIDORES @break
                    @case('0') OUTRAS @break
                    @default {{ $produto->operVeic }}
                @endswitch
            </p>
        </div>
    </div>
</div>
@endif
            </div>
        </div>
    </div>
@stop