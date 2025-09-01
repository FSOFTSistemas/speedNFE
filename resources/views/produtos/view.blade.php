@extends('adminlte::page')

@section('title', 'Visualizar Produto')

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
            <h1 class="m-0 text-dark" style="font-weight: 600;">Visualizar Produto</h1>
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
                        aria-controls="home" aria-selected="true"><b>Informações do Produto</b></a>
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
                            <span class="data-label">Produto</span>
                            <p class="data-value">{{ $produto->produto }}</p>
                        </div>
                        <div class="col-md-6 data-item">
                            <span class="data-label">Categoria</span>
                            <p class="data-value">{{ $produto->descricao }}</p>
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
                        <div class="col-md-3 data-item">
                            <span class="data-label">Empresa</span>
                            <p class="data-value">{{ $produto->fantasia }}</p>
                        </div>
                        <div class="col-md-3 data-item">
                            <span class="data-label">É Veículo?</span>
                            <p class="data-value">{{ $produto->tpProd ? 'Sim' : 'Não' }}</p>
                        </div>
                    </div>
                </div>

                {{-- ABA 2: INFORMAÇÕES FISCAIS --}}
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
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
                        <div class="col-md-4 data-item">
                            <span class="data-label">CST</span>
                            <p class="data-value">{{ $produto->cst }}</p>
                        </div>
                        <div class="col-md-8 data-item">
                            <span class="data-label">CST/CSOSN</span>
                            <p class="data-value">{{ $produto->cst_csosn }}</p>
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-6 data-item">
                            <span class="data-label">CST/PIS</span>
                            <p class="data-value">{{ $produto->cst_pis }}</p>
                        </div>
                        <div class="col-md-6 data-item">
                            <span class="data-label">CST/COFINS</span>
                            <p class="data-value">{{ $produto->cst_cofins }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 data-item">
                            <span class="data-label">ICMS (%)</span>
                            <p class="data-value">{{ $produto->icms }}</p>
                        </div>
                        <div class="col-md-3 data-item">
                            <span class="data-label">PIS (%)</span>
                            <p class="data-value">{{ $produto->pis }}</p>
                        </div>
                        <div class="col-md-3 data-item">
                            <span class="data-label">COFINS (%)</span>
                            <p class="data-value">{{ $produto->cofins }}</p>
                        </div>
                        <div class="col-md-3 data-item">
                            <span class="data-label">IPI (%)</span>
                            <p class="data-value">{{ $produto->ipi }}</p>
                        </div>
                    </div>
                </div>

                {{-- ABA 3: INFORMAÇÕES DO VEÍCULO --}}
                @if($produto->tpProd)
                <div class="tab-pane fade" id="veic" role="tabpanel" aria-labelledby="veic-tab">
                    <div class="row">
                        <div class="col-md-4 data-item">
                            <span class="data-label">Tipo de Veículo</span>
                            <p class="data-value">{{ $produto->tpVeic }}</p>
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
                            <p class="data-value">{{ $produto->pesoLVeic }}</p>
                        </div>
                        <div class="col-md-4 data-item">
                            <span class="data-label">Peso Bruto (kg)</span>
                            <p class="data-value">{{ $produto->pesoBVeic }}</p>
                        </div>
                        <div class="col-md-4 data-item">
                            <span class="data-label">Distância Eixos (mm)</span>
                            <p class="data-value">{{ $produto->distVeic }}</p>
                        </div>
                    </div>
                    {{-- Adicione aqui os demais campos de veículo no mesmo formato --}}
                </div>
                @endif
            </div>
        </div>
    </div>
@stop
