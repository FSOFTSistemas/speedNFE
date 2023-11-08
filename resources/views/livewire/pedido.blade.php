<div>
<div class="row" style="text-align: center;">
    <div class="col">
        <h3 class="m-0 text-black" width="100%">Pedido</h3>
    </div>
</div>
<div class="row" style="margin-top: 2%">
    <div class="col">
        <a href="{{ route('vendas.index') }}" class="btn btn-secondary">Voltar</a>
    </div>
</div>
<div class="container" style="margin-top: 3%">
    <form method="POST">
        @csrf

        <div class="content">
            <div class="container-fluid">
                <div class="col-xs-12 col-sm-12" style="width: 100%">

                    <div class="card card-primary card-outline card-tabs">

                        <div class="card-header p-0 pt-1 border-bottom-0">
                            <div class="row" style="text-align: center">
                                <div class="col">
                                    <h3>Informações do Pedido</h3>
                                </div>
                            </div>
                            <ul class="nav nav-tabs" id="tab" role="tablist" wire:ignore>
                                <li class="nav-item">
                                    <a class="nav-link active" id="home-tab" data-toggle="pill" href="#home"
                                        role="tab" aria-controls="home" aria-selected="true">Cliente</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="produtos-tab" data-toggle="pill" href="#produtos"
                                        role="tab" aria-controls="produtos" aria-selected="false">Produtos</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="pagamento-tab" data-toggle="pill" href="#pagamento"
                                        role="tab" aria-controls="pagamento" aria-selected="false">Pagamento</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                @csrf
                                <div class="tab-content" id="tabContent">

                                    <div class="tab-pane fade show active" id="home" role="tabpanel"
                                        aria-labelledby="home-tab">

                                        <label>Empresa</label>
                                        <select wire:change="atualizarArrays()" class="form-control" name="empresa"
                                            required wire:model="empresa">
                                            <option value="" disabled selected>--Escolha uma empresa--</option>
                                            @foreach (json_decode($empresas) as $emp)
                                                <option value="{{ $emp->id }}">{{ $emp->fantasia }} |
                                                    {{ $emp->cpf_cnpj }}</option>
                                            @endforeach
                                        </select>


                                        <div class="row">
                                            <div class="col-8">
                                                <label>Cliente</label>
                                                <select class="form-control" name="cliente">
                                                    <option value="" disabled selected>--Escolha um cliente--
                                                    </option>
                                                    @if ($empresaL != 1)
                                                        @foreach (json_decode($clientes) as $cliente)
                                                            <option value="{{ $cliente->id }}">{{ $cliente->nome }} |
                                                                {{ $cliente->cpf_cnpj }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        @foreach ($clientes as $cliente)
                                                            <option value="{{ $cliente->id }}">{{ $cliente->nome }} |
                                                                {{ $cliente->cpf_cnpj }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-1">
                                                <label>CFOP</label>
                                                <input wire:change="buscaCfop()" class="form-control" required
                                                    wire:model="bcfop">
                                            </div>
                                            <div class="col-3">
                                                <label>Descrição CFOP</label>
                                                <select wire:change="atualizarBCfop()" required class="form-control"
                                                    name="cfop" wire:model="cfop">
                                                    <option value='' disabled selected>--Selecione o CFOP da
                                                        Nota--
                                                    </option>
                                                    @if ($empresaL != 1)
                                                        @foreach (json_decode($cfops) as $cfop)
                                                            <option value="{{ $cfop->id }}">{{ $cfop->cfop }} |
                                                                {{ $cfop->natureza }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        @foreach ($cfops as $cfop)
                                                            <option value="{{ $cfop->id }}">{{ $cfop->cfop }} |
                                                                {{ $cfop->natureza }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row" style="text-align: center; margin-top: 3%;">
                                            <div class="col">
                                                <a id="proxCliente" class="btn btn-primary">
                                                    Próximo
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="produtos" role="tabpanel"
                                        aria-labelledby="produtos-tab">

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-2">
                                                                <label>Cód Barras</label>
                                                                <input type="text" wire:model="barras"
                                                                    wire:keydown.enter="buscaProd()"
                                                                    class="form-control">
                                                            </div>

                                                            <div class="col-5">
                                                                {{-- <label>Produto</label>
                                                                <input type="text" wire:model="produto" class="form-control"> --}}
                                                                <label>Produto</label>
                                                                <select wire:change="atualizarProds()"
                                                                    class="form-control" wire:model="produto">
                                                                    <option value="" disabled selected>--Escolha
                                                                        um
                                                                        produto--</option>
                                                                    @if ($empresaL != 1)
                                                                        @foreach (json_decode($produtos) as $produto)
                                                                            <option value="{{ $produto->id }}">
                                                                                {{ $produto->produto }}</option>
                                                                        @endforeach
                                                                    @else
                                                                        @foreach ($produtos as $produto)
                                                                            <option value="{{ $produto->id }}">
                                                                                {{ $produto->produto }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>

                                                            <div class="col-1">
                                                                <label>Qtd</label>
                                                                <input class="form-control" type="number"
                                                                    step="0.1" wire:change="atualizarTot()"
                                                                    wire:model="quantidade">
                                                            </div>

                                                            <div class="col-1">
                                                                <label>Valor</label>
                                                                <input class="form-control" type="number"
                                                                    step="0.01" wire:change="atualizarTot()"
                                                                    wire:model="preco">
                                                            </div>

                                                            <div class="col-1">
                                                                <label>Dsct. (%)</label>
                                                                <input class="form-control" type="number"
                                                                    step="0.1" wire:change="atualizarTot()"
                                                                    wire:model="desconto">
                                                            </div>

                                                            <div class="col-1">
                                                                <label>Total</label>
                                                                <input class="form-control" type="number"
                                                                    step="0.01" wire:model="total">
                                                            </div>

                                                            <div class="col-1">
                                                                <label>&nbsp;</label>
                                                                <a wire:click.prevent="salvarProd()"
                                                                    class="btn btn-success">Adicionar</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="overflow-auto" style="max-height: 30%">
                                                            <table class="table table-striped">
                                                                <thead>
                                                                    <th>Id</th>
                                                                    <th>Produto</th>
                                                                    <th>Quantidade</th>
                                                                    <th>Unitário</th>
                                                                    <th>Desconto</th>
                                                                    <th>Total</th>
                                                                    <th>Ações</th>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($vendaItens as $item)
                                                                        <tr>
                                                                            @foreach ($item as $i)
                                                                                <td>{{ $i }}</td>
                                                                            @endforeach
                                                                            <td><button
                                                                                    wire:click.prevent="removerProduto({{ array_search($item, $vendaItens, true) }})"
                                                                                    class="btn btn-danger">rmv</button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-3">
                                                                Soma produtos:
                                                            </div>
                                                            <div class="col-9">
                                                                <b><input disabled wire:model="subtotal"
                                                                        type="number" class="form-control"></b>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @foreach ($vendaItens as $index => $vendaItem)
                                                    <input type="hidden"
                                                        name="vendaItens[{{ $index }}][produto_id]"
                                                        wire:model="vendaItens.{{ $index }}.produto_id">
                                                    <input type="hidden"
                                                        name="vendaItens[{{ $index }}][quantidade]"
                                                        wire:model="vendaItens.{{ $index }}.quantidade" />
                                                    <input type="hidden"
                                                        name="vendaItens[{{ $index }}][unitario]"
                                                        wire:model="vendaItens.{{ $index }}.unitario" />
                                                    <input type="hidden"
                                                        name="vendaItens[{{ $index }}][desconto]"
                                                        wire:model="vendaItens.{{ $index }}.desconto" />
                                                    <input type="hidden"
                                                        name="vendaItens[{{ $index }}][total]"
                                                        wire:model="vendaItens.{{ $index }}.total" />
                                                @endforeach

                                            </div>
                                        </div>

                                        <div class="row" style="text-align: center; margin-top: 3%;">
                                            <div class="col">
                                                <a id="prevProdutos" class="btn btn-primary">
                                                    Anterior
                                                </a>
                                                <a id="proxProdutos" class="btn btn-primary">
                                                    Próximo
                                                </a>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="tab-pane fade" id="pagamento" role="tabpanel"
                                        aria-labelledby="pagamento-tab">

                                        <div class="row">
                                            <div class="col-9">
                                                <label>Forma de Pagamento</label>
                                                <select class="form-control" wire:model="forma">
                                                    <option>--Escolha uma forma--</option>
                                                    @if ($empresaL != 1)
                                                        @foreach ($formas as $for)
                                                            <option value="{{ $for->id }}">{{ $for->descricao }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        @foreach ($formas as $for)
                                                            <option value="{{ $for->id }}">{{ $for->descricao }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <label>Valor</label>
                                                <input class="form-control" type="number" step="0.01"
                                                    wire:model="valPag">
                                            </div>
                                            <div class="col-1">
                                                <label>&nbsp;</label>
                                                <a wire:click.prevent="salvarForma()"
                                                    class="btn btn-success">Adicionar</a>
                                            </div>
                                        </div>

                                        <table class="table table-striped">
                                            <thead>
                                                <th>Forma</th>
                                                <th>Descricao</th>
                                                <th>Valor</th>
                                                <th></th>
                                            </thead>
                                            <tbody>
                                                @foreach ($formasVenda as $formas)
                                                    <tr>
                                                        @foreach ($formas as $forma)
                                                            <td>{{ $forma }}</td>
                                                        @endforeach
                                                        <td><a wire:click.prevent="removerForma({{ array_search($formas, $formasVenda, true) }})"
                                                                class="btn btn-danger">rmv</a></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        @foreach ($formasVenda as $index => $formaVenda)
                                            <input type="hidden" name="formasVenda[{{ $index }}][forma_id]"
                                                wire:model="formasVenda.{{ $index }}.forma_id">
                                            <input type="hidden" name="formasVenda[{{ $index }}][total]"
                                                wire:model="formasVenda.{{ $index }}.total" />
                                        @endforeach

                                        <div class="row" style="text-align: center; margin-top: 3%;">
                                            <div class="col">
                                                <a id="prevPagamento" class="btn btn-primary">
                                                    Anterior
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row" style="margin-top: 3%">
                                        <div class="col">
                                            <button type="submit" class="btn btn-success form-control">Salvar</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </form>
</div>

<script>
    $('#tab a').on('click', function(e) {
        e.preventDefault()
        $(this).tab('show')
    })
    $('#proxCliente').on('click', function(e) {
        e.preventDefault()
        $('#produtos-tab').tab('show')
    })
    $('#prevProdutos').on('click', function(e) {
        e.preventDefault()
        $('#home-tab').tab('show')
    })
    $('#proxProdutos').on('click', function(e) {
        e.preventDefault()
        $('#pagamento-tab').tab('show')
    })
    $('#prevPagamento').on('click', function(e) {
        e.preventDefault()
        $('#produtos-tab').tab('show')
    })

    $(document).ready(function() {
        $(window).keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    });
</script>
</div>