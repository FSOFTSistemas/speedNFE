<div>
    <form method="POST">
        @csrf
        <div class="container">
            <div class="row" style="text-align: center">
                <div class="col">
                    <h5>Cabeçalho</h5>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-xs-12">
            <label>Empresa</label>
            <select wire:change="atualizarArrays()" class="form-control" name="empresa" wire:model="empresa" required>
                <option value="" disabled selected>--Escolha uma empresa--</option>
                @foreach (json_decode($empresas) as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->fantasia }} | {{ $emp->cpf_cnpj }}</option>
                @endforeach
            </select>
        </div>
    </div>

            <div class="row">
                <div class="col-md-8 col-xs-12">
                    <label>Cliente</label>
                    <select class="form-control" name="cliente" id="cliente" required>
                        <option value="" disabled selected>--Escolha um cliente--</option>
                        @if ($empresaL != 1)
                            @foreach (json_decode($clientes) as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nome }} | {{ $cliente->cpf_cnpj  }}
                                </option>
                            @endforeach
                        @else
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nome }} | {{ $cliente->cpf_cnpj }} | {{ $cliente->id }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-1 col-xs-6">
                    <label>CFOP</label>
                    <input wire:change="buscaCfop()" class="form-control" wire:model="bcfop">
                </div>
                <div class="col-md-3 col-xs-6">
                    <label>Descrição CFOP</label>
                    <select wire:change="atualizarBCfop()" required class="form-control" name="cfop"
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
        </div>

        <div class="container">
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
                                <div class="col-md-8 col-xs-12">
                                    {{-- <label>Produto</label>
                                <input type="text" wire:model="produto" class="form-control"> --}}
                                    <label>Produto</label>
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
                                </div>

                                <div class="col-md-1 col-xs-3">
                                    <label>Qtd.</label>
                                    <input class="form-control" type="number" min="1" wire:change="atualizarTot()"
                                        wire:model="quantidade">
                                </div>

                                <div class="col-md-1 col-xs-3">
                                    <label>Valor</label>
                                    <input class="form-control" type="number" step="0.01"
                                        wire:change="atualizarTot()" wire:model="preco">
                                </div>

                                <div class="col-md-1 col-xs-3">
                                    <label>Dsct. (%)</label>
                                    <input class="form-control" type="number" step="0.1"
                                        wire:change="atualizarTot()" wire:model="desconto">
                                </div>

                                <div class="col-md-1 col-xs-3">
                                    <label>Total</label>
                                    <input class="form-control" type="number" step="0.01" wire:model="total">
                                </div>

                            </div>
                            <div class="row" style="text-align: center; margin-top: 2%;">
                                <div class="col">
                                    <button wire:click.prevent="salvarProd()" class="btn btn-primary" style="width: 25%;">+ Adicionar</button>
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
                                                @foreach ($item as $i)
                                                    <td>{{ $i }}</td>
                                                @endforeach
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
                                    <b><input disabled wire:model="subtotal" type="number" class="form-control"></b>
                                </div>
                            </div>
                        </div>
                    </div>

                    @foreach ($vendaItens as $index => $vendaItem)
                        <input type="hidden" name="vendaItens[{{ $index }}][produto_id]"
                            wire:model="vendaItens.{{ $index }}.produto_id">
                        <input type="hidden" name="vendaItens[{{ $index }}][quantidade]"
                            wire:model="vendaItens.{{ $index }}.quantidade" />
                        <input type="hidden" name="vendaItens[{{ $index }}][unitario]"
                            wire:model="vendaItens.{{ $index }}.unitario" />
                        <input type="hidden" name="vendaItens[{{ $index }}][desconto]"
                            wire:model="vendaItens.{{ $index }}.desconto" />
                        <input type="hidden" name="vendaItens[{{ $index }}][total]"
                            wire:model="vendaItens.{{ $index }}.total" />
                    @endforeach

                </div>
            </div>
        </div>

        {{-- <div class="row">
            <div class="col-9">
                <label>Forma de Pagamento</label>
                <select class="form-control" wire:model="forma">
                    <option>--Escolha uma forma--</option>
                    @if ($empresaL != 1)
                        @foreach ($formas as $for)
                            <option value="{{ $for->id }}">{{ $for->descricao }}</option>
                        @endforeach
                    @else
                        @foreach ($formas as $for)
                            <option value="{{ $for->id }}">{{ $for->descricao }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-2">
                <label>Valor</label>
                <input class="form-control" type="number" step="0.01" wire:model="valPag">
            </div>
            <div class="col-1">
                <label>&nbsp;</label>
                <a wire:click.prevent="salvarForma()" class="btn btn-success">Adicionar</a>
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
        @endforeach --}}

        {{-- <hr color="black"> --}}

        <div class="row" style="margin-bottom: 2%;">
            <div class="col">
                <label for="">Informações Complementares</label>
                <textarea class="form-control" name="info_complementares" maxlength="1500" id="info_complementares" cols="30" rows="9" placeholder="Opicional..."></textarea>
            </div>
        </div>

        <div class="row" style="margin-bottom: 2%; text-align: center;">
            <div class="col">
                <button type="submit" style="width: 25%;" class="btn btn-success">Salvar</a>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $(window).keydown(function(event) {
                    if (event.keyCode == 13) {
                        event.preventDefault();
                        return false;
                    }
                });
            });
        </script>

    </form>
</div>
