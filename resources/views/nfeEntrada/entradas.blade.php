@extends('adminlte::page')

@php
    $ehRamoMotos = optional(Auth::user()->empresa)->ramo_atividade === 'motos';
@endphp

@section('title', 'Entradas de Notas Fiscais')

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <div class="page-eyebrow">Entradas</div>
            <h1 class="m-0 text-dark" style="font-weight: 700;">Entradas de Notas Fiscais</h1>
            <div class="page-subtitle">Notas de compra importadas ou lançadas manualmente</div>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            <a class="btn custom-btn custom-btn-primary" data-toggle="modal" data-target="#modalImportarNFe"><i class="fas fa-upload mr-1"></i> Importar NFe</a>
            <a href="{{ route('entradas.manual') }}" class="btn custom-btn custom-btn-success"><i class="fas fa-plus mr-1"></i> Nova Entrada Manual</a>
            <a href="{{ route('produto.index') }}" class="btn custom-btn btn-outline-secondary">Voltar para Produtos</a>
        </div>
    </div>
@stop

@section('content')
    @php
        $totalEntradas = $entradas->count();
        $valorTotal = $entradas->sum('valor');
    @endphp

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon bg-primary-soft"><i class="fas fa-file-invoice"></i></div>
            <div>
                <div class="stat-value">{{ $totalEntradas }}</div>
                <div class="stat-label">Entradas no período</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-info-soft"><i class="fas fa-money-bill-wave"></i></div>
            <div>
                <div class="stat-value">R$ {{ number_format($valorTotal, 2, ',', '.') }}</div>
                <div class="stat-label">Valor total</div>
            </div>
        </div>
    </div>

    @php
        $filtrosAtivos = request()->hasAny(['data_inicio', 'data_fim', 'fornecedor', 'busca', 'chassi']);
    @endphp
    <div class="card card-main mb-4">
        <div class="filter-card-header" data-toggle="collapse" data-target="#filtrosEntradas"
            aria-expanded="{{ $filtrosAtivos ? 'true' : 'false' }}" aria-controls="filtrosEntradas">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter mr-2"></i>Filtros
                @if ($filtrosAtivos)
                    <span class="badge badge-info filter-active-badge ml-2">Ativos</span>
                @endif
            </h5>
            <i class="fas fa-chevron-down filter-toggle-icon"></i>
        </div>
        <div class="collapse {{ $filtrosAtivos ? 'show' : '' }}" id="filtrosEntradas">
        <div class="card-body">
            <form action="{{ route('entradas.index') }}" method="GET" class="row align-items-end">
                <div class="col-6 col-md-2 mb-2">
                    <label for="data_inicio" class="form-label">Data Início</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        <input type="date" class="form-control" id="data_inicio" name="data_inicio"
                            value="{{ request()->get('data_inicio', $filtros['data_inicio']) }}">
                    </div>
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label for="data_fim" class="form-label">Data Fim</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        <input type="date" class="form-control" id="data_fim" name="data_fim"
                            value="{{ request()->get('data_fim', $filtros['data_fim']) }}">
                    </div>
                </div>
                <div class="col-12 col-md-3 mb-2">
                    <label for="fornecedor" class="form-label">Fornecedor</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-truck"></i></span>
                        </div>
                        <input type="text" class="form-control" id="fornecedor" name="fornecedor"
                            placeholder="Nome do fornecedor" value="{{ request()->get('fornecedor') }}">
                    </div>
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label for="busca" class="form-label">Número ou Chave</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                        </div>
                        <input type="text" class="form-control" id="busca" name="busca"
                            placeholder="Nº ou chave" value="{{ request()->get('busca') }}">
                    </div>
                </div>
                @if ($ehRamoMotos)
                    <div class="col-6 col-md-2 mb-2">
                        <label for="chassi" class="form-label">Chassi</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-motorcycle"></i></span>
                            </div>
                            <input type="text" class="form-control" id="chassi" name="chassi"
                                placeholder="Chassi" value="{{ request()->get('chassi') }}">
                        </div>
                    </div>
                @endif
                <div class="col-12 col-md-1 mb-2">
                    <button type="submit" class="btn custom-btn custom-btn-primary w-100">Filtrar</button>
                </div>
            </form>
            @if ($filtrosAtivos)
                <div class="mt-2 text-right">
                    <a href="{{ route('entradas.index') }}" class="text-muted"><i class="fas fa-times-circle"></i> Limpar filtros</a>
                </div>
            @endif
        </div>
        </div>
    </div>

    <div class="card card-main">
        <div class="card-body p-0">
            @component('components.dataTable', [
                'responsive' => true,
                'searching' => false,
                'lengthChange' => false,
                'pageLength' => 1000,
                'ordering' => true,
                'showFooter' => true,
                'sumColumnIndex' => 5,
            ])
                <thead class="table-light">
                    <tr>
                        <th>Emissão</th>
                        <th>Entrada</th>
                        <th>Nº</th>
                        <th class="text-left">Fornecedor</th>
                        <th>Chave</th>
                        <th>Valor (R$)</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($entradas as $etd)
                        <tr>
                            <td>{{ optional(\App\Utils\FormatationUtil::parseData($etd->dataEmissao))->format('d/m/Y') }}</td>
                            <td>{{ optional(\App\Utils\FormatationUtil::parseData($etd->dataEntrada))->format('d/m/Y') }}</td>
                            <td>{{ $etd->numeroNota }}</td>
                            <td class="text-left">{{ $etd->fornecedor }}</td>
                            <td>{{ $etd->chave }}</td>
                            <td>{{ $etd->valor }}</td>
                            <td class="action-buttons">
                                <span class="d-none d-md-inline-flex">
                                    <a title="Visualizar" href="{{ route('itens-entradas.show', [$etd->id]) }}" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                                    @if ($etd->xml)
                                        <a title="Abrir PDF" href="{{ route('entradas.pdf', $etd->id) }}" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-file-pdf"></i></a>
                                    @endif
                                    <form action="{{ route('entradas.destroy', $etd->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta entrada? Esta ação não pode ser desfeita.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </span>
                                <div class="mobile-actions d-md-none">
                                    <a href="{{ route('itens-entradas.show', [$etd->id]) }}" class="btn btn-sm btn-outline-info"><i class="fa fa-eye"></i> Visualizar</a>
                                    @if ($etd->xml)
                                        <a href="{{ route('entradas.pdf', $etd->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fa fa-file-pdf"></i> Abrir PDF</a>
                                    @endif
                                    <form action="{{ route('entradas.destroy', $etd->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta entrada? Esta ação não pode ser desfeita.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100"><i class="fa fa-trash"></i> Excluir</button>
                                    </form>
                                </div>
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
