@extends('adminlte::page')

@section('title', 'Pré-vendas')

@push('css')
<style>
    .pre-sale-card { border: 0; border-radius: 15px; box-shadow: 0 5px 20px rgba(0, 0, 0, .08); }
    .pre-sale-card .card-body { padding: 1.5rem; }
    .pre-sale-table td, .pre-sale-table th { vertical-align: middle; }
    .pre-sale-actions { display: flex; flex-wrap: wrap; gap: .35rem; justify-content: flex-end; }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-md-7">
            <div class="text-uppercase text-muted small font-weight-bold">Comercial</div>
            <h1 class="m-0 text-dark font-weight-bold">Pré-vendas</h1>
            <p class="mb-0 text-muted">Registre propostas e converta-as em NF-e ou NFC-e.</p>
        </div>
        <div class="col-md-5 text-md-right mt-3 mt-md-0">
            <a href="{{ route('pre-vendas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Nova pré-venda
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card pre-sale-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pre-vendas.index') }}" class="row align-items-end">
                <div class="col-lg-4 col-md-6 mb-3">
                    <label for="search">Busca</label>
                    <input id="search" name="search" class="form-control" value="{{ request('search') }}"
                        placeholder="Número, cliente, documento ou produto">
                </div>
                <div class="col-lg-2 col-md-6 mb-3">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">Todos</option>
                        @foreach ($statusOptions as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                                {{ ucfirst(strtolower($status->value)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-6 mb-3">
                    <label for="data_inicio">Data inicial</label>
                    <input id="data_inicio" type="date" name="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                </div>
                <div class="col-lg-2 col-6 mb-3">
                    <label for="data_fim">Data final</label>
                    <input id="data_fim" type="date" name="data_fim" class="form-control" value="{{ request('data_fim') }}">
                </div>
                <div class="col-lg-2 mb-3 d-flex">
                    <button class="btn btn-primary flex-fill"><i class="fas fa-filter mr-1"></i> Filtrar</button>
                    <a href="{{ route('pre-vendas.index') }}" class="btn btn-outline-secondary ml-2" title="Limpar filtros">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card pre-sale-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover pre-sale-table mb-0">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Validade</th>
                            <th class="text-right">Total</th>
                            <th>Status</th>
                            <th class="text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($preVendas as $preVenda)
                            @php($status = $preVenda->statusEfetivo()->value)
                            <tr>
                                <td><strong>#{{ $preVenda->numero }}</strong></td>
                                <td>
                                    {{ $preVenda->cliente->nome ?? $preVenda->cliente_nome ?? 'Consumidor não identificado' }}
                                    @if ((int) Auth::user()->empresa_id === 1)
                                        <small class="d-block text-muted">{{ $preVenda->empresa->fantasia }}</small>
                                    @endif
                                </td>
                                <td>{{ $preVenda->data->format('d/m/Y') }}</td>
                                <td>{{ $preVenda->validade_at?->format('d/m/Y') ?? 'Sem validade' }}</td>
                                <td class="text-right">R$ {{ number_format($preVenda->total, 2, ',', '.') }}</td>
                                <td>
                                    @if ($status === 'ABERTA')
                                        <span class="badge badge-info">Aberta</span>
                                    @elseif ($status === 'CONVERTIDA')
                                        <span class="badge badge-success">Convertida</span>
                                    @elseif ($status === 'EXPIRADA')
                                        <span class="badge badge-warning">Expirada</span>
                                    @else
                                        <span class="badge badge-secondary">Cancelada</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="pre-sale-actions">
                                        <a href="{{ route('pre-vendas.show', $preVenda->id) }}" class="btn btn-sm btn-outline-primary" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a target="_blank" href="{{ route('pre-vendas.pdf', $preVenda->id) }}" class="btn btn-sm btn-outline-secondary" title="Imprimir">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @if ($status === 'ABERTA')
                                            <a href="{{ route('pre-vendas.edit', $preVenda->id) }}" class="btn btn-sm btn-outline-info" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-5 text-center text-muted">
                                    <i class="fas fa-clipboard-list fa-2x mb-3 d-block"></i>
                                    Nenhuma pré-venda encontrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $preVendas->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@stop
