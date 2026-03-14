<div class="pdv-container">
    <form id="pdv-form" action="{{ route('cupom.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="acao_pos_salvar" id="acao_pos_salvar" value="depois">

        <!-- Header com título e cliente -->
        <div class="pdv-header">
            <h2 class="pdv-title">
                <i class="fas fa-cash-register"></i>
                Ponto de Venda
            </h2>
            @if (!empty($cliente))
                <div class="cliente-info">
                    <span class="cliente-label">Cliente:</span>
                    <span class="cliente-nome">{{ $cliente['nome'] }}</span>
                    @if ($cliente['nome'] != 'Consumidor Final')
                        <button title="Remover Cliente" class="btn-remove-client" type="button"
                            wire:click="removeClient">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                    <input type="hidden" name="cliente[id]" wire:model="cliente.id">
                </div>
            @endif
        </div>

        <!-- Layout principal -->
        <div class="pdv-main-layout">
            <!-- Coluna esquerda - Botões de ação -->
            <div class="pdv-sidebar">
                <div class="action-buttons">
                    <button class="btn-action btn-primary" type="button" wire:click="searchCustomers">
                        <i class="fas fa-user-plus"></i>
                        <span>Buscar Cliente</span>
                        <small>(F1)</small>
                    </button>

                    <button class="btn-action btn-secundario" type="button"
                        @if (!empty($formasSelecionadas)) disabled @endif wire:click="searchProducts">
                        <i class="fas fa-search"></i>
                        <span>Buscar Produto</span>
                        <small>(F2)</small>
                    </button>

                    <button class="btn-action btn-danger" type="button"
                        @if (!empty($formasSelecionadas)) disabled @endif wire:click="clearItems">
                        <i class="fas fa-trash"></i>
                        <span>Limpar Itens</span>
                        <small>(F4)</small>
                    </button>

                    @if (!empty($itens))
                        <button class="btn-action btn-sucesso" type="button" wire:click="showPaymentArea">
                            <i class="fas fa-credit-card"></i>
                            <span>Finalizar Venda</span>
                            <small>(F5)</small>
                        </button>
                    @endif

                    @if (!empty($formasSelecionadas))
                        <button class="btn-action btn-warning" type="button" wire:click="clearMethods">
                            <i class="fas fa-undo"></i>
                            <span>Limpar Pagamento</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Área central - Lista de itens -->
            <div class="pdv-center">
                <!-- Lista de itens -->
                <div class="items-container">
                    <div class="items-header">
                        <h3>Itens da Venda</h3>
                        <span class="badge bg-primary">{{ count($itens) }} item(s)</span>
                    </div>

                    <div class="items-list">
                        @forelse ($itens as $index => $item)
                            <div class="item-card">
                                <div class="item-info">
                                    <div class="item-number">{{ $index + 1 }}</div>
                                    <div class="item-details">
                                        <div class="item-name">{{ $item['produto'] }}</div>
                                        <div class="item-code">Código: {{ $item['codigo'] ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                <div class="item-values">
                                    <div class="value-row">
                                        <span class="value-label">Qtd:</span>
                                        <span class="value-number">{{ $item['qtde'] }}</span>
                                    </div>
                                    <div class="value-row">
                                        <span class="value-label">Unit:</span>
                                        <span class="value-number">R$
                                            {{ number_format($item['unitario'], 2, ',', '.') }}</span>
                                    </div>
                                    @if ($item['desconto'] > 0)
                                        <div class="value-row discount">
                                            <span class="value-label">Desc:</span>
                                            <span class="value-number">-R$
                                                {{ number_format($item['desconto'] * $item['qtde'], 2, ',', '.') }}</span>
                                        </div>
                                    @endif
                                    @if ($item['acrescimo'] > 0)
                                        <div class="value-row addition">
                                            <span class="value-label">Acrés:</span>
                                            <span class="value-number">+R$
                                                {{ number_format($item['acrescimo'] * $item['qtde'], 2, ',', '.') }}</span>
                                        </div>
                                    @endif
                                    <div class="value-row total">
                                        <span class="value-label">Total:</span>
                                        <span class="value-number">R$
                                            {{ number_format($item['total'], 2, ',', '.') }}</span>
                                    </div>
                                </div>

                                @if (empty($formasSelecionadas))
                                    <div class="item-actions">
                                        <button class="btn btn-sm btn-outline-warning" type="button"
                                            wire:click="editItem({{ $index }})" title="Editar Item">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" type="button"
                                            wire:click="removeItem({{ $index }})" title="Remover Item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endif

                                <!-- Campos hidden para o form -->
                                <input type="hidden" name="itens[{{ $index }}][prodId]"
                                    wire:model="itens.{{ $index }}.prodId">
                                <input type="hidden" name="itens[{{ $index }}][produto]"
                                    wire:model="itens.{{ $index }}.produto">
                                <input type="hidden" name="itens[{{ $index }}][qtde]"
                                    wire:model="itens.{{ $index }}.qtde">
                                <input type="hidden" name="itens[{{ $index }}][unitario]"
                                    wire:model="itens.{{ $index }}.unitario">
                                <input type="hidden" name="itens[{{ $index }}][desconto]"
                                    wire:model="itens.{{ $index }}.desconto">
                                <input type="hidden" name="itens[{{ $index }}][acrescimo]"
                                    wire:model="itens.{{ $index }}.acrescimo">
                                <input type="hidden" name="itens[{{ $index }}][total]"
                                    wire:model="itens.{{ $index }}.total">
                                <input type="hidden" name="itens[{{ $index }}][subtotal]"
                                    wire:model="itens.{{ $index }}.subtotal">
                            </div>
                        @empty
                            <div class="empty-items">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Nenhum item adicionado</p>
                                <small class="text-muted">Use o campo de busca para adicionar produtos</small>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        


        <div class="pdv-footer">
<!-- Área de entrada de produto -->

    <div class="row g-2 align-items-end row-cols-1 row-cols-md-6">
        <!-- Produto -->
        <div class="col-md-2">
            <label class="form-label"><i class="fas fa-barcode"></i>
        Adicionar Produto</label>
            <div class="input-group">
                <input type="text" class="form-control"
                    @isset($cod) disabled @endisset wire:model="prod"
                    wire:keydown.enter="searchProds()" placeholder="Código ou nome do produto">
                @isset($cod)
                    <button class="btn btn-outline-danger" type="button" wire:click="cancelProd">
                        <i class="fas fa-times"></i>
                    </button>
                @endisset
            </div>
        </div>

        <!-- Quantidade -->
        <div class="col-md-2">
            <label class="form-label">Quantidade</label>
            <input type="number" @if (!isset($cod)) disabled @endif class="form-control"
                wire:model="qtde" wire:change="updateProductTotal" min="1" required>
        </div>

        <!-- Desconto -->
        <div class="col-md-2">
            <div class="d-flex justify-content-between align-items-center">
                <label class="form-label mb-0">Desconto</label>
                @if ($desconto > 0 && isset($cod))
                    <small class="form-text text-success fw-bold mb-0 ms-2">
                        R$ {{ number_format($descontoCalculadoItem, 2, ',', '.') }}
                    </small>
                @endif
            </div>
            <div class="input-group">
                <input type="number" @if (!isset($cod)) disabled @endif
                    class="form-control" wire:model="desconto" wire:change="updateProductTotal"
                    min="0"
                    @if ($descontoTipo === 'percent') max="100" @else max="{{ $unitario }}" @endif
                    step="0.01">
                <button class="btn btn-outline-secondary btn-sm" type="button"
                    wire:click="toggleDescontoTipo" title="Alternar entre % e R$">
                    @if ($descontoTipo === 'percent')
                        <i class="fas fa-percent"></i>
                    @else
                        <i class="fas fa-dollar-sign"></i>
                    @endif
                </button>
            </div>
        </div>

        <!-- Acréscimo -->
        <div class="col-md-2">
            <div class="d-flex justify-content-between align-items-center">
                <label class="form-label mb-0">Acréscimo</label>
                @if ($acrescimo > 0 && isset($cod))
                    <small class="form-text text-danger fw-bold mb-0 ms-2">
                        R$ {{ number_format($acrescimoCalculadoItem, 2, ',', '.') }}
                    </small>
                @endif
            </div>
            <div class="input-group">
                <input type="number" @if (!isset($cod)) disabled @endif
                    class="form-control" wire:model="acrescimo" wire:change="updateProductTotal"
                    min="0" step="0.01">
                <button class="btn btn-outline-secondary btn-sm" type="button"
                    wire:click="toggleAcrescimoTipo" title="Alternar entre % e R$">
                    @if ($acrescimoTipo === 'percent')
                        <i class="fas fa-percent"></i>
                    @else
                        <i class="fas fa-dollar-sign"></i>
                    @endif
                </button>
            </div>
        </div>

        <!-- Total -->
        <div class="col-md-2">
            <div class="d-flex justify-content-between align-items-center">
                <label class="form-label mb-0">Total</label>
            </div>
            <input type="number" @if (!isset($cod)) disabled @endif
                class="form-control total-input" wire:model="total" min="0" readonly>
        </div>

        <!-- Botão -->
        <div class="col-md-2">
            <label class="form-label d-block">&nbsp;</label>
            @if ($editProd)
                <button class="btn btn-warning w-100" type="button" wire:click="updateProd">
                    <i class="fas fa-save"></i>
                </button>
            @else
                <button class="btn btn-sucesso w-100" type="button"
                    @if (!empty($formasSelecionadas)) disabled @endif wire:click="addProd">
                    <i class="fas fa-plus"></i>
                </button>
            @endif
        </div>
    </div>


        </div>

        

        <!-- Totais fixos na parte inferior -->
        <div class="pdv-footer">
            <div class="totals-container">
                <div class="total-item">
                    <span class="total-label">Subtotal:</span>
                    <span class="total-value">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                </div>
                @if ($descontoTotalCalculado > 0)
                    <div class="total-item discount">
                        <span class="total-label">Desconto Total:</span>
                        <span class="total-value">-R$ {{ number_format($descontoTotalCalculado, 2, ',', '.') }}</span>
                    </div>
                @endif
                @if ($acrescimoTotalCalculado > 0)
                    <div class="total-item addition">
                        <span class="total-label">Acréscimo Total:</span>
                        <span class="total-value">+R$
                            {{ number_format($acrescimoTotalCalculado, 2, ',', '.') }}</span>
                    </div>
                @endif
                <div class="total-item final-total">
                    <span class="total-label">TOTAL GERAL:</span>
                    <span class="total-value">R$ {{ number_format($valorTotal, 2, ',', '.') }}</span>
                </div>
                <input type="hidden" name="valorTotal" wire:model="valorTotal">
            </div>
        </div>

        <!-- Área de pagamento (quando ativada) -->
        @if ($showPaymentArea == 'block')
            <div class="payment-area" id="addPaymentMethod">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-md-7 d-flex align-items-stretch">
                            <div class="card w-100">
                                <div class="card-header" style="background: #00033a; color: white;">
                                    <h4 class="mb-0">
                                        <i class="fas fa-credit-card"></i>
                                        Formas de Pagamento
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach ($formas as $forma)
                                            <div class="col-6 col-md-6 mb-3">
                                                <div class="payment-method-card"
                                                    wire:click="addPaymentMethod('{{ $forma }}')">
                                                    <div class="payment-method-body text-center">
                                                        <p class="payment-method-name">
                                                            <b>{{ Illuminate\Support\Str::title(str_replace('_', ' ', $forma)) }}</b>
                                                        </p>
                                                        @if ($forma == 'DINHEIRO')
                                                            <i class="fas fa-money-bill fa-2x text-success"></i>
                                                        @elseif ($forma == 'PIX')
                                                            <i class="fas fa-qrcode fa-2x text-info"></i>
                                                        @elseif ($forma == 'CARTAO_CREDITO')
                                                            <i class="fas fa-credit-card fa-2x text-primary"></i>
                                                        @else
                                                            <i class="fas fa-credit-card fa-2x text-secondary"></i>
                                                        @endif
                                                    </div>
                                                    <div class="payment-method-footer">
                                                        <input class="form-control text-center" readonly
                                                            type="text" name="formas[{{ $forma }}]"
                                                            wire:model="formasSelecionadas.{{ $forma }}"
                                                            placeholder="R$ 0,00">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- COLUNA DIREITA: RESUMO DA VENDA --}}
                        <div class="col-12 col-md-5 d-flex align-items-stretch">
                            <div class="card w-100">
                                <div class="card-header" style="background: #00033a; color: white;">
                                    <h5 class="mb-0">
                                        <i class="fas fa-calculator"></i>
                                        Resumo da Venda
                                    </h5>
                                </div>
                                <div class="card-body">
                                    {{-- Linha Subtotal --}}
                                    <div class="row mb-2">
                                        <div class="col">
                                            <div class="input-group">
                                                <span class="input-group-text payment-label">Subtotal</span>
                                                <input type="number" class="form-control" wire:model="subtotal"
                                                    name="subtotal" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Linha Desconto --}}
                                    <div class="row mb-2">
                                        <div class="col">
                                            <div class="input-group">
                                                <span class="input-group-text payment-label">Desconto</span>
                                                <input type="number"
                                                    @if (!empty($formasSelecionadas)) readonly @endif
                                                    class="form-control" wire:model="descontoTotal" min="0"
                                                    @if ($descontoTotalTipo === 'percent') max="100" @else max="{{ $valorTotal }}" @endif
                                                    wire:change="updateSaleTotal" wire:keyup="updateSaleTotal">
                                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                                    wire:click="toggleDescontoTotalTipo"
                                                    title="Alternar entre % e R$">
                                                    @if ($descontoTotalTipo === 'percent')
                                                        <i class="fas fa-percent"></i>
                                                    @else
                                                        <i class="fas fa-dollar-sign"></i>
                                                    @endif
                                                </button>
                                            </div>
                                            <input type="hidden" name="descontoTotal"
                                                wire:model="descontoTotalCalculado">
                                        </div>
                                    </div>

                                    {{-- Linha Acréscimo --}}
                                    <div class="row mb-2">
                                        <div class="col">
                                            <div class="input-group">
                                                <span class="input-group-text payment-label">Acréscimo</span>
                                                <input type="number"
                                                    @if (!empty($formasSelecionadas)) readonly @endif
                                                    class="form-control" wire:model="acrescimoTotal" min="0"
                                                    wire:change="updateSaleTotal" wire:keyup="updateSaleTotal">
                                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                                    wire:click="toggleAcrescimoTotalTipo"
                                                    title="Alternar entre % e R$">
                                                    @if ($acrescimoTotalTipo === 'percent')
                                                        <i class="fas fa-percent"></i>
                                                    @else
                                                        <i class="fas fa-dollar-sign"></i>
                                                    @endif
                                                </button>
                                            </div>
                                            <input type="hidden" name="acrescimoTotal"
                                                wire:model="acrescimoTotalCalculado">
                                        </div>
                                    </div>

                                    {{-- Linha Total --}}
                                    <div class="row mb-2">
                                        <div class="col">
                                            <div class="input-group">
                                                <span class="input-group-text payment-label-total">Total</span>
                                                <input type="number" disabled class="form-control payment-total"
                                                    wire:model="valorTotal">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Linha Valor Pago --}}
                                    <div class="row mb-2">
                                        <div class="col">
                                            <div class="input-group">
                                                <span class="input-group-text payment-label">Valor Pago</span>
                                                <input type="number" class="form-control" wire:model="valorPago"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Linha Troco ou Restante --}}
                                    @if ($troco > 0)
                                        <div class="row mb-2">
                                            <div class="col">
                                                <div class="input-group">
                                                    <span class="input-group-text payment-label-success">Troco</span>
                                                    <input type="number" class="form-control payment-troco"
                                                        wire:model="troco" name="troco" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="row mb-2">
                                            <div class="col">
                                                <div class="input-group">
                                                    <span
                                                        class="input-group-text payment-label-warning">Restante</span>
                                                    <input type="number" class="form-control payment-restante"
                                                        wire:model="aReceber" name="aReceber" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Linha Botões --}}
                                    <div class="row text-center mt-3">
                                        <div class="col">
                                            <button class="btn btn-outline-secondary me-2" type="button"
                                                wire:click="clearMethods"
                                                @if (empty($formasSelecionadas)) disabled @endif>
                                                <i class="fas fa-eraser"></i>
                                                Limpar
                                            </button>
                                            <button class="btn btn-sucesso"
                                                @if ($valorPago < $valorTotal || $troco < 0) disabled @endif type="button"
                                                onclick="confirmarFinalizacao()">
                                                <i class="fas fa-check"></i>
                                                Finalizar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </form>

    <!-- Modal de busca de produtos -->
    <div class="modal fade" id="AddProdModal" tabindex="-1" aria-labelledby="AddProdModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="AddProdModalLabel">
                        <i class="fas fa-search"></i>
                        Buscar Produto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Produto</th>
                                    <th>Código</th>
                                    <th>Valor Unitário</th>
                                </tr>
                            </thead>
                            <tbody class="product-table-body">
                                <!-- Produtos serão inseridos via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de forma de pagamento -->
    <div class="modal fade" id="PaymentModal" tabindex="-1" aria-labelledby="PaymentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="PaymentModalLabel">
                        <i class="fas fa-credit-card"></i>
                        Forma de Pagamento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Método: <strong>{{ $selectedForma }}</strong></label>
                    </div>
                    <div class="mb-3">
                        <label for="valorRecebimento" class="form-label">Valor Recebido:</label>
                        <input type="number" class="form-control" wire:model="valorRecebimento" min="0"
                            step="0.01" placeholder="0,00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success"
                        wire:click="updateValueReceived">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de busca de clientes -->
    <div class="modal fade" id="SearchClientModal" tabindex="-1" aria-labelledby="SearchClientModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="SearchClientModalLabel">
                        <i class="fas fa-users"></i>
                        Buscar Cliente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Nome</th>
                                    <th>CPF/CNPJ</th>
                                    <th>Endereço</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (json_decode($customers) as $client)
                                    <tr wire:dblclick="selectClient('{{ $client->id }}', '{{ $client->nome }}')"
                                        style="cursor: pointer;">
                                        <td>{{ $client->codigo }}</td>
                                        <td>{{ $client->nome }}</td>
                                        <td>{{ $client->cpf_cnpj }}</td>
                                        <td>{{ $client->nome }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Reset e configurações gerais */
        * {
            box-sizing: border-box;
        }

        .pdv-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Header */
        .pdv-header {
            background: #00033a;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 3, 58, 0.3);
        }

        .pdv-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .cliente-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .cliente-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .cliente-nome {
            font-weight: 600;
        }

        .btn-remove-client {
            background: rgba(220, 53, 69, 0.8);
            border: none;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-remove-client:hover {
            background: #dc3545;
            transform: scale(1.1);
        }

        /* Layout principal */
        .pdv-main-layout {
            display: flex;
            height: calc(100vh - 440px);
            gap: 1rem;
            padding: 1rem;
        }

        /* Sidebar esquerda */
        .pdv-sidebar {
            width: 320px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            overflow-y: auto;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .btn-action {
            background: white;
            border: 2px solid #00033a;
            color: #00033a;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            text-align: center;
        }

        .btn-action:hover:not(:disabled) {
            background: #00033a;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 3, 58, 0.3);
        }

        .btn-action:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-action i {
            font-size: 1.2rem;
        }

        .btn-action small {
            font-size: 0.7rem;
            opacity: 0.7;
        }

        .btn-primary {
            border-color: #007bff;
            color: #007bff;
        }

        .btn-primary:hover:not(:disabled) {
            background: #007bff;
        }

        .btn-secundario {
            border-color: #6c757d;
            color: #6c757d;
        }

        .btn-secundario:hover:not(:disabled) {
            background: #6c757d;
        }

        .btn-danger {
            border-color: #dc3545;
            color: #dc3545;
        }

        .btn-danger:hover:not(:disabled) {
            background: #dc3545;
        }

        .btn-sucesso {
            border-color: #28a745;
            color: #28a745;
        }

        .btn-sucesso:hover:not(:disabled) {
            background: #28a745;
        }

        .btn-warning {
            border-color: #ffc107;
            color: #856404;
        }

        .btn-warning:hover:not(:disabled) {
            background: #ffc107;
        }

        /* Seção de entrada de produto */
        .product-input-section {
            border-top: 2px solid #e9ecef;
            padding-top: 1.5rem;
        }

        .section-title {
            color: #00033a;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .total-input {
            background: #f8f9fa !important;
            font-weight: 600;
            color: #00033a;
        }

        /* Área central */
        .pdv-center {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        /* Área de pagamento */
        .payment-area {
            margin: 0 1rem 1rem;
        }

        .payment-method-card {
            border: 2px solid #00033a;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            height: 100%;
        }

        .payment-method-card:hover {
            background: #00033a;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 3, 58, 0.3);
        }

        .payment-method-card:hover i {
            color: white !important;
        }

        .payment-method-body {
            padding: 1rem;
        }

        .payment-method-name {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .payment-method-footer {
            padding: 0.5rem;
            border-top: 1px solid #e9ecef;
        }

        .payment-label {
            background: #00033a !important;
            color: white !important;
            width: 40% !important;
            display: flex !important;
            justify-content: end !important;
            font-weight: 500;
        }

        .payment-label-total {
            background: #28a745 !important;
            color: white !important;
            width: 40% !important;
            display: flex !important;
            justify-content: end !important;
            font-weight: 600;
        }

        .payment-label-success {
            background: #28a745 !important;
            color: white !important;
            width: 40% !important;
            display: flex !important;
            justify-content: end !important;
            font-weight: 500;
        }

        .payment-label-warning {
            background: #ffc107 !important;
            color: #856404 !important;
            width: 40% !important;
            display: flex !important;
            justify-content: end !important;
            font-weight: 500;
        }

        .payment-total {
            background: #f8f9fa !important;
            font-weight: 600;
            color: #28a745;
        }

        .payment-troco {
            background: #d4edda !important;
            font-weight: 600;
            color: #155724;
        }

        .payment-restante {
            background: #fff3cd !important;
            font-weight: 600;
            color: #856404;
        }

        .items-container {
            flex: 1;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .items-header {
            background: #00033a;
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .items-header h3 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .items-list {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        .item-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border: 2px solid #f8f9fa;
            border-radius: 12px;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
            background: white;
            animation: slideIn 0.3s ease-out;
        }

        .item-card:hover {
            border-color: #00033a;
            box-shadow: 0 2px 10px rgba(0, 3, 58, 0.1);
        }

        .item-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
        }

        .item-number {
            background: #00033a;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #212529;
            margin-bottom: 0.25rem;
        }

        .item-code {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .item-values {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            min-width: 120px;
        }

        .value-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .value-label {
            font-size: 0.75rem;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 500;
        }

        .value-number {
            font-weight: 600;
            color: #212529;
            font-size: 0.85rem;
        }

        .value-row.discount .value-number {
            color: #dc3545;
        }

        .value-row.addition .value-number {
            color: #28a745;
        }

        .value-row.total .value-number {
            color: #00033a;
            font-size: 0.95rem;
        }

        .item-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .empty-items {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }

        /* Footer com totais */
        .pdv-footer {
            background: white;
            border-radius: 15px;
            margin: 0 1rem 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }

        .totals-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .total-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
        }

        .total-label {
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 500;
        }

        .total-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #212529;
        }

        .total-item.discount .total-value {
            color: #dc3545;
        }

        .total-item.addition .total-value {
            color: #28a745;
        }

        .total-item.final-total {
            background: #00033a;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
        }

        .total-item.final-total .total-label,
        .total-item.final-total .total-value {
            color: white;
        }

        .total-item.final-total .total-value {
            font-size: 1.5rem;
        }

        /* Área de pagamento */
        .payment-area {
            margin: 0 1rem 1rem;
        }

        .payment-methods {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        /* Responsividade */
        @media (max-width: 1200px) {
            .pdv-main-layout {
                flex-direction: column;
                height: auto;
            }

            .pdv-sidebar {
                width: 100%;
                order: 2;
            }

            .pdv-center {
                order: 1;
            }

            .action-buttons {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .btn-action {
                flex: 1;
                min-width: 150px;
            }
        }

        @media (max-width: 768px) {
            .pdv-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .pdv-main-layout {
                padding: 0.5rem;
                gap: 0.5rem;
            }

            .pdv-sidebar {
                padding: 1rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-action {
                min-width: auto;
            }

            .item-card {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
            }

            .item-values {
                flex-direction: row;
                justify-content: space-between;
                flex-wrap: wrap;
            }

            .totals-container {
                flex-direction: column;
                align-items: stretch;
            }

            .total-item.final-total {
                order: -1;
            }

            .payment-methods {
                flex-direction: column;
            }
        }

        /* Animações */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Scrollbar personalizada */
        .items-list::-webkit-scrollbar {
            width: 8px;
        }

        .items-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .items-list::-webkit-scrollbar-thumb {
            background: #00033a;
            border-radius: 4px;
        }

        .items-list::-webkit-scrollbar-thumb:hover {
            background: #000529;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (() => {
            'use strict'

            const forms = document.querySelectorAll('.needs-validation')

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                        const accordion = document.getElementById('accordion');
                        if (accordion) {
                            accordion.classList.add('invalid-accordion');
                        }
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
@endsection

<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
<script>
    document.addEventListener('keydown', function(event) {
        if (event.key === 'enter' || event.keyCode === 13) {
            event.preventDefault();
        } else if (event.key === 'F1' || event.keyCode === 112) {
            event.preventDefault();
            Livewire.emit('searchCustomers');
        } else if (event.key === 'F2' || event.keyCode === 113) {
            event.preventDefault();
            Livewire.emit('searchProducts');
        } else if (event.key === 'F4' || event.keyCode === 115) {
            event.preventDefault();
            Livewire.emit('clearItems');
        } else if (event.key === 'F5' || event.keyCode === 116) {
            event.preventDefault();
            Livewire.emit('showPaymentArea');
        }
    });

    document.addEventListener('livewire:load', function() {
        Livewire.on('OpenAddProdModal', function(data) {
            if (data.length > 0) {
                const modal = new bootstrap.Modal(document.getElementById('AddProdModal'));
                modal.show();

                const tbody = document.querySelector('.product-table-body');
                tbody.innerHTML = '';

                data.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.style.cursor = 'pointer';
                    tr.addEventListener('dblclick', function() {
                        Livewire.emit('selectProd', item.id, item.produto, item.codigo,
                            item.precovenda);
                    });

                    const tdProduto = document.createElement('td');
                    tdProduto.textContent = item.produto || 'Produto Desconhecido';

                    const tdCodigo = document.createElement('td');
                    tdCodigo.textContent = item.codigo;

                    const tdUnitario = document.createElement('td');
                    tdUnitario.textContent = item.precovenda.toLocaleString('pt-BR', {
                        style: 'currency',
                        currency: 'BRL'
                    });

                    tr.appendChild(tdProduto);
                    tr.appendChild(tdCodigo);
                    tr.appendChild(tdUnitario);

                    tbody.appendChild(tr);
                });
            } else {
                alert('Produtos não encontrados...')
            }
        });

        Livewire.on('OpenCustomersModal', function() {
            const modal = new bootstrap.Modal(document.getElementById('SearchClientModal'));
            modal.show();
        });

        Livewire.on('CloseCustomersModal', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('SearchClientModal'));
            if (modal) modal.hide();
        });

        Livewire.on('CloseAddProdModal', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('AddProdModal'));
            if (modal) modal.hide();
        });

        Livewire.on('OpenPaymentModal', function() {
            const modal = new bootstrap.Modal(document.getElementById('PaymentModal'));
            modal.show();
        });

        Livewire.on('ClosePaymentModal', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('PaymentModal'));
            if (modal) modal.hide();
        });

        Livewire.on('ShowPaymentArea', function() {
            const paymentArea = document.getElementById('addPaymentMethod');
            if (paymentArea) {
                paymentArea.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });

        Livewire.on('ProdutoJaInserido', function(message) {
            alert(message)
        });

        Livewire.on('ErrorInPayment', function($message) {
            alert($message)
        });
    });
</script>
<script>
    function confirmarFinalizacao() {
        // Pega o formulário e o input hidden que criamos
        const form = document.getElementById('pdv-form');
        const acaoInput = document.getElementById('acao_pos_salvar');

        Swal.fire({
            title: 'Finalizar Venda',
            text: "Deseja emitir a NFC-e agora ou deixar para depois?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Emitir Agora',
            cancelButtonText: 'Salvar e Emitir Depois'
        }).then((result) => {
            // Se o usuário clicou em "Emitir Agora"
            if (result.isConfirmed) {
                // Define o valor do input hidden para 'agora'
                acaoInput.value = 'agora';
                // Envia o formulário
                form.submit();
            }
            // Se o usuário clicou em "Salvar e Emitir Depois" (o botão de cancelar)
            else if (result.dismiss === Swal.DismissReason.cancel) {
                // O valor do input já é 'depois' por padrão, então apenas enviamos o formulário
                acaoInput.value = 'depois';
                form.submit();
            }
        });
    }
</script>
