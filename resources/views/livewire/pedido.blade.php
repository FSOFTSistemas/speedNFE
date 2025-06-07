<div>
    <form method="POST" action="{{ route('salvar_venda') }}" enctype="multipart/form-data">
        @csrf

        <div class="row" style="text-align: center">
            <div class="col">
                <h5>Cabeçalho</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-6">
                <label>Empresa</label>
                <select wire:change="atualizarArrays()" class="form-control" name="empresa" id="empresa"
                    wire:model="empresa" required>
                    <option value="" disabled selected>--Escolha uma empresa--</option>

                    @foreach (json_decode($empresas) as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->fantasia }} | {{ $emp->cpf_cnpj }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 col-3">
                <label>Finalidade</label>
                <select class="form-control" name="finalidade" id="finalidade" wire:model="finalidade"
                    wire:change="refNFeSection" required>
                    <option value="1">Venda</option>
                    <option value="4">Devolução</option>
                    <option value="0">Compra</option>
                </select>
            </div>

            <div class="col-md-2 col-3" id="tp_nfe_section" style="display: none" wire:ignore>
                <label>Tipo</label>
                <select class="form-control" name="tipo" id="tipo" wire:model="tipo" required>
                    <option value="1">Saída</option>
                    <option value="0">Entrada</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-xs-12">
                <label>Cliente</label>
                <div class="input-group">
                    <select class="form-control" name="cliente" id="cliente" wire:model="cliente" required>
                        <option value="" disabled selected>--Escolha um cliente--</option>
                        @if ($empresaL != 1)
                            @foreach (json_decode($clientes) as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nome }} | {{ $cliente->cpf_cnpj }}
                                </option>
                            @endforeach
                        @else
                            @foreach ($clientes as $cli)
                                <option value="{{ $cli->id }}">{{ $cli->nome }} | {{ $cli->cpf_cnpj }}</option>
                            @endforeach
                        @endif
                    </select>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary"
                            wire:click="$emit('abrirModalClientes')">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-1 col-xs-6">
                <label>CFOP</label>
                <input wire:change="buscaCfop()" class="form-control" wire:model="bcfop">
            </div>
            <div class="col-md-3 col-xs-6">
                <label>Descrição CFOP</label>
                <select wire:change="atualizarBCfop()" required class="form-control" name="cfop" id="cfop"
                    wire:model="cfop">
                    <option value='' disabled selected>--Selecione o CFOP da Nota--</option>
                    @if ($empresaL != 1)
                        @foreach (json_decode($cfops) as $cfop)
                            <option value="{{ $cfop->id }}">{{ $cfop->cfop }} | {{ $cfop->natureza }}
                            </option>
                        @endforeach
                    @else
                        @foreach (json_decode($cfops) as $cfop)
                            <option value="{{ $cfop->id }}">{{ $cfop->cfop }} | {{ $cfop->natureza }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <br>
        <hr color="black">


        <div class="row" style="text-align: center">
            <div class="col">
                <h5>Itens</h5>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="input-group">
                                <select wire:change="atualizarProds()" class="form-control" wire:model="produto">
                                    <option value="" disabled selected>--Escolha um produto--</option>
                                    @if ($empresaL != 1)
                                        @foreach (json_decode($produtos) as $produto)
                                            <option value="{{ $produto->id }}">{{ $produto->produto }}</option>
                                        @endforeach
                                    @else
                                        @foreach ($produtos as $produto)
                                            <option value="{{ $produto->id }}">{{ $produto->produto }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary"
                                        wire:click="$emit('abrirModalProdutos')">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-2 col-xs-2">
                                <label>Qtd.</label>
                                <input class="form-control" type="number" min="1" wire:change="atualizarTot()"
                                    wire:model="quantidade">
                            </div>

                            <div class="col-md-4 col-xs-3">
                                <label>Valor</label>
                                <input class="form-control" type="number" step="0.01" wire:change="atualizarTot()"
                                    wire:model="preco">
                            </div>

                            <div class="col-md-2 col-xs-2">
                                <label>Dsct. (R$)</label>
                                <input class="form-control" type="number" step="0.1" wire:change="atualizarTot()"
                                    wire:model="desconto">
                            </div>

                            <div class="col-md-4 col-xs-3">
                                <label>Total</label>
                                <input class="form-control" type="number" step="0.01" wire:model="total">
                            </div>

                        </div>
                        <div class="row" style="text-align: center; margin-top: 2%;">
                            <div class="col">
                                <button wire:click.prevent="salvarProd()" class="btn btn-primary"
                                    style="width: 25%;">+
                                    Adicionar</button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="overflow-auto" style="max-height: 30%">
                            <table class="table table-hover">
                                <thead class="table-primary" style="text-align: center">
                                    <tr>
                                        <th>Id</th>
                                        <th>Produto</th>
                                        <th>Quantidade</th>
                                        <th>Unitário</th>
                                        <th>Desconto</th>
                                        <th>Total</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody style="text-align: center">
                                    @foreach ($vendaItens as $item)
                                        <tr>
                                            <td>#{{ $item['produto_id'] }}</td>
                                            <td>{{ $item['descricao'] }}</td>
                                            <td>{{ $item['quantidade'] }}</td>
                                            <td>R$ {{ number_format($item['unitario'], 2) }}</td>
                                            <td>R$ {{ number_format($item['desconto'], 2) }}</td>
                                            <td>R$ {{ number_format($item['total'], 2) }}</td>
                                            <td><a wire:click.prevent="removerProduto({{ array_search($item, $vendaItens, true) }})"
                                                    title="Remover Item" class="text-danger"><i
                                                        class="fa fa-trash"></i></a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-5 col-xs-6" style="text-align: center">
                                Soma produtos:
                            </div>
                            <div class="col-md-7 col-xs-6">
                                <b>R$ {{ number_format($subtotal, 2) }}</b>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach ($vendaItens as $index => $vendaItem)
                    <input type="hidden" name="vendaItens[{{ $index }}][produto_id]"
                        wire:model="vendaItens.{{ $index }}.produto_id">
                    <input type="hidden" name="vendaItens[{{ $index }}][quantidade]"
                        wire:model="vendaItens.{{ $index }}.quantidade">
                    <input type="hidden" name="vendaItens[{{ $index }}][unitario]"
                        wire:model="vendaItens.{{ $index }}.unitario">
                    <input type="hidden" name="vendaItens[{{ $index }}][desconto]"
                        wire:model="vendaItens.{{ $index }}.desconto">
                    <input type="hidden" name="vendaItens[{{ $index }}][total]"
                        wire:model="vendaItens.{{ $index }}.total">
                @endforeach

            </div>
        </div>

        <div class="row mb-3" id="ref_nfe_section" style="display: none" wire:ignore>
            <div class="col">
                <label for="">Referência NFe</label>
                <input type="text" class="form-control" name="ref_nfe" id="ref_nfe" minlength="44"
                    value="{{ old('ref_nfe') }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="">Informações Complementares</label>
                <textarea class="form-control" name="info_complementares" maxlength="1500" id="info_complementares" cols="30"
                    rows="9" placeholder="Opicional...">{{ old('info_complementares') }}</textarea>
            </div>
        </div>

        <div class="row" style="margin-bottom: 2%; text-align: center;">
            <div class="col">
                <button type="submit" style="width: 25%;" class="btn btn-success">Salvar</a>
            </div>
        </div>
    </form>

    <div wire:ignore.self class="modal fade" id="modalClientes" tabindex="-1" role="dialog"
        aria-labelledby="modalClientesLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buscar Cliente</h5>
                    <button type="button" class="close" wire:click="$emit('fecharModalClientes')">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-3" placeholder="Nome ou CPF/CNPJ"
                        wire:model.debounce.500ms="buscaCliente">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>CPF/CNPJ</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientesModal as $cliente)
                            
                                <tr>
                                    <td>{{ is_object($cliente) ? $cliente->cpf_cnpj : $cliente['cpf_cnpj'] }}</td>
                                    <td>{{ is_object($cliente) ? $cliente->nome : $cliente['nome'] }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary"
                                            wire:click="selecionarCliente({{ is_object($cliente) ? $cliente->id : $cliente['id'] }})">
                                            Selecionar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    


    <div wire:ignore.self class="modal fade" id="modalProdutos" tabindex="-1" role="dialog"
        aria-labelledby="modalProdutosLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buscar Produto</h5>
                    <button type="button" class="close" wire:click="$emit('fecharModalProdutos')">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-3" placeholder="Nome do produto"
                        wire:model.debounce.500ms="buscaProduto">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produtosModal as $produto)
                                <tr ondblclick="Livewire.emit('selecionarProduto', {{ is_object($produto) ? $produto->id : $produto['id'] }})">
                                    <td>{{ is_object($produto) ? $produto->produto : $produto['produto'] }}</td>
                                    <td>R$ {{ number_format(is_object($produto) ? $produto->precovenda : $produto['precovenda'], 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $(window).keydown(function(event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });

        document.addEventListener('livewire:load', function() {
            Livewire.on('section_nfe', function(value) {
                let section = document.getElementById('ref_nfe_section')
                let section2 = document.getElementById('tp_nfe_section')
                if (value == 4) {
                    section.style.display = 'block'
                    section2.style.display = 'block'
                } else {
                    section.style.display = 'none'
                    section2.style.display = 'none'
                }
            });
        });
    </script>
    <script>
        window.addEventListener('abrirModalClientes', () => {
            $('#modalClientes').modal('show');
        });

        window.addEventListener('fecharModalClientes', () => {
            $('#modalClientes').modal('hide');
        });

        window.addEventListener('abrirModalProdutos', () => {
            $('#modalProdutos').modal('show');
        });

        window.addEventListener('fecharModalProdutos', () => {
            $('#modalProdutos').modal('hide');
        });
    </script>


</div>
