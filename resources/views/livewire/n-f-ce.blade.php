<div class="pdv" id="pdv-app">
    <header class="pdv-header">
        <div><span class="pdv-eyebrow">FRENTE DE CAIXA</span><h1>Ponto de Venda <span class="pdv-badge">NFC-e</span></h1><p>Uma venda simples, do primeiro item ao pagamento.</p></div>
        <a class="pdv-btn pdv-btn-light" href="{{ route('cupom.index') }}"><i class="fas fa-receipt" aria-hidden="true"></i> Vendas realizadas</a>
    </header>
    <div class="pdv-notice" id="pdv-notice" role="alert" hidden wire:ignore></div>
    <form id="pdv-form" action="{{ route('cupom.store') }}" method="POST">
        @csrf
        <input type="hidden" name="acao_pos_salvar" id="acao_pos_salvar" value="depois" wire:ignore>
        <input type="hidden" name="cliente[id]" value="{{ $cliente['id'] ?? '' }}">
        <input type="hidden" name="valorTotal" value="{{ $valorTotal }}">
        <input type="hidden" name="subtotal" value="{{ $subtotal }}">
        <input type="hidden" name="descontoTotal" value="{{ $descontoTotalCalculado }}">
        <input type="hidden" name="acrescimoTotal" value="{{ $acrescimoTotalCalculado }}">
        <input type="hidden" name="troco" value="{{ max(0, $troco) }}">
        <input type="hidden" name="aReceber" value="{{ max(0, $aReceber) }}">
        @foreach ($formasSelecionadas as $forma => $valor)
            <input type="hidden" name="formas[{{ $forma }}]" value="{{ $valor }}">
        @endforeach
        <div class="pdv-workspace">
            <section class="pdv-main" aria-label="Itens da venda">
                <div class="pdv-card pdv-composer">
                    <div class="pdv-card-heading"><h2><span class="pdv-step">1</span> {{ $editProd ? 'Editar item' : 'Adicionar produto' }}</h2><span class="pdv-muted">Busca rápida <kbd>F2</kbd></span></div>
                    <fieldset @if (!empty($formasSelecionadas)) disabled @endif>
                        <label for="pdv-product">Produto ou código de barras</label>
                        <div class="pdv-search">
                            <i class="fas fa-search" aria-hidden="true"></i>
                            <input id="pdv-product" type="text" wire:model.defer="prod" wire:keydown.enter.prevent="searchProds" placeholder="Digite um nome ou leia o código e pressione Enter" @if ($prodId) readonly @endif autocomplete="off">
                            @if ($prodId)<button type="button" class="pdv-icon-btn" wire:click="cancelProd" aria-label="Limpar produto"><i class="fas fa-times" aria-hidden="true"></i></button>@endif
                            <button type="button" class="pdv-btn pdv-btn-light" wire:click="searchProds" wire:loading.attr="disabled">Buscar</button>
                        </div>
                        <div class="pdv-item-fields">
                            <div><label for="pdv-quantity">Quantidade</label><input id="pdv-quantity" type="number" min="1" step="1" wire:model.lazy="qtde" wire:change="updateProductTotal"></div>
                            <div><label for="pdv-unit">Valor unitário</label><input id="pdv-unit" type="text" value="R$ {{ number_format((float) $unitario, 2, ',', '.') }}" readonly></div>
                            <div><label for="pdv-discount">Desconto / un.</label><div class="pdv-input-action"><input id="pdv-discount" type="number" min="0" step="0.01" wire:model.lazy="desconto" wire:change="updateProductTotal"><button type="button" wire:click="toggleDescontoTipo" aria-label="Alternar desconto entre reais e percentual">{{ $descontoTipo === 'percent' ? '%' : 'R$' }}</button></div></div>
                            <div><label for="pdv-addition">Acréscimo / un.</label><div class="pdv-input-action"><input id="pdv-addition" type="number" min="0" step="0.01" wire:model.lazy="acrescimo" wire:change="updateProductTotal"><button type="button" wire:click="toggleAcrescimoTipo" aria-label="Alternar acréscimo entre reais e percentual">{{ $acrescimoTipo === 'percent' ? '%' : 'R$' }}</button></div></div>
                        </div>
                        <div class="pdv-composer-footer"><div><span class="pdv-muted">Total do item</span><strong>R$ {{ number_format((float) $total, 2, ',', '.') }}</strong></div><button type="button" class="pdv-btn pdv-btn-primary" wire:click="{{ $editProd ? 'updateProd' : 'addProd' }}" wire:loading.attr="disabled" @if (!$prodId || $qtde < 1) disabled @endif><i class="fas fa-{{ $editProd ? 'check' : 'plus' }}" aria-hidden="true"></i> {{ $editProd ? 'Salvar item' : 'Adicionar à venda' }}</button></div>
                    </fieldset>
                    @if (!empty($formasSelecionadas))<p class="pdv-hint">Para alterar os itens, limpe os pagamentos no resumo.</p>@endif
                </div>
                <div class="pdv-card pdv-cart">
                    <div class="pdv-card-heading"><h2>Itens da venda <span class="pdv-count">{{ count($itens) }}</span></h2><button type="button" class="pdv-text-btn pdv-danger" data-pdv-clear @if (empty($itens) || !empty($formasSelecionadas)) disabled @endif>Limpar <kbd>F4</kbd></button></div>
                    @forelse ($itens as $index => $item)
                        <article class="pdv-cart-item" wire:key="pdv-item-{{ $item['prodId'] }}">
                            <span class="pdv-item-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="pdv-item-description"><h3>{{ $item['produto'] }}</h3><p>Cód. {{ $item['codigo'] ?? '—' }} <span>·</span> {{ $item['qtde'] }} × R$ {{ number_format((float) $item['unitario'], 2, ',', '.') }}</p>@if ($item['desconto'] > 0 || $item['acrescimo'] > 0)<small>Desconto/un.: R$ {{ number_format((float) $item['desconto'], 2, ',', '.') }} · Acréscimo/un.: R$ {{ number_format((float) $item['acrescimo'], 2, ',', '.') }}</small>@endif</div>
                            <strong class="pdv-item-total">R$ {{ number_format((float) $item['total'], 2, ',', '.') }}</strong>
                            <div class="pdv-item-actions"><button type="button" class="pdv-icon-btn" wire:click="editItem({{ $index }})" @if (!empty($formasSelecionadas)) disabled @endif aria-label="Editar {{ $item['produto'] }}"><i class="fas fa-pen" aria-hidden="true"></i></button><button type="button" class="pdv-icon-btn pdv-danger" wire:click="removeItem({{ $index }})" @if (!empty($formasSelecionadas)) disabled @endif aria-label="Remover {{ $item['produto'] }}"><i class="far fa-trash-alt" aria-hidden="true"></i></button></div>
                            @foreach (['prodId', 'produto', 'qtde', 'unitario', 'desconto', 'acrescimo', 'total', 'subtotal'] as $campo)<input type="hidden" name="itens[{{ $index }}][{{ $campo }}]" value="{{ $item[$campo] }}">@endforeach
                        </article>
                    @empty
                        <div class="pdv-empty"><div class="pdv-empty-icon"><i class="fas fa-shopping-basket" aria-hidden="true"></i></div><h3>Sua próxima venda começa aqui</h3><p>Busque um produto pelo nome ou código.<br>Os itens adicionados aparecerão nesta lista.</p><button type="button" class="pdv-btn pdv-btn-light" wire:click="searchProducts">Buscar produtos <kbd>F2</kbd></button></div>
                    @endforelse
                </div>
                <div class="pdv-shortcuts"><span><kbd>F1</kbd> Cliente</span><span><kbd>F2</kbd> Produtos</span><span><kbd>F4</kbd> Limpar itens</span><span><kbd>F5</kbd> Pagamento</span></div>
            </section>
            <aside class="pdv-summary" aria-label="Resumo e pagamento">
                <div class="pdv-card">
                    <div class="pdv-card-heading"><h2><span class="pdv-step">2</span> Cliente</h2><button type="button" class="pdv-text-btn" wire:click="searchCustomers">Alterar <kbd>F1</kbd></button></div>
                    <div class="pdv-customer"><div class="pdv-avatar"><i class="far fa-user" aria-hidden="true"></i></div><div><strong>{{ $cliente['nome'] ?? 'Consumidor Final' }}</strong><span>{{ !empty($cliente['id']) ? 'Cliente identificado' : 'Venda sem identificação' }}</span></div>@if (!empty($cliente['id']))<button type="button" class="pdv-icon-btn" wire:click="removeClient" aria-label="Remover cliente"><i class="fas fa-times" aria-hidden="true"></i></button>@endif</div>
                    <div class="pdv-divider"></div>
                    <div class="pdv-card-heading"><h2><span class="pdv-step">3</span> Resumo da venda</h2></div>
                    <div class="pdv-total-row"><span>Subtotal dos itens</span><strong>R$ {{ number_format((float) $subtotal, 2, ',', '.') }}</strong></div>
                    <fieldset class="pdv-sale-adjustments" @if (!empty($formasSelecionadas)) disabled @endif>
                        <div><label for="pdv-sale-discount">Desconto</label><div class="pdv-input-action"><input id="pdv-sale-discount" type="number" min="0" step="0.01" wire:model.lazy="descontoTotal" wire:change="updateSaleTotal"><button type="button" wire:click="toggleDescontoTotalTipo" aria-label="Alternar desconto da venda">{{ $descontoTotalTipo === 'percent' ? '%' : 'R$' }}</button></div></div>
                        <div><label for="pdv-sale-addition">Acréscimo</label><div class="pdv-input-action"><input id="pdv-sale-addition" type="number" min="0" step="0.01" wire:model.lazy="acrescimoTotal" wire:change="updateSaleTotal"><button type="button" wire:click="toggleAcrescimoTotalTipo" aria-label="Alternar acréscimo da venda">{{ $acrescimoTotalTipo === 'percent' ? '%' : 'R$' }}</button></div></div>
                    </fieldset>
                    @if ($descontoTotalCalculado > 0)<div class="pdv-total-row pdv-green"><span>Desconto aplicado</span><span>− R$ {{ number_format((float) $descontoTotalCalculado, 2, ',', '.') }}</span></div>@endif
                    @if ($acrescimoTotalCalculado > 0)<div class="pdv-total-row"><span>Acréscimo aplicado</span><span>+ R$ {{ number_format((float) $acrescimoTotalCalculado, 2, ',', '.') }}</span></div>@endif
                    <div class="pdv-grand-total"><span>Total a pagar</span><strong>R$ {{ number_format((float) $valorTotal, 2, ',', '.') }}</strong><small>{{ count($itens) }} {{ count($itens) === 1 ? 'item na venda' : 'itens na venda' }}</small></div>
                    <button id="pdv-start-payment" type="button" class="pdv-btn pdv-btn-primary pdv-wide" wire:click="showPaymentArea" @if (empty($itens) || $editProd) disabled @endif wire:loading.attr="disabled">Ir para pagamento <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
                </div>
                @if ($showPaymentArea === 'block')
                    <div class="pdv-card pdv-payment" id="addPaymentMethod" tabindex="-1">
                        <div class="pdv-card-heading"><h2>Pagamento</h2><button type="button" class="pdv-text-btn" wire:click="clearMethods" @if (empty($formasSelecionadas)) disabled @endif>Limpar</button></div>
                        <p class="pdv-muted">Combine formas de pagamento, se precisar.</p>
                        <div class="pdv-payment-options">
                            @foreach ($formas as $forma)
                                <button type="button" class="pdv-payment-option {{ isset($formasSelecionadas[$forma]) ? 'is-selected' : '' }}" wire:click="addPaymentMethod(@js($forma))" @if ($valorPago >= $valorTotal && !empty($formasSelecionadas)) disabled @endif><i class="fas {{ $forma === 'DINHEIRO' ? 'fa-money-bill-wave' : ($forma === 'PIX' ? 'fa-qrcode' : 'fa-credit-card') }}" aria-hidden="true"></i><span>{{ ['DINHEIRO' => 'Dinheiro', 'PIX' => 'Pix', 'CARTÃO/CRÉDITO' => 'Crédito', 'CARTÃO/DÉBITO' => 'Débito'][$forma] ?? $forma }}</span>@if (isset($formasSelecionadas[$forma]))<small>R$ {{ number_format((float) $formasSelecionadas[$forma], 2, ',', '.') }}</small>@endif</button>
                            @endforeach
                        </div>
                        <div class="pdv-total-row"><span>Valor recebido</span><strong>R$ {{ number_format((float) $valorPago, 2, ',', '.') }}</strong></div>
                        <div class="pdv-total-row {{ $aReceber > 0 ? '' : 'pdv-green' }}"><span>{{ $troco > 0 ? 'Troco' : 'Falta receber' }}</span><strong>R$ {{ number_format((float) ($troco > 0 ? $troco : max(0, $aReceber)), 2, ',', '.') }}</strong></div>
                        <button type="button" id="pdv-finish" class="pdv-btn pdv-btn-success pdv-wide" data-pdv-finish wire:loading.attr="disabled" @if (empty($itens) || empty($formasSelecionadas) || $valorPago < $valorTotal || $editProd) disabled @endif><i class="fas fa-check" aria-hidden="true"></i> Finalizar venda</button>
                    </div>
                @endif
            </aside>
        </div>
    </form>
    <div class="pdv-loading" wire:loading.delay role="status"><i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i> Atualizando venda…</div>

    <dialog id="AddProdModal" class="pdv-dialog" wire:ignore.self aria-labelledby="pdv-products-title">
        <div class="pdv-dialog-heading"><div><span class="pdv-eyebrow">CATÁLOGO</span><h2 id="pdv-products-title">Escolha um produto</h2></div><button type="button" class="pdv-icon-btn" data-pdv-close aria-label="Fechar"><i class="fas fa-times" aria-hidden="true"></i></button></div>
        <label for="pdv-product-filter">Filtrar resultados</label><input id="pdv-product-filter" type="search" placeholder="Nome ou código" data-pdv-filter="pdv-product-results" autocomplete="off">
        <div id="pdv-product-results" class="pdv-picker" wire:ignore></div>
    </dialog>
    <dialog id="SearchClientModal" class="pdv-dialog" wire:ignore.self aria-labelledby="pdv-customers-title">
        <div class="pdv-dialog-heading"><div><span class="pdv-eyebrow">CLIENTES</span><h2 id="pdv-customers-title">Identificar cliente</h2></div><button type="button" class="pdv-icon-btn" data-pdv-close aria-label="Fechar"><i class="fas fa-times" aria-hidden="true"></i></button></div>
        <label for="pdv-customer-filter">Buscar cliente</label><input id="pdv-customer-filter" type="search" placeholder="Nome, código ou CPF/CNPJ" data-pdv-filter="pdv-customer-results" autocomplete="off">
        <div id="pdv-customer-results" class="pdv-picker">
            @forelse ($customers as $client)
                <button type="button" class="pdv-picker-item" wire:key="pdv-client-{{ $client['id'] }}" wire:click="selectClient({{ $client['id'] }}, @js($client['nome']))"><span><strong>{{ $client['nome'] }}</strong><small>{{ $client['codigo'] ?? '—' }} · {{ $client['cpf_cnpj'] ?? '—' }}</small></span><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
            @empty<p class="pdv-muted">Nenhum cliente cadastrado. Você pode vender para Consumidor Final.</p>@endforelse
        </div><p class="pdv-filter-empty" hidden>Nenhum cliente encontrado.</p>
    </dialog>
    <dialog id="PaymentModal" class="pdv-dialog pdv-dialog-small" wire:ignore.self aria-labelledby="pdv-payment-title">
        <div class="pdv-dialog-heading"><h2 id="pdv-payment-title">{{ $selectedForma ?: 'Receber pagamento' }}</h2><button type="button" class="pdv-icon-btn" data-pdv-close aria-label="Fechar"><i class="fas fa-times" aria-hidden="true"></i></button></div>
        <p class="pdv-muted">Falta receber R$ {{ number_format((float) max(0, $aReceber), 2, ',', '.') }}</p>
        <label for="pdv-received">Valor recebido (R$)</label><input id="pdv-received" type="number" min="0.01" step="0.01" wire:model.defer="valorRecebimento" wire:keydown.enter.prevent="updateValueReceived" inputmode="decimal">
        <button type="button" class="pdv-btn pdv-btn-primary pdv-wide" wire:click="updateValueReceived" wire:loading.attr="disabled">Confirmar pagamento</button>
    </dialog>
    <dialog id="pdv-confirm" class="pdv-dialog pdv-dialog-small" wire:ignore aria-labelledby="pdv-confirm-title">
        <div class="pdv-dialog-heading"><h2 id="pdv-confirm-title">Finalizar venda</h2><button type="button" class="pdv-icon-btn" data-pdv-close aria-label="Voltar à venda"><i class="fas fa-times" aria-hidden="true"></i></button></div>
        <p>Como deseja concluir esta venda?</p><button type="button" class="pdv-btn pdv-btn-success pdv-wide" data-pdv-submit="agora"><i class="fas fa-receipt" aria-hidden="true"></i> Salvar e emitir NFC-e agora</button><button type="button" class="pdv-btn pdv-btn-light pdv-wide" data-pdv-submit="depois">Salvar e emitir depois</button><p class="pdv-hint">A emissão usará o ambiente configurado na empresa.</p>
    </dialog>
</div>
