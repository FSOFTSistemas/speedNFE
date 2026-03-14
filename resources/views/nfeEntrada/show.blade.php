@extends('adminlte::page')

@section('title', 'Visualizar Itens da Entrada')

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
        background-color: var(--text-light) !important;
        border-color: var(--text-light) !important;
        color: #fff !important;
        font-weight: 500;
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .custom-btn-secondary:hover {
        background-color: #5a6268 !important;
        border-color: #545b62 !important;
        transform: translateY(-2px);
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
    .table td.product-name, .table th.product-header {
        text-align: left;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-md-10">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Itens da Nota Fiscal</h1>
            <small>Fornecedor: <strong>{{ $entrada->fornecedor }}</strong> | Nota Nº: <strong>{{ $entrada->numeroNota }}</strong></small>
        </div>
        <div class="col-md-2 text-md-right mt-2 mt-md-0">
            <a href="{{ route('entradas.index') }}" class="btn custom-btn-secondary btn-block">Voltar</a>
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
                        <th class="product-header">Produto</th>
                        <th>Quantidade</th>
                    </tr>
                </thead>

                 <tbody>
                    @foreach ($entrada->itensEntradas as $item)
                        <tr>
                            <td>{{ $item->produto->produto }}</td>
                            <td>{{ $item->qtde }}</td>
                        </tr>
                    @endforeach
                </tbody>
            @endcomponent
        </div>
    </div>
@stop

@section('js')
    <script>
        // Scripts específicos da página, se necessário
    </script>
@stop
