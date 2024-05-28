<div>
    {{-- <form action=""> --}}
    <section>
        <div class="card">
            <div class="card-header">
                <div class="row text-center">
                    <div class="col">
                        <h4>Venda em Aberto</h4>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="container mt-3">
                    <div class="row">
                        <div class="col-md-7 d-flex align-items-stretch">
                            <div class="table-responsive shadow p-3 mb-3 bg-body rounded w-100">
                                <table class="table table-hover w-100">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Item</th>
                                            <th>Nome</th>
                                            <th>Qtde.</th>
                                            <th>Unitário</th>
                                            <th>Desconto</th>
                                            <th>Acrescimo</th>
                                            <th>Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($itens as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item['produto'] }}</td>
                                                <td>{{ $item['qtde'] }}</td>
                                                <td>{{ number_format($item['unitario'], 2) }}</td>
                                                <td>{{ number_format($item['desconto'], 2) }}</td>
                                                <td>{{ number_format($item['acrescimo'], 2) }}</td>
                                                <td>{{ number_format($item['total'], 2) }}</td>
                                                <td>
                                                    @if (empty($formasSelecionadas))
                                                        <a title="Remover Item" class="text-danger"
                                                            wire:click="removeItem({{ $index }})"><i
                                                                class="fa fa-trash"></i></a>
                                                        <a title="Editar Item" class="text-info"
                                                            wire:click="editItem({{ $index }})"><i
                                                                class="fa fa-edit"></i></a>
                                                    @endif
                                                </td>
                                            </tr>

                                            <input type="hidden" name="itens[{{ $index }}][produto]"
                                                wire:model="itens.{{ $index }}.produto" required>
                                            <input type="hidden" name="itens[{{ $index }}][qtde]"
                                                wire:model="itens.{{ $index }}.qtde" required>
                                            <input type="hidden" name="itens[{{ $index }}][unitario]"
                                                wire:model="itens.{{ $index }}.unitario" required>
                                            <input type="hidden" name="itens[{{ $index }}][desconto]"
                                                wire:model="itens.{{ $index }}.desconto" required>
                                            <input type="hidden" name="itens[{{ $index }}][acrescimo]"
                                                wire:model="itens.{{ $index }}.acrescimo" required>
                                            <input type="hidden" name="itens[{{ $index }}][total]"
                                                wire:model="itens.{{ $index }}.total" required>
                                        @empty
                                            <tr class="text-center">
                                                <td colspan="7">
                                                    <span class="text-blue">Sem itens...</span>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col col-md-5 d-flex align-items-stretch">
                            <div class="card w-100">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <img src="{{ asset('logo_pdv.jpg') }}" class="w-100 h-100"
                                                alt="Ícone do PDV">
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col">
                                            <div class="input-group">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control"
                                                        @isset($cod) disabled @endisset
                                                        wire:model="prod" wire:keydown.enter="searchProds()"
                                                        placeholder=" ">
                                                    <label for="prod">Produto</label>
                                                </div>
                                                @isset($cod)
                                                    <button class="btn btn-secondary" wire:click="cancelProd"><i
                                                            class="fa fa-times"></i></button>
                                                @endisset
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col">
                                            <div class="input-group">
                                                <div class="form-floating">
                                                    <input type="number"
                                                        @if (!isset($cod)) disabled @endif
                                                        class="form-control" name="qtde" wire:model="qtde"
                                                        wire:change="updateProductTotal" placeholder=" " min="1"
                                                        required>
                                                    <label for="qtde">Quantidade</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group">
                                                <div class="form-floating">
                                                    <input type="number"
                                                        @if (!isset($cod)) disabled @endif
                                                        class="form-control" wire:model="desconto"
                                                        wire:change="updateProductTotal" placeholder=" " min="0"
                                                        step="0.01" required>
                                                    <label for="desconto">Desconto</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="input-group">
                                                <div class="form-floating">
                                                    <input type="number"
                                                        @if (!isset($cod)) disabled @endif
                                                        class="form-control" wire:model="acrescimo"
                                                        wire:change="updateProductTotal" placeholder=" " min="0"
                                                        step="0.01" required>
                                                    <label for="acrescimo">Acrescimo</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col">
                                            <div class="input-group">
                                                <div class="form-floating">
                                                    <input type="number"
                                                        @if (!isset($cod)) disabled @endif
                                                        class="form-control" name="total" wire:model="total"
                                                        placeholder=" " min="0" required>
                                                    <label for="total">Total</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row text-center mt-2">
                                        <div class="col">
                                            @if ($editProd)
                                                <button class="btn btn-outline-primary"
                                                    wire:click="updateProd">Salvar</button>
                                            @else
                                                <button class="btn btn-outline-primary"
                                                    @if (!empty($formasSelecionadas)) disabled @endif
                                                    wire:click="addProd">+ Adicionar</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="row text-center">
                    <div class="col">
                        <h5>Valor Total: <b>R$ {{ number_format($valorTotal, 2) }}</b></h5>
                        <input type="hidden" name="valorTotal" wire:model="valorTotal" required>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col">
                            <button class="btn btn-outline-info" wire:click="searchCustomers">Buscar Cliente
                                (F1)</button>
                        </div>

                        <div class="col">
                            <button class="btn btn-outline-dark" @if (!empty($formasSelecionadas)) disabled @endif
                                wire:click="searchProducts">Buscar Produto (F2)</button>
                        </div>

                        <div class="col">
                            <button class="btn btn-outline-danger" @if (!empty($formasSelecionadas)) disabled @endif
                                wire:click="clearItems">Limpar Itens (F4)</button>
                        </div>
                    </div>

                    <div class="row text-center mt-2">
                        <div class="col">
                            <h6>Atalhos Rápidos</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="row mb-3">
        <div class="col text-center">
            <button class="btn btn-outline-success btn-lg" @if (empty($itens)) disabled @endif
                wire:click="showPaymentArea">Encerrar Cupom</button>
        </div>
    </div>
    {{-- </form> --}}

    <section>
        <div id="addPaymentMethod" style="display: {{ $showPaymentArea }}">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-7 d-flex align-items-stretch">
                        <div class="row">
                            @foreach ($formas as $forma)
                                <div class="col-6 col-md-6">
                                    <div class="card" wire:click="addPaymentMethod('{{ $forma }}')">
                                        <div class="card-body pb-5 text-center">
                                            <p><b>{{ $forma }}</b></p>
                                        </div>
                                        <div class="card-footer">
                                            <input class="form-control-plaintext text-center" readonly type="text"
                                                wire:model="formasSelecionadas.{{ $forma }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12 col-md-5 d-flex align-items-stretch">
                        <div class="card w-100">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="input-group">
                                            <span
                                                class="input-group-text bg-secondary w-50 d-flex justify-content-end">Subtotal</span>
                                            <input type="number" class="form-control" wire:model="subtotal"
                                                name="subtotal" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="input-group">
                                            <span
                                                class="input-group-text bg-secondary w-50 d-flex justify-content-end">Desconto</span>
                                            <input type="number" @if (!empty($formasSelecionadas)) readonly @endif
                                                class="form-control" wire:model="descontoTotal" name="descontoTotal"
                                                wire:change="updateSaleTotal">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="input-group">
                                            <span
                                                class="input-group-text bg-secondary w-50 d-flex justify-content-end">Acrescimo</span>
                                            <input type="number" @if (!empty($formasSelecionadas)) readonly @endif
                                                class="form-control" wire:model="acrescimoTotal"
                                                name="acrescimoTotal" wire:change="updateSaleTotal">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="input-group">
                                            <span
                                                class="input-group-text bg-secondary w-50 d-flex justify-content-end">Total</span>
                                            <input type="number" disabled class="form-control"
                                                wire:model="valorTotal">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="input-group">
                                            <span
                                                class="input-group-text bg-secondary w-50 d-flex justify-content-end">Valor
                                                Pago</span>
                                            <input type="number" class="form-control" wire:model="valorPago"
                                                name="valorPago" readonly>
                                        </div>
                                    </div>
                                </div>

                                @if ($troco > 0)
                                    <div class="row mb-2">
                                        <div class="col">
                                            <div class="input-group">
                                                <span
                                                    class="input-group-text bg-secondary w-50 d-flex justify-content-end">Troco</span>
                                                <input type="number" class="form-control" wire:model="troco"
                                                    name="troco" readonly>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="row mb-2">
                                        <div class="col">
                                            <div class="input-group">
                                                <span
                                                    class="input-group-text bg-secondary w-50 d-flex justify-content-end">Restante</span>
                                                <input type="number" class="form-control" wire:model="aReceber"
                                                    name="aReceber" readonly>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row text-center">
                                    <div class="col">
                                        <button class="btn btn-outline-secondary" wire:click="clearMethods"
                                            @if (empty($formasSelecionadas)) disabled @endif>Limpar</button>
                                        <button class="btn btn-outline-success"
                                            @if ($valorPago < $valorTotal || $troco < 0) disabled @endif
                                            type="submit">Finalizar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @component('components.modal', [
        'modalId' => 'PaymentModal',
        'modalTitle' => 'Recebimento',
        'sizeModal' => 'modal-md',
    ])
        <div class="row">
            <div class="col">
                <input class="form-control-plaintext text-center text-bold" type="text" wire:model="selectedForma">
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text bg-secondary w-50 d-flex justify-content-end">Valor Recebimento</span>
                    <input type="number" class="form-control" wire:model="valorRecebimento">
                </div>
            </div>
        </div>

        <div class="row text-center mt-2">
            <div class="col">
                <button class="btn btn-outline-success" wire:click="updateValueReceived">Salvar</button>
            </div>
        </div>
    @endcomponent

    @component('components.modal', [
        'modalId' => 'AddProdModal',
        'modalTitle' => 'Adicionar Produtos',
        'sizeModal' => 'modal-lg',
    ])
        @component('components.dataTable', [
            'responsive' => [
                [
                    'responsivePriority' => 1,
                    'targets' => 0,
                ],
                [
                    'responsivePriority' => 2,
                    'targets' => 1,
                ],
                [
                    'responsivePriority' => 3,
                    'targets' => 2,
                ],
            ],
            'searching' => false,
            'lengthChange' => false,
        ])
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Produto</th>
                    <th>Unitário</th>
                </tr>
            </thead>

            <tbody class="product-table-body">
            </tbody>
        @endcomponent
    @endcomponent

    @component('components.modal', [
        'modalId' => 'AddProdModal',
        'modalTitle' => 'Adicionar Produtos',
        'sizeModal' => 'modal-lg',
    ])
        @component('components.dataTable', [
            'responsive' => [
                [
                    'responsivePriority' => 1,
                    'targets' => 0,
                ],
                [
                    'responsivePriority' => 2,
                    'targets' => 1,
                ],
                [
                    'responsivePriority' => 3,
                    'targets' => 2,
                ],
            ],
            'searching' => false,
            'lengthChange' => false,
        ])
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Produto</th>
                    <th>Unitário</th>
                </tr>
            </thead>

            <tbody class="product-table-body">
            </tbody>
        @endcomponent
    @endcomponent

    @component('components.modal', [
        'modalId' => 'ProdModal',
        'modalTitle' => 'Adicionar Produtos',
        'sizeModal' => 'modal-lg',
    ])
        @component('components.dataTable', [
            'responsive' => [
                [
                    'responsivePriority' => 1,
                    'targets' => 0,
                ],
                [
                    'responsivePriority' => 2,
                    'targets' => 1,
                ],
                [
                    'responsivePriority' => 3,
                    'targets' => 2,
                ],
            ],
            'searching' => false,
            'lengthChange' => false
        ])
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Produto</th>
                    <th>Unitário</th>
                </tr>
            </thead>

            <tbody>
                @foreach (json_decode($products) as $product)
                    <tr wire:dblclick="selectProd('{{ $product->produto }}', '{{ $product->codigo }}', {{ $product->precovenda }})">
                        <td>{{ $product->codigo }}</td>
                        <td>{{ $product->produto }}</td>
                        <td>{{ number_format($product->precovenda, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        @endcomponent
    @endcomponent

    @component('components.modal', [
        'modalId' => 'ClientModal',
        'modalTitle' => 'Adicionar Cliente',
        'sizeModal' => 'modal-lg',
    ])
        @component('components.dataTable', [
            'responsive' => [
                [
                    'responsivePriority' => 1,
                    'targets' => 0,
                ],
                [
                    'responsivePriority' => 2,
                    'targets' => 1,
                ],
                [
                    'responsivePriority' => 3,
                    'targets' => 2,
                ],
                [
                    'responsivePriority' => 4,
                    'targets' => 3,
                ],
            ],
            'searching' => false,
            'lengthChange' => false
        ])
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>CPF/CNPJ</th>
                    <th>Endereço</th>
                </tr>
            </thead>

            <tbody>
                @foreach(json_decode($customers) as $client)
                    <tr wire:dblclick="selectClient('{{ $client->codigo }}', '{{ $client->nome }}')">
                        <td>{{ $client->codigo }}</td>
                        <td>{{ $client->nome }}</td>
                        <td>{{ $client->cpf_cnpj }}</td>
                        <td>{{ $client->nome }}</td>
                    </tr>
                @endforeach
            </tbody>
        @endcomponent
    @endcomponent
</div>

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .table-responsive-container {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

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
                        accordion.classList.add('invalid-accordion');
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
@endsection

<script>
    document.addEventListener('keydown', function(event) {
        if (event.key === 'F1' || event.keyCode === 112) {
            event.preventDefault();
            Livewire.emit('searchCustomers');
        } else if (event.key === 'F2' || event.keyCode === 113) {
            event.preventDefault();
            Livewire.emit('searchProducts');
        } else if (event.key === 'F4' || event.keyCode === 115) {
            event.preventDefault();
            Livewire.emit('clearItems');
        }
    });

    document.addEventListener('livewire:load', function() {
        Livewire.on('OpenAddProdModal', function(data) {
            if (data.length > 0) {
                $('#AddProdModal').modal('show');

                const tbody = document.querySelector('.product-table-body');
                tbody.innerHTML = '';

                data.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.addEventListener('dblclick', function() {
                        Livewire.emit('selectProd', item.produto, item.codigo, item
                            .precovenda);
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

        Livewire.on('OpenProductsModal', function() {
            $('#ProdModal').modal('show');
        });

        Livewire.on('OpenCustomersModal', function() {
            $('#ClientModal').modal('show');
        });

        Livewire.on('CloseAddProdModal', function() {
            $('#AddProdModal').modal('hide');
            $('#ProdModal').modal('hide');
        });

        Livewire.on('OpenPaymentModal', function() {
            $('#PaymentModal').modal('show');
        });

        Livewire.on('ClosePaymentModal', function() {
            $('#PaymentModal').modal('hide');
        });

        Livewire.on('ShowPaymentArea', function() {
            document.getElementById('addPaymentMethod').scrollIntoView({
                behavior: 'smooth'
            });
        });

        Livewire.on('ErrorInPayment', function($message) {
            alert($message)
        });
    });
</script>
