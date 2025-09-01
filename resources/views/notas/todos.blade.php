@extends('adminlte::page')

@section('title', 'Notas Fiscais')

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
        --action-view: #007bff;
        --action-delete: #dc3545;
        --action-send: #28a745;
        --info-color: #17a2b8;
        --warning-color: #ffc107;
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
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .custom-btn-info { background-color: var(--info-color) !important; border-color: var(--info-color) !important; color: #fff !important; }
    
    .header-buttons .btn, .header-buttons .form-control {
        margin-bottom: 8px;
    }
    @media (min-width: 992px) {
        .header-buttons .btn, .header-buttons .form-control {
            margin-bottom: 0;
            margin-left: 8px;
        }
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
        letter-spacing: 0.5px;
    }
    .table tbody tr:hover {
        background-color: #f1f1f1 !important;
    }
    .table td.text-left { text-align: left; }

    .action-buttons {
        white-space: nowrap;
        text-align: right; /* Alinha o conteúdo do form à direita */
    }
    .action-buttons form {
        display: inline-block; /* Mantém os forms na mesma linha se houver mais de um */
    }
    .action-buttons .btn-icon {
        background: none;
        border: none;
        color: var(--text-light);
        margin: 0 8px;
        font-size: 1.2rem;
        transition: color 0.3s ease;
        padding: 0;
    }
    .action-buttons .btn-icon:hover { color: var(--action-send) !important; }

    .form-control {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        height: 48px;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-4 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Notas Fiscais</h1>
        </div>
        <div class="col-lg-8 text-center text-lg-right header-buttons">
            <form action="{{ route('zip') }}" method="POST" class="d-inline-flex align-items-center">
                @csrf
                <input class="form-control" type="month" name="periodo" required min="2022-01" max="2030-12" value="{{ date_format(today(), 'Y-m') }}">
                <button class="btn custom-btn custom-btn-info w-100" type="submit">
                    <i class="fas fa-file-archive mr-1"></i> Download ZIP
                </button>
            </form>
            <a class="btn custom-btn custom-btn-info" href="{{ route('inutilizar.index') }}">
                <i class="fas fa-ban mr-1"></i> Inutilizar Notas
            </a>
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
                        <th>Número</th>
                        <th class="text-left">Chave</th>
                        <th>Valor</th>
                        <th>Estado</th>
                        @if ($empresa == 1)
                            <th class="text-left">Empresa</th>
                        @endif
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($notas as $nota)
                        <tr>
                            <td>#{{ $nota->numero_nfe }}</td>
                            <td class="text-left">
                                <a href="/venda/imprimir/{{ $nota->id }}" target="_blank">{{ $nota->chave }}</a>
                            </td>
                            <td>R$ {{ number_format($nota->total, 2, ',', '.') }}</td>
                            <td>
                                @if ($nota->estado == 'Autorizado') <span class="badge badge-success">{{ $nota->estado }}</span>
                                @elseif ($nota->estado == 'Cancelado') <span class="badge badge-danger">{{ $nota->estado }}</span>
                                @else <span class="badge badge-warning">{{ $nota->estado }}</span>
                                @endif
                            </td>
                            @if ($empresa == 1)
                                <td class="text-left">{{ $nota->fantasia }}</td>
                            @endif
                            <td class="action-buttons">
                                <form action="{{ route('baixarXml') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="empresa" value="{{ $nota->fantasia }}">
                                    <input type="hidden" name="chave" value="{{ $nota->chave }}">
                                    <input type="hidden" name="data" value="{{ $nota->data }}">
                                    <input type="hidden" name="estado" value="{{ $nota->estado }}">
                                    <button class="btn-icon" type="submit" title="Download XML">
                                        <i class="fa fa-file-code"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @endcomponent
        </div>
    </div>
@stop