@extends('adminlte::page')

@section('title', 'XMLS de NFCe')

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
        --info-color: #17a2b8;
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
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .custom-btn-info { background-color: var(--info-color) !important; border-color: var(--info-color) !important; color: #fff !important; }
    .custom-btn-secondary { background-color: #6c757d !important; border-color: #6c757d !important; color: #fff !important; }
    .custom-btn-success { background-color: var(--success-color) !important; border-color: var(--success-color) !important; color: #fff !important; }

    .header-buttons .btn { display: block; margin-bottom: 8px; }
    @media (min-width: 992px) {
        .header-buttons .btn { display: inline-block; margin-bottom: 0; margin-left: 8px; }
    }
    
    .table thead th, .table tbody td {
        background-color: transparent !important;
        vertical-align: middle;
        text-align: center;
    }
    .table thead th {
        color: var(--text-dark) !important;
        font-weight: 600;
        border-bottom: 2px solid var(--border-color) !important;
        text-transform: uppercase;
    }
    .table tbody tr:hover { background-color: #f1f1f1 !important; }
    .table td.text-left { text-align: left; }

    .action-buttons {
        white-space: nowrap;
        text-align: right;
    }
    .action-buttons a {
        color: var(--text-light);
        margin: 0 8px;
        font-size: 1.2rem;
        transition: color 0.3s ease;
    }
    .action-buttons a:hover.text-success { color: var(--success-color) !important; }

    .modal-content { border-radius: 15px; border: none; }
    .modal-header { border-bottom: 1px solid var(--border-color); }
    .form-label { font-weight: 500; color: #495057; margin-bottom: .5rem; }
    .form-control { border-radius: 8px; border: 1px solid var(--border-color); height: 48px; }
    .form-control:focus { border-color: #80bdff; box-shadow: 0 0 0 0.2rem rgba(0, 3, 58, .25); }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">XMLs de NFCe</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            <a class="btn custom-btn custom-btn-info" href="{{ route('nfce.showUnuser') }}">
                <i class="fas fa-ban mr-1"></i> Inutilizar Faixa
            </a>
            <button class="btn custom-btn custom-btn-secondary" data-toggle="modal" data-target="#SendXmlModal">
                <i class="far fa-share-square mr-1"></i> Enviar para Contador
            </button>
        </div>
    </div>
@stop

@section('content')
<div class="card card-main">
    <div class="card-body p-0">
        @component('components.dataTable', [
            'responsive' => true,
            'searching' => true,
            'lengthChange' => true,
            'pageLength' => 10,
            'ordering' => true,
            'showFooter' => false,
        ])
            <thead class="table-light">
                <tr>
                    <th>Nº</th>
                    <th>Data</th>
                    <th class="text-left">Chave</th>
                    <th class="text-left">Cliente</th>
                    <th>Cupom</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($nfces as $nfce)
                    <tr>
                        <td>{{ $nfce->nro }}</td>
                        <td>{{ \Carbon\Carbon::parse($nfce->data)->format('d/m/Y') }}</td>
                        <td class="text-left">
                            <a class="text-decoration-none" title="Visualizar" target="_blank" href="{{ route('nfce.show', [$nfce->cupom->id]) }}">
                                {{ $nfce->chave }}
                            </a>
                        </td>
                        <td class="text-left">{{ $nfce->cupom->cliente->nome ?? 'CONSUMIDOR FINAL' }}</td>
                        <td>{{ $nfce->cupom->nroCupom }}</td>
                        <td class="action-buttons">
                            <a title="Download XML" href='{{ route('nfce.downloadXml', [$nfce->id]) }}' class='text-success'>
                                <i class="far fa-file-code"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endcomponent
    </div>
</div>

@component('components.modal', [
    'modalId' => 'SendXmlModal',
    'modalTitle' => 'Enviar XMLs para o Contador',
    'sizeModal' => 'modal-md',
])
    <form class="needs-validation" novalidate action="{{ route('nfce.sendXmlsToAccountant') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="form-group mb-3">
                <label for="month" class="form-label">Mês de Referência</label>
                <input class="form-control" type="month" id="month" name="month" required>
                <div class="invalid-feedback">Por favor, selecione o mês.</div>
            </div>
            <div class="form-group mb-3">
                <label for="accountant" class="form-label">E-mail do Contador</label>
                <input class="form-control" type="email" id="accountant" name="accountant" value="{{ $accountant }}" required>
                <div class="invalid-feedback">Por favor, informe um e-mail válido.</div>
            </div>
        </div>
        <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn custom-btn custom-btn-success">Confirmar Envio</button>
        </div>
    </form>
@endcomponent
@stop

@section('js')
    <script>
        // Script de validação padrão do Bootstrap
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
        })()
    </script>
@endsection