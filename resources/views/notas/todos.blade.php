@extends('adminlte::page')

@php
    $ehRamoMotos = optional(Auth::user()->empresa)->ramo_atividade === 'motos';
@endphp

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
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .custom-btn-info {
            background-color: var(--info-color) !important;
            border-color: var(--info-color) !important;
            color: #fff !important;
        }

        .header-buttons .btn,
        .header-buttons .form-control {
            margin-bottom: 8px;
        }

        @media (min-width: 992px) {

            .header-buttons .btn,
            .header-buttons .form-control {
                margin-bottom: 0;
                margin-left: 8px;
            }
        }

        .table thead th,
        .table tbody td {
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

        .table td.text-left {
            text-align: left;
        }

        .action-buttons {
            white-space: nowrap;
            text-align: right;
            /* Alinha o conteúdo do form à direita */
        }

        .action-buttons form {
            display: inline-block;
            /* Mantém os forms na mesma linha se houver mais de um */
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

        .action-buttons .btn-icon:hover {
            color: var(--action-send) !important;
        }

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
                <input class="form-control" type="month" name="periodo" required min="2022-01" max="2030-12"
                    value="{{ date_format(today(), 'Y-m') }}">
                <button class="btn custom-btn custom-btn-info w-100" type="submit">
                    <i class="fas fa-file-archive mr-1"></i> Download ZIP
                </button>
            </form>
            <button class="btn custom-btn custom-btn-inf" style="background-color: #6c757d; color: white;"
                data-toggle="modal" data-target="#SendXmlModal">
                <i class="far fa-share-square mr-1"></i> Enviar para Contador
            </button>
            <a class="btn custom-btn custom-btn-info" href="{{ route('inutilizar.index') }}">
                <i class="fas fa-ban mr-1"></i> Inutilizar Notas
            </a>
        </div>
    </div>
@stop

@section('content')
    @php
        $filtrosAtivos = request()->hasAny(['data_inicio', 'data_fim', 'cliente', 'chassi', 'estado']);
    @endphp
    <div class="card card-main mb-4">
        <div class="filter-card-header" data-toggle="collapse" data-target="#filtrosNotas"
            aria-expanded="{{ $filtrosAtivos ? 'true' : 'false' }}" aria-controls="filtrosNotas">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter mr-2"></i>Filtros
                @if ($filtrosAtivos)
                    <span class="badge badge-info filter-active-badge ml-2">Ativos</span>
                @endif
            </h5>
            <i class="fas fa-chevron-down filter-toggle-icon"></i>
        </div>
        <div class="collapse {{ $filtrosAtivos ? 'show' : '' }}" id="filtrosNotas">
        <div class="card-body">
            <form action="{{ route('notas.index') }}" method="GET" class="row align-items-end">
                <div class="col-6 col-md-2 mb-2">
                    <label for="data_inicio" class="form-label">Data Início</label>
                    <input type="date" class="form-control" id="data_inicio" name="data_inicio"
                        value="{{ request()->get('data_inicio', $filtros['data_inicio']) }}">
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label for="data_fim" class="form-label">Data Fim</label>
                    <input type="date" class="form-control" id="data_fim" name="data_fim"
                        value="{{ request()->get('data_fim', $filtros['data_fim']) }}">
                </div>
                <div class="col-12 col-md-3 mb-2">
                    <label for="cliente" class="form-label">Cliente</label>
                    <input type="text" class="form-control" id="cliente" name="cliente"
                        placeholder="Nome ou CPF/CNPJ" value="{{ request()->get('cliente') }}">
                </div>
                @if ($ehRamoMotos)
                    <div class="col-6 col-md-2 mb-2">
                        <label for="chassi" class="form-label">Chassi</label>
                        <input type="text" class="form-control" id="chassi" name="chassi"
                            placeholder="Chassi" value="{{ request()->get('chassi') }}">
                    </div>
                @endif
                <div class="col-6 col-md-2 mb-2">
                    <label for="estado" class="form-label">Situação</label>
                    <select class="form-control" id="estado" name="estado">
                        <option value="">Todas</option>
                        @foreach (\App\Enums\EstadoEnum::cases() as $estadoOption)
                            <option value="{{ $estadoOption->value }}" @selected(request('estado') == $estadoOption->value)>
                                {{ $estadoOption->value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-1 mb-2">
                    <button type="submit" class="btn custom-btn custom-btn-info w-100">Filtrar</button>
                </div>
            </form>
            @if ($filtrosAtivos)
                <div class="mt-2 text-right">
                    <a href="{{ route('notas.index') }}" class="text-muted"><i class="fas fa-times-circle"></i> Limpar filtros</a>
                </div>
            @endif
        </div>
        </div>
    </div>

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
                        <th>Data</th>
                        <th class="text-left">Cliente</th>
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
                            <td>{{ date('d/m/Y', strtotime($nota->data)) }}</td>
                            <td class="text-left">{{ $nota->cliente_nome ?? '-' }}</td>
                            <td class="text-left">
                                <a href="/venda/imprimir/{{ $nota->id }}" target="_blank">{{ $nota->chave }}</a>
                            </td>
                            <td>R$ {{ number_format($nota->total, 2, ',', '.') }}</td>
                            <td>
                                @if ($nota->estado->value == 'Autorizado')
                                    <span class="badge badge-success">{{ $nota->estado }}</span>
                                @elseif ($nota->estado->value == 'Cancelado')
                                    <span class="badge badge-danger">{{ $nota->estado }}</span>
                                @else
                                    <span class="badge badge-warning">{{ $nota->estado }}</span>
                                @endif
                            </td>
                            @if ($empresa == 1)
                                <td class="text-left">{{ $nota->fantasia }}</td>
                            @endif
                            <td class="action-buttons">
                                <form action="{{ route('baixarXml') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="pedido_id" value="{{ $nota->id }}">
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
    @component('components.modal', [
    'modalId' => 'SendXmlModal',
    'modalTitle' => 'Enviar XMLs de NFe para o Contador',
    'sizeModal' => 'modal-md',
])
    <form class="needs-validation" novalidate action="{{ route('nfe.enviarContador') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="form-group mb-3">
                <label for="month" class="form-label">Mês de Referência</label>
                <input class="form-control" type="month" id="month" name="month" required value="{{ date('Y-m') }}">
                <div class="invalid-feedback">Por favor, selecione o mês.</div>
            </div>
            <div class="form-group mb-3">
                <label for="accountant" class="form-label">E-mail do Contador</label>
                <input class="form-control" type="email" id="accountant" name="accountant" value="{{ Auth::user()->empresa->contador ?? '' }}" required>
                <div class="invalid-feedback">Por favor, informe um e-mail válido.</div>
            </div>
        </div>
        <div class="modal-footer justify-content-center">
            <button type="submit" class="btn custom-btn btn-success" style="background-color: #28a745; color: white;">Confirmar Envio</button>
        </div>
    </form>
@endcomponent
@stop
