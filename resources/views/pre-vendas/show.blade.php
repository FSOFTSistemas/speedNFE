@extends('adminlte::page')

@section('title', 'Pré-venda #'.$preVenda->numero)

@push('css')
<style>
    .pre-sale-detail-card { border: 0; border-radius: 15px; box-shadow: 0 5px 20px rgba(0, 0, 0, .08); }
    .detail-label { color: #6c757d; font-size: .75rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
    .detail-value { font-size: 1rem; font-weight: 600; }
    .total-box { background: #f8f9fa; border-radius: 12px; padding: 1.25rem; }
    .total-box .line { display: flex; justify-content: space-between; margin-bottom: .5rem; }
    .total-box .grand-total { border-top: 1px solid #dee2e6; font-size: 1.25rem; font-weight: 700; margin-top: .75rem; padding-top: .75rem; }
</style>
@endpush

@php($status = $preVenda->statusEfetivo()->value)

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-7">
            <div class="text-uppercase text-muted small font-weight-bold">Comercial</div>
            <h1 class="m-0 text-dark font-weight-bold">Pré-venda #{{ $preVenda->numero }}</h1>
            <p class="mb-0 text-muted">Criada em {{ $preVenda->data->format('d/m/Y') }}</p>
        </div>
        <div class="col-lg-5 text-lg-right mt-3 mt-lg-0">
            <a href="{{ route('pre-vendas.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Voltar
            </a>
            <a target="_blank" href="{{ route('pre-vendas.pdf', $preVenda->id) }}" class="btn btn-outline-primary">
                <i class="fas fa-print mr-1"></i> Imprimir
            </a>
            @if ($status === 'ABERTA')
                <a href="{{ route('pre-vendas.edit', $preVenda->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit mr-1"></i> Editar
                </a>
            @endif
        </div>
    </div>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-warning">
            <strong>Não foi possível concluir a operação:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-8">
            <div class="card pre-sale-detail-card mb-4">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <div class="detail-label">Cliente</div>
                            <div class="detail-value">{{ $preVenda->cliente->nome ?? $preVenda->cliente_nome ?? 'Consumidor não identificado' }}</div>
                            @if ($preVenda->cliente?->cpf_cnpj ?? $preVenda->cliente_documento)
                                <small class="text-muted">{{ $preVenda->cliente?->cpf_cnpj ?? $preVenda->cliente_documento }}</small>
                            @endif
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">Validade</div>
                            <div class="detail-value">{{ $preVenda->validade_at?->format('d/m/Y') ?? 'Sem validade' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="detail-label">Status</div>
                            @if ($status === 'ABERTA')
                                <span class="badge badge-info p-2">Aberta</span>
                            @elseif ($status === 'CONVERTIDA')
                                <span class="badge badge-success p-2">Convertida em {{ $preVenda->destino?->value }}</span>
                            @elseif ($status === 'EXPIRADA')
                                <span class="badge badge-warning p-2">Expirada</span>
                            @else
                                <span class="badge badge-secondary p-2">Cancelada</span>
                            @endif
                        </div>
                    </div>
                    @if ($preVenda->observacoes)
                        <div class="rounded bg-light p-3"><strong>Observações:</strong> {{ $preVenda->observacoes }}</div>
                    @endif
                </div>
            </div>

            <div class="card pre-sale-detail-card mb-4">
                <div class="card-header bg-transparent"><h3 class="card-title font-weight-bold">Itens</h3></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr><th>Produto</th><th class="text-right">Qtd.</th><th class="text-right">Unitário</th><th class="text-right">Desc./Acrésc.</th><th class="text-right">Total</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($preVenda->itens as $item)
                                    <tr>
                                        <td>{{ $item->descricao }}<small class="d-block text-muted">{{ $item->codigo }}</small></td>
                                        <td class="text-right">{{ number_format($item->quantidade, 4, ',', '.') }}</td>
                                        <td class="text-right">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                                        <td class="text-right">
                                            <span class="text-danger">- R$ {{ number_format($item->desconto, 2, ',', '.') }}</span><br>
                                            <span class="text-success">+ R$ {{ number_format($item->acrescimo, 2, ',', '.') }}</span>
                                        </td>
                                        <td class="text-right font-weight-bold">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card pre-sale-detail-card mb-4">
                <div class="card-header bg-transparent"><h3 class="card-title font-weight-bold">Previsão de pagamento</h3></div>
                <div class="card-body">
                    @forelse ($preVenda->pagamentos as $pagamento)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ $pagamento->descricao }} @if($pagamento->vencimento)<small class="text-muted">· {{ $pagamento->vencimento->format('d/m/Y') }}</small>@endif</span>
                            <strong>R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</strong>
                        </div>
                    @empty
                        <p class="mb-0 text-muted">Nenhuma forma de pagamento prevista.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card pre-sale-detail-card mb-4">
                <div class="card-body">
                    <div class="total-box">
                        <div class="line"><span>Subtotal</span><strong>R$ {{ number_format($preVenda->subtotal, 2, ',', '.') }}</strong></div>
                        <div class="line"><span>Desconto geral</span><strong>R$ {{ number_format($preVenda->desconto, 2, ',', '.') }}</strong></div>
                        <div class="line"><span>Acréscimo geral</span><strong>R$ {{ number_format($preVenda->acrescimo, 2, ',', '.') }}</strong></div>
                        <div class="line grand-total"><span>Total</span><span>R$ {{ number_format($preVenda->total, 2, ',', '.') }}</span></div>
                    </div>
                </div>
            </div>

            @if ($status === 'ABERTA')
                <div class="card pre-sale-detail-card mb-4">
                    <div class="card-header bg-transparent"><h3 class="card-title font-weight-bold">Converter em venda</h3></div>
                    <div class="card-body">
                        <button class="btn btn-primary btn-block mb-2" data-toggle="modal" data-target="#convertNfeModal" @disabled(!$preVenda->cliente_id)>
                            <i class="far fa-file-alt mr-1"></i> Gerar rascunho de NF-e
                        </button>
                        @if (!$preVenda->cliente_id)
                            <small class="d-block text-warning mb-3">Selecione um cliente para gerar NF-e.</small>
                        @endif
                        <form method="POST" action="{{ route('pre-vendas.converter', $preVenda->id) }}">
                            @csrf
                            <input type="hidden" name="destino" value="NFCE">
                            <button class="btn btn-outline-primary btn-block" @disabled($preVenda->pagamentos->isEmpty())
                                onclick="return confirm('Converter esta pré-venda em uma venda NFC-e? O estoque será baixado.')">
                                <i class="fas fa-cash-register mr-1"></i> Gerar venda NFC-e
                            </button>
                        </form>
                        @if ($preVenda->pagamentos->isEmpty())
                            <small class="d-block text-warning mt-2">Informe o pagamento antes de gerar NFC-e.</small>
                        @endif
                    </div>
                </div>

                <div class="card pre-sale-detail-card border-danger">
                    <div class="card-body">
                        <form method="POST" action="{{ route('pre-vendas.cancelar', $preVenda->id) }}">
                            @csrf
                            <button class="btn btn-outline-danger btn-block" onclick="return confirm('Deseja cancelar esta pré-venda?')">
                                <i class="fas fa-ban mr-1"></i> Cancelar pré-venda
                            </button>
                        </form>
                    </div>
                </div>
            @elseif ($status === 'CONVERTIDA')
                <div class="card pre-sale-detail-card">
                    <div class="card-body">
                        <div class="detail-label">Documento gerado</div>
                        @if ($preVenda->pedido_id)
                            <a href="{{ route('vendas.editar', $preVenda->pedido_id) }}" class="btn btn-outline-primary btn-block mt-2">Abrir NF-e #{{ $preVenda->pedido_id }}</a>
                        @elseif ($preVenda->cupom_id)
                            <a href="{{ route('cupom.showPreView', $preVenda->cupom_id) }}" target="_blank" class="btn btn-outline-primary btn-block mt-2">Abrir venda NFC-e #{{ $preVenda->cupom_id }}</a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="convertNfeModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('pre-vendas.converter', $preVenda->id) }}">
                    @csrf
                    <input type="hidden" name="destino" value="NFE">
                    <div class="modal-header">
                        <h5 class="modal-title">Gerar rascunho de NF-e</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-7 form-group">
                                <label for="cfop">CFOP</label>
                                <select id="cfop" name="cfop" class="form-control" required>
                                    <option value="">Selecione</option>
                                    @foreach ($cfops as $cfop)
                                        <option value="{{ $cfop->id }}">{{ $cfop->cfop }} — {{ $cfop->natureza }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5 form-group">
                                <label for="finalidade">Finalidade</label>
                                <select id="finalidade" name="finalidade" class="form-control">
                                    <option value="1">NF-e normal</option>
                                    <option value="4">Devolução</option>
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="info_complementares">Informações complementares</label>
                                <textarea id="info_complementares" name="info_complementares" class="form-control" rows="3">{{ $preVenda->observacoes }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Fechar</button>
                        <button class="btn btn-primary">Gerar rascunho</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
