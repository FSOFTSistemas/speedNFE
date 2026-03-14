@extends('adminlte::page')

@section('title', 'Visualizar Cliente')

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
            <h1 class="m-0 text-dark" style="font-weight: 600;">Visualizar Cliente</h1>
        </div>
        <div class="col-md-4 text-md-right mt-2 mt-md-0">
            <a href="{{ route('editar_cliente', ['id' => $cliente->id]) }}" class="btn custom-btn-edit mr-2">
                <i class="far fa-edit mr-1"></i> Editar
            </a>
            <a href="{{ route('cliente.index') }}" class="btn custom-btn-secondary">Voltar</a>
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
            <div class="tab-content mt-4" id="tabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row">
                        <div class="col-md-4 data-item">
                            <span class="data-label">Tipo</span>
                            <p class="data-value">{{ $cliente->tipo == '1' ? 'Pessoa Física' : 'Pessoa Jurídica' }}</p>
                        </div>
                        <div class="col-md-4 data-item">
                            <span class="data-label">CPF/CNPJ</span>
                            <p class="data-value">{{ $cliente->cpf_cnpj }}</p>
                        </div>
                        <div class="col-md-4 data-item">
                            <span class="data-label">RG / Inscrição Estadual</span>
                            <p class="data-value">{{ $cliente->rg_ie }}</p>
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-7 data-item">
                            <span class="data-label">Nome / Razão Social</span>
                            <p class="data-value">{{ $cliente->nome }}</p>
                        </div>
                        <div class="col-md-5 data-item">
                            <span class="data-label">Apelido / Nome Fantasia</span>
                            <p class="data-value">{{ $cliente->apelido }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 data-item">
                            <span class="data-label">Telefone</span>
                            <p class="data-value">{{ $cliente->telefone }}</p>
                        </div>
                        <div class="col-md-4 data-item">
                            <span class="data-label">Limite de Crédito</span>
                            <p class="data-value">R$ {{ number_format($cliente->limite, 2, ',', '.') }}</p>
                        </div>
                        <div class="col-md-4 data-item">
                            <span class="data-label">Empresa</span>
                            <p class="data-value">{{ $cliente->razao }}</p>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row">
                        <div class="col-md-3 data-item">
                            <span class="data-label">CEP</span>
                            <p class="data-value">{{ $cliente->cep }}</p>
                        </div>
                        <div class="col-md-9 data-item">
                            <span class="data-label">Logradouro</span>
                            <p class="data-value">{{ $cliente->rua }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 data-item">
                            <span class="data-label">Número</span>
                            <p class="data-value">{{ $cliente->numero }}</p>
                        </div>
                        <div class="col-md-5 data-item">
                            <span class="data-label">Bairro</span>
                            <p class="data-value">{{ $cliente->bairro }}</p>
                        </div>
                        <div class="col-md-4 data-item">
                            <span class="data-label">Cidade</span>
                            <p class="data-value">{{ $cliente->cidade }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 data-item">
                            <span class="data-label">Estado</span>
                            <p class="data-value">{{ $cliente->uf }}</p>
                        </div>
                         <div class="col-md-3 data-item">
                            <span class="data-label">Cód. IBGE</span>
                            <p class="data-value">{{ $cliente->codigoIBGE }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

