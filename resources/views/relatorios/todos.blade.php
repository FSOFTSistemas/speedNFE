@extends('adminlte::page')

@section('title', 'Relatórios')

@push('css')
{{-- Link original para o CSS do DataTables mantido --}}
<link href="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/cr-2.0.0/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.1/sc-2.4.1/datatables.min.css" rel="stylesheet">

<style>
    /* Estilos do Padrão Visual Definido */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    :root {
        --primary-color: #00033a;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --text-dark: #343a40;
    }

    body {
        font-family: 'Poppins', sans-serif;
    }
    
    .card-main {
        background: var(--card-bg);
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px var(--shadow-color);
        overflow: hidden; 
    }

    .card-header-filters {
        background-color: #f8f9fa;
        border-bottom: 1px solid var(--border-color);
        padding: 0;
    }
    .card-header-filters .btn-link {
        color: var(--text-dark);
        text-decoration: none;
        font-weight: 600;
        width: 100%;
        text-align: left;
        padding: 1rem 1.5rem;
    }
    .card-header-filters .btn-link:hover {
        background-color: #e9ecef;
    }
    
    .custom-btn-primary { 
        background-color: var(--primary-color) !important; 
        border-color: var(--primary-color) !important; 
        color: #fff !important; 
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .custom-btn-primary:hover:not(:disabled) { 
        transform: translateY(-2px);
    }
    
    /* Estilo para botão desabilitado */
    .custom-btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        filter: grayscale(1);
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

    /* Estilos da Tabela */
    .table thead th, .table tbody td {
        vertical-align: middle;
        text-align: center;
    }
    .table thead th {
        color: var(--text-dark) !important;
        font-weight: 600;
        border-bottom: 2px solid var(--border-color) !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table tbody tr:hover {
        background-color: #f1f1f1 !important;
    }
    .table td.text-left { text-align: left; }
    .dataTables_wrapper { padding: 20px; }

    .aviso-aux {
        font-size: 0.7rem;
        display: block;
        margin-top: 4px;
        color: #dc3545;
        font-weight: 500;
    }
</style>
@endpush

@section('content_header')
    <h1 class="m-0 text-dark" style="font-weight: 600;">Relatórios</h1>
@stop

@section('content')
<div class="card card-main">
    {{-- CABEÇALHO COM FILTROS COLAPSÁVEIS --}}
    <div class="card-header-filters" id="headingOne">
        <h2 class="mb-0">
            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFilters" aria-expanded="true" aria-controls="collapseFilters">
                <i class="fas fa-filter mr-2"></i> Filtros do Relatório
            </button>
        </h2>
    </div>

    <div id="collapseFilters" class="collapse show" aria-labelledby="headingOne">
        <div class="card-body border-bottom">
            <form id="relatorioForm" method="POST" target="_blank" action="{{ route('relatorio-pdf') }}">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label for="tipo_relatorio" class="form-label">Tipo de Relatório</label>
                        <select class="form-select" id="tipo_relatorio" name="tipo_relatorio">
                            <option value="nfe">Todos</option>
                            <option value="tipoR1">Vendas Sintéticas</option>
                            <option value="tipoR2">Vendas Analíticas</option>
                            <option value="tipoR3">Vendas de Produtos</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="estado" class="form-label">Status</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="%">Todos</option>
                            <option value="Aprovado">Aprovado</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="inicio" class="form-label">Data Início</label>
                        <input type="date" class="form-control input-data" id="inicio" name="inicio">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="fim" class="form-label">Data Fim</label>
                        <input type="date" class="form-control input-data" id="fim" name="fim">
                    </div>
                    <div class="col-md-1 mb-3">
                        <button type="submit" class="btn custom-btn-primary w-100" id="btnGerarPdf" disabled title="Preencha as datas para liberar">
                            PDF <i class="fas fa-file-pdf"></i>
                        </button>
                        <span id="msgErroData" class="aviso-aux">Defina as datas</span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- CORPO DO CARD COM A TABELA DE DADOS --}}
    <div class="card-body">
        <table class="table table-hover" id="produtos">
            <thead class="table-light">
                <tr>
                    <th>Nº Nota</th>
                    <th>Data</th>
                    <th class="text-left">Cliente</th>
                    <th>Situação</th>
                    <th>Valor Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pedidos as $venda)
                <tr>
                    <td>{{ $venda->numero_nfe }}</td>
                    <td>{{ \Carbon\Carbon::parse($venda->data)->format('d/m/Y') }}</td>
                    <td class="text-left">{{ $venda->cliente }}</td>
                    <td>
                        @if ($venda->estado == 'Aprovado' || $venda->estado == 'Autorizado') <span class="badge badge-success">{{ $venda->estado }}</span>
                        @elseif ($venda->estado == 'Cancelado') <span class="badge badge-danger">{{ $venda->estado }}</span>
                        @else <span class="badge badge-warning">{{ $venda->estado }}</span>
                        @endif
                    </td>
                    <td>R$ {{ number_format($venda->total, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')
{{-- Scripts originais do DataTables mantidos --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/cr-2.0.0/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.1/sc-2.4.1/datatables.min.js"></script>
<script>
    $(document).ready(function() {
        // DataTable original
        $('#produtos').DataTable({
            responsive: { details: true },
            language: { "url": 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json' },
            layout: {
                topStart: {
                    buttons: [
                        { extend: 'copyHtml5', text: '<i class="fa fa-clone text-secondary"></i>', titleAttr: 'Copiar' },
                        { extend: 'excelHtml5', text: '<i class="fa fa-file-excel text-success"></i>', titleAttr: 'Excel', title: 'Relatorio' },
                        { extend: 'pdfHtml5', text: '<i class="fa fa-file-pdf text-danger"></i>', titleAttr: 'PDF', title: 'Relatorio' }
                    ]
                }
            }
        });

        // Lógica de Trava do Botão
        const btnPdf = $('#btnGerarPdf');
        const inputsData = $('.input-data');
        const msgErro = $('#msgErroData');

        function validarCampos() {
            let todosPreenchidos = true;
            inputsData.each(function() {
                if ($(this).val() === "") {
                    todosPreenchidos = false;
                }
            });

            if (todosPreenchidos) {
                btnPdf.prop('disabled', false);
                msgErro.fadeOut();
            } else {
                btnPdf.prop('disabled', true);
                msgErro.fadeIn();
            }
        }

        // Monitora mudanças nos campos de data
        inputsData.on('change', validarCampos);
    });
</script>
@endsection