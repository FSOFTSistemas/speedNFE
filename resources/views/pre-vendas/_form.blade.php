@php
    $preVendaAtual = $preVenda ?? null;
    $itensIniciais = old('itens', $preVendaAtual
        ? $preVendaAtual->itens->map(fn ($item) => [
            'produto_id' => $item->produto_id,
            'quantidade' => (float) $item->quantidade,
            'valor_unitario' => (float) $item->valor_unitario,
            'desconto' => (float) $item->desconto,
            'acrescimo' => (float) $item->acrescimo,
        ])->values()->all()
        : []);
    $pagamentosIniciais = old('pagamentos', $preVendaAtual
        ? $preVendaAtual->pagamentos->map(fn ($pagamento) => [
            'forma_pag_id' => $pagamento->forma_pag_id,
            'descricao' => $pagamento->descricao,
            'valor' => (float) $pagamento->valor,
            'vencimento' => $pagamento->vencimento?->format('Y-m-d'),
        ])->values()->all()
        : []);
    $empresaSelecionada = old('empresa_id', $preVendaAtual->empresa_id ?? Auth::user()->empresa_id);
@endphp

@push('css')
<style>
    .pre-sale-form-card { border: 0; border-radius: 15px; box-shadow: 0 5px 20px rgba(0, 0, 0, .08); }
    .pre-sale-form-card .card-header { background: transparent; border-bottom: 1px solid #eef0f2; padding: 1.25rem 1.5rem; }
    .pre-sale-form-card .card-body { padding: 1.5rem; }
    .item-row, .payment-row { border: 1px solid #e5e7eb; border-radius: 10px; margin-bottom: .75rem; padding: 1rem; }
    .summary-line { display: flex; justify-content: space-between; margin-bottom: .55rem; }
    .summary-total { border-top: 1px solid #dee2e6; font-size: 1.2rem; font-weight: 700; padding-top: .8rem; }
</style>
@endpush

@if ($errors->any())
    <div class="alert alert-warning">
        <strong>Revise os dados da pré-venda:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-xl-8">
        <div class="card pre-sale-form-card mb-4">
            <div class="card-header">
                <h3 class="card-title font-weight-bold"><i class="fas fa-user mr-2 text-primary"></i>Dados comerciais</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @if ($isMaster)
                        <div class="col-md-6 form-group">
                            <label for="empresa_id">Empresa</label>
                            <select id="empresa_id" name="empresa_id" class="form-control" required>
                                @foreach ($empresas as $empresa)
                                    <option value="{{ $empresa->id }}" @selected((int) $empresaSelecionada === (int) $empresa->id)>
                                        {{ $empresa->fantasia ?: $empresa->razao }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" id="empresa_id" name="empresa_id" value="{{ Auth::user()->empresa_id }}">
                    @endif

                    <div class="col-md-{{ $isMaster ? '6' : '12' }} form-group">
                        <label for="cliente_id">Cliente</label>
                        <select id="cliente_id" name="cliente_id" class="form-control">
                            <option value="">Consumidor não identificado</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" data-company="{{ $cliente->empresa_id }}"
                                    @selected((string) old('cliente_id', $preVendaAtual->cliente_id ?? '') === (string) $cliente->id)>
                                    {{ $cliente->nome }}{{ $cliente->cpf_cnpj ? ' — '.$cliente->cpf_cnpj : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="data">Data</label>
                        <input id="data" type="date" name="data" class="form-control" required
                            value="{{ old('data', $preVendaAtual?->data?->format('Y-m-d') ?? today()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="validade_at">Validade</label>
                        <input id="validade_at" type="date" name="validade_at" class="form-control"
                            value="{{ old('validade_at', $preVendaAtual?->validade_at?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="observacoes">Observações</label>
                        <input id="observacoes" name="observacoes" class="form-control" maxlength="2000"
                            value="{{ old('observacoes', $preVendaAtual->observacoes ?? '') }}" placeholder="Condições ou detalhes da proposta">
                    </div>
                </div>
            </div>
        </div>

        <div class="card pre-sale-form-card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title font-weight-bold"><i class="fas fa-box-open mr-2 text-primary"></i>Itens</h3>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addItem">
                    <i class="fas fa-plus mr-1"></i> Adicionar item
                </button>
            </div>
            <div class="card-body">
                <div id="itemsContainer"></div>
                <p id="emptyItems" class="text-center text-muted mb-0 py-3">Adicione ao menos um produto.</p>
            </div>
        </div>

        <div class="card pre-sale-form-card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title font-weight-bold"><i class="fas fa-wallet mr-2 text-primary"></i>Previsão de pagamento</h3>
                    <small class="text-muted">Obrigatória somente para conversão em NFC-e.</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addPayment">
                    <i class="fas fa-plus mr-1"></i> Adicionar pagamento
                </button>
            </div>
            <div class="card-body">
                <div id="paymentsContainer"></div>
                <p id="emptyPayments" class="text-center text-muted mb-0 py-3">Nenhum pagamento previsto.</p>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card pre-sale-form-card mb-4 position-sticky" style="top: 1rem;">
            <div class="card-header">
                <h3 class="card-title font-weight-bold"><i class="fas fa-calculator mr-2 text-primary"></i>Resumo</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="desconto">Desconto geral</label>
                    <input id="desconto" type="number" min="0" step="0.01" name="desconto" class="form-control money-input"
                        value="{{ old('desconto', (float) ($preVendaAtual->desconto ?? 0)) }}">
                </div>
                <div class="form-group">
                    <label for="acrescimo">Acréscimo geral</label>
                    <input id="acrescimo" type="number" min="0" step="0.01" name="acrescimo" class="form-control money-input"
                        value="{{ old('acrescimo', (float) ($preVendaAtual->acrescimo ?? 0)) }}">
                </div>
                <div class="summary-line"><span>Itens</span><strong id="summarySubtotal">R$ 0,00</strong></div>
                <div class="summary-line"><span>Descontos</span><strong id="summaryDiscount">R$ 0,00</strong></div>
                <div class="summary-line"><span>Acréscimos</span><strong id="summaryAddition">R$ 0,00</strong></div>
                <div class="summary-line summary-total"><span>Total</span><span id="summaryTotal">R$ 0,00</span></div>
                <button class="btn btn-primary btn-block mt-4" type="submit">
                    <i class="fas fa-save mr-1"></i> Salvar pré-venda
                </button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const products = @json($produtos->map(fn ($produto) => [
        'id' => $produto->id,
        'company' => $produto->empresa_id,
        'name' => $produto->produto,
        'code' => $produto->codigo,
        'price' => (float) $produto->precovenda,
    ])->values());
    const paymentMethods = @json($formasPagamento->map(fn ($forma) => [
        'id' => $forma->id,
        'company' => $forma->empresa_id,
        'name' => $forma->descricao,
    ])->values());
    const initialItems = @json($itensIniciais);
    const initialPayments = @json($pagamentosIniciais);
    const itemsContainer = document.getElementById('itemsContainer');
    const paymentsContainer = document.getElementById('paymentsContainer');
    let itemIndex = 0;
    let paymentIndex = 0;

    const currency = value => Number(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    const selectedCompany = () => Number(document.getElementById('empresa_id').value);
    const escapeAttribute = value => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('"', '&quot;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;');
    const availableProducts = () => products.filter(product => Number(product.company) === selectedCompany());
    const availableMethods = () => paymentMethods.filter(method => Number(method.company) === selectedCompany() || Number(method.company) === 1);

    function createOption(value, label, selected) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = label;
        option.selected = String(value) === String(selected ?? '');
        return option;
    }

    function fillProducts(select, selected) {
        select.innerHTML = '';
        select.appendChild(createOption('', 'Selecione um produto', selected));
        availableProducts().forEach(product => {
            select.appendChild(createOption(product.id, `${product.name}${product.code ? ` — ${product.code}` : ''}`, selected));
        });
    }

    function fillMethods(select, selected) {
        select.innerHTML = '';
        select.appendChild(createOption('', 'Forma personalizada', selected));
        availableMethods().forEach(method => select.appendChild(createOption(method.id, method.name, selected)));
    }

    function addItem(data = {}) {
        const index = itemIndex++;
        const row = document.createElement('div');
        row.className = 'item-row';
        row.innerHTML = `
            <div class="row align-items-end">
                <div class="col-lg-5 form-group mb-lg-0">
                    <label>Produto</label>
                    <select name="itens[${index}][produto_id]" class="form-control product-select" required></select>
                </div>
                <div class="col-6 col-lg-2 form-group mb-lg-0">
                    <label>Quantidade</label>
                    <input name="itens[${index}][quantidade]" type="number" min="0.0001" step="0.0001" class="form-control calc-input quantity" value="${data.quantidade ?? 1}" required>
                </div>
                <div class="col-6 col-lg-2 form-group mb-lg-0">
                    <label>Valor unitário</label>
                    <input name="itens[${index}][valor_unitario]" type="number" min="0" step="0.0001" class="form-control calc-input unit-price" value="${data.valor_unitario ?? 0}" required>
                </div>
                <div class="col-6 col-lg-1 form-group mb-lg-0">
                    <label>Desc.</label>
                    <input name="itens[${index}][desconto]" type="number" min="0" step="0.01" class="form-control calc-input item-discount" value="${data.desconto ?? 0}">
                </div>
                <div class="col-6 col-lg-1 form-group mb-lg-0">
                    <label>Acrésc.</label>
                    <input name="itens[${index}][acrescimo]" type="number" min="0" step="0.01" class="form-control calc-input item-addition" value="${data.acrescimo ?? 0}">
                </div>
                <div class="col-lg-1 text-right">
                    <button type="button" class="btn btn-outline-danger remove-row" title="Remover"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <div class="text-right text-muted mt-2">Total do item: <strong class="item-total">R$ 0,00</strong></div>`;
        const select = row.querySelector('.product-select');
        fillProducts(select, data.produto_id);
        select.addEventListener('change', function () {
            const product = products.find(item => String(item.id) === this.value);
            if (product) row.querySelector('.unit-price').value = product.price;
            calculate();
        });
        row.querySelector('.remove-row').addEventListener('click', () => { row.remove(); calculate(); });
        row.querySelectorAll('.calc-input').forEach(input => input.addEventListener('input', calculate));
        itemsContainer.appendChild(row);
        calculate();
    }

    function addPayment(data = {}) {
        const index = paymentIndex++;
        const row = document.createElement('div');
        row.className = 'payment-row';
        row.innerHTML = `
            <div class="row align-items-end">
                <div class="col-md-4 form-group mb-md-0"><label>Forma</label><select name="pagamentos[${index}][forma_pag_id]" class="form-control method-select"></select></div>
                <div class="col-md-3 form-group mb-md-0"><label>Descrição</label><input name="pagamentos[${index}][descricao]" class="form-control method-description" value="${escapeAttribute(data.descricao)}" placeholder="Ex.: Dinheiro"></div>
                <div class="col-6 col-md-2 form-group mb-md-0"><label>Valor</label><input name="pagamentos[${index}][valor]" type="number" min="0.01" step="0.01" class="form-control" value="${data.valor ?? 0}" required></div>
                <div class="col-6 col-md-2 form-group mb-md-0"><label>Vencimento</label><input name="pagamentos[${index}][vencimento]" type="date" class="form-control" value="${escapeAttribute(data.vencimento)}"></div>
                <div class="col-md-1 text-right"><button type="button" class="btn btn-outline-danger remove-row" title="Remover"><i class="fas fa-trash"></i></button></div>
            </div>`;
        const select = row.querySelector('.method-select');
        fillMethods(select, data.forma_pag_id);
        select.addEventListener('change', function () {
            const method = paymentMethods.find(item => String(item.id) === this.value);
            if (method) row.querySelector('.method-description').value = method.name;
        });
        row.querySelector('.remove-row').addEventListener('click', () => { row.remove(); updateEmptyStates(); });
        paymentsContainer.appendChild(row);
        updateEmptyStates();
    }

    function calculate() {
        let subtotal = 0;
        let itemDiscounts = 0;
        let itemAdditions = 0;
        itemsContainer.querySelectorAll('.item-row').forEach(row => {
            const quantity = Number(row.querySelector('.quantity').value || 0);
            const price = Number(row.querySelector('.unit-price').value || 0);
            const discount = Number(row.querySelector('.item-discount').value || 0);
            const addition = Number(row.querySelector('.item-addition').value || 0);
            const itemSubtotal = quantity * price;
            const itemTotal = itemSubtotal - discount + addition;
            subtotal += itemSubtotal;
            itemDiscounts += discount;
            itemAdditions += addition;
            row.querySelector('.item-total').textContent = currency(itemTotal);
        });
        const generalDiscount = Number(document.getElementById('desconto').value || 0);
        const generalAddition = Number(document.getElementById('acrescimo').value || 0);
        document.getElementById('summarySubtotal').textContent = currency(subtotal);
        document.getElementById('summaryDiscount').textContent = currency(itemDiscounts + generalDiscount);
        document.getElementById('summaryAddition').textContent = currency(itemAdditions + generalAddition);
        document.getElementById('summaryTotal').textContent = currency(subtotal - itemDiscounts + itemAdditions - generalDiscount + generalAddition);
        updateEmptyStates();
    }

    function updateEmptyStates() {
        document.getElementById('emptyItems').style.display = itemsContainer.children.length ? 'none' : 'block';
        document.getElementById('emptyPayments').style.display = paymentsContainer.children.length ? 'none' : 'block';
    }

    function filterCompanyData() {
        const company = selectedCompany();
        document.querySelectorAll('#cliente_id option[data-company]').forEach(option => {
            option.hidden = Number(option.dataset.company) !== company;
        });
        const clientSelect = document.getElementById('cliente_id');
        if (clientSelect.selectedOptions[0]?.hidden) clientSelect.value = '';
        itemsContainer.querySelectorAll('.product-select').forEach(select => fillProducts(select, select.value));
        paymentsContainer.querySelectorAll('.method-select').forEach(select => fillMethods(select, select.value));
    }

    document.getElementById('addItem').addEventListener('click', () => addItem());
    document.getElementById('addPayment').addEventListener('click', () => addPayment());
    document.getElementById('desconto').addEventListener('input', calculate);
    document.getElementById('acrescimo').addEventListener('input', calculate);
    document.getElementById('empresa_id').addEventListener('change', filterCompanyData);
    initialItems.forEach(addItem);
    initialPayments.forEach(addPayment);
    if (!initialItems.length) addItem();
    filterCompanyData();
    calculate();
});
</script>
@endpush
