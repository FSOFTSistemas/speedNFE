@extends('adminlte::page')

@section('title', 'Entradas de Notas Fiscais')

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
        --action-view: #17a2b8;
        --action-delete: #dc3545;
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
    .custom-btn-primary {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        color: #fff !important;
    }
    .custom-btn-primary:hover {
        background-color: #00045e !important;
        border-color: #00045e !important;
    }
    .custom-btn-success {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        color: #fff !important;
    }
    .custom-btn-success:hover {
        background-color: #218838 !important;
        border-color: #1e7e34 !important;
    }
    .custom-btn-secondary {
        background-color: var(--text-light) !important;
        border-color: var(--text-light) !important;
        color: #fff !important;
    }
    .custom-btn-secondary:hover {
        background-color: #5a6268 !important;
        border-color: #545b62 !important;
    }

    /* Otimização dos botões do cabeçalho para mobile */
    .header-buttons .btn {
        display: block;
        margin-bottom: 8px;
    }
     .header-buttons .btn:last-child {
        margin-bottom: 0;
    }
    @media (min-width: 992px) {
        .header-buttons .btn {
            display: inline-block;
            margin-bottom: 0;
            margin-left: 8px;
        }
        .header-buttons .btn:first-child {
            margin-left: 0;
        }
    }
    
    /* Estilos da Tabela */
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
        letter-spacing: 0.5px;
    }
    .table tbody tr:hover {
        background-color: #f1f1f1 !important;
    }
    .table td.fornecedor-name, .table th.fornecedor-header {
        text-align: left;
    }

    /* Ações */
    .action-buttons {
        white-space: nowrap;
    }
    .action-buttons .btn {
        margin: 0 4px;
    }
    
    /* Modal */
    .modal-content {
        border-radius: 15px;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Entradas de Notas Fiscais</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            <a class="btn custom-btn custom-btn-primary" data-toggle="modal" data-target="#modalImportarNFe"><i class="fas fa-upload mr-1"></i> Importar NFe</a>
            <a href="{{ route('entradas.manual') }}" class="btn custom-btn custom-btn-success"><i class="fas fa-plus mr-1"></i> Nova Entrada Manual</a>
            <a href="{{ route('produto.index') }}" class="btn custom-btn custom-btn-secondary">Voltar para Produtos</a>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-main">
        <div class="card-header bg-transparent border-0 pb-0">
            <h5 class="card-title mb-0">Filtrar por Período</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('entradas.index') }}" method="GET" class="form-inline">
                <div class="form-group mr-2 mb-2 flex-grow-1">
                    <label for="data_inicio" class="mr-2">De:</label>
                    <input type="date" name="data_inicio" id="data_inicio" class="form-control flex-grow-1" value="{{ request('data_inicio') }}">
                </div>
                <div class="form-group mr-2 mb-2 flex-grow-1">
                    <label for="data_fim" class="mr-2">Até:</label>
                    <input type="date" name="data_fim" id="data_fim" class="form-control flex-grow-1" value="{{ request('data_fim') }}">
                </div>
                <button type="submit" class="btn custom-btn custom-btn-primary mb-2">Filtrar</button>
                <a href="{{ route('entradas.index') }}" class="btn btn-outline-secondary mb-2 ml-2" title="Limpar os filtros aplicados" data-toggle="tooltip">Limpar</a>
            </form>
        </div>
    </div>

    <div class="card card-main mt-4">
        <div class="card-body p-0">
            @component('components.dataTable', [
                'responsive' => true,
                'searching' => false,
                'lengthChange' => false,
                'pageLength' => 10,
                'ordering' => true,
                'showFooter' => true,
                'sumColumnIndex' => 5,
            ])
                <thead class="table-light">
                    <tr>
                        <th>Emissão</th>
                        <th>Entrada</th>
                        <th>Nº</th>
                        <th class="fornecedor-header">Fornecedor</th>
                        <th>Chave</th>
                        <th>Valor (R$)</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($entradas as $etd)
                        <tr>
                            <td>{{ date('d/m/Y', strtotime($etd->dataEmissao)) }}</td>
                            <td>{{ date('d/m/Y', strtotime($etd->dataEntrada)) }}</td>
                            <td>{{ $etd->numeroNota }}</td>
                            <td class="fornecedor-name">{{ $etd->fornecedor }}</td>
                            <td>{{ $etd->chave }}</td>
                            <td>{{ $etd->valor }}</td>
                            <td class="action-buttons">
                                <a title="Visualizar" href="{{ route('itens-entradas.show', [$etd->id]) }}" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                                <form action="{{ route('entradas.destroy', $etd->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta entrada? Esta ação não pode ser desfeita.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @endcomponent
        </div>
    </div>

    @component('components.modal', [
        'modalId' => 'modalImportarNFe',
        'modalTitle' => 'Importar Nota Fiscal Eletrônica',
        'sizeModal' => 'modal-md',
    ])
        @component('components.custom-form', ['route' => 'importar_produtos'])
            <div class="row mt-3">
                <div class="col">
                    <div class="form-group">
                        <label for="nota">Chave da NFe ou Arquivo XML</label>
                        <input type="text" class="form-control" id="nota" name="nota" placeholder="Digite os 44 números da chave" minlength="44" maxlength="44" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                        <div class="invalid-feedback">A chave deve conter 44 números.</div>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="type" name="type" onchange="importXML(this)">
                        <label class="form-check-label" for="type">
                            Importar usando arquivo XML
                        </label>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn custom-btn custom-btn-success">
                    <i class="fas fa-check mr-1"></i> Importar Nota
                </button>
            </div>
        @endcomponent
    @endcomponent
@stop

@push('js')
<script>
    function importXML(input) {
        const notaInput = document.getElementById('nota');
        if (input.checked) {
            notaInput.type = 'file';
            notaInput.removeAttribute('minlength');
            notaInput.removeAttribute('maxlength');
            notaInput.removeAttribute('oninput');
            notaInput.placeholder = '';
            notaInput.accept = '.xml';
        } else {
            notaInput.type = 'text';
            notaInput.setAttribute('minlength', '44');
            notaInput.setAttribute('maxlength', '44');
            notaInput.setAttribute('oninput', "this.value = this.value.replace(/[^0-9]/g, '');");
            notaInput.placeholder = 'Digite os 44 números da chave';
            notaInput.removeAttribute('accept');
        }
    }

    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
