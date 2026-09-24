<div>
    <form action="{{ route('vendas.atualizar', [$pedido->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="container">
            <div class="row" style="text-align: center">
                <div class="col">
                    <h5>Cabeçalho</h5>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>Empresa</label>
                    <select disabled class="form-control" name="empresa" wire:model="empresa">
                        <option value="" disabled selected>--Escolha uma empresa--</option>
                        @if ($empresa != '')
                            @foreach (json_decode($empresas) as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->fantasia }} | {{ $emp->cpf_cnpj }}</option>
                            @endforeach
                        @else
                            @foreach ($empresas as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->fantasia }} | {{ $emp->cpf_cnpj }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 col-xs-12">
                    <label>Cliente</label>
                    <select class="form-control" name="cliente" wire:model="cliente" required>
                        <option value="" disabled selected>--Escolha um cliente--</option>
                        @if ($empresa != '')
                            @foreach (json_decode($clientes) as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nome }} | {{ $cliente->cpf_cnpj }}
                                </option>
                            @endforeach
                        @else
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nome }} | {{ $cliente->cpf_cnpj }}
                                </option>
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
                        @if ($empresa != '')
                            @foreach (json_decode($cfops) as $cfop)
                                <option value="{{ $cfop->id }}">{{ $cfop->cfop }} | {{ $cfop->natureza }}
                                </option>
                            @endforeach
                        @else
                            @foreach ($cfops as $cfop)
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
            @if ((int) $pedido->finNF === 4 && $referenciaItemHabilitada)
                <div class="alert alert-info">
                    Para cada item devolvido, informe a chave da NF-e de origem e o número correspondente no documento original.
                </div>
            @endif
            <br>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                                    {{-- <label>Produto</label>
                                <input type="text" wire:model="produto" class="form-control"> --}}
                                    <label>Produto</label>
                                    <select wire:change="atualizarProds()" class="form-control" wire:model="produto">
                                        <option value="" disabled selected>--Escolha um produto--</option>
                                        @if ($empresa != '')
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

                                <div class="col-md-1 col-xs-1">
                                    <label>Qtd.</label>
                                    <input class="form-control" type="number" min="1"
                                        wire:change="atualizarTot()" wire:model="quantidade">
                                </div>

                                <div class="col-md-2 col-xs-2">
                                    <label>Valor</label>
                                    <input class="form-control" type="number" step="0.01"
                                        wire:change="atualizarTot()" wire:model="preco">
                                </div>

                                <div class="col-md-1 col-xs-1">
                                    <label>Dsct. (%)</label>
                                    <input class="form-control" type="number" step="0.1"
                                        wire:change="atualizarTot()" wire:model="desconto">
                                </div>

                                <div class="col-md-2 col-xs-2">
                                    <label>Total</label>
                                    <input class="form-control" type="number" step="0.01" wire:model="total">
                                </div>

                            </div>
                            <div class="row" style="text-align: center; margin-top: 2%;">
                                <div class="col">
                                    <button wire:click.prevent="salvarProd()" style="width: 25%;"
                                        class="btn btn-primary">+ Adicionar</button>
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
                                        <th>Id</th>
                                        <th>Produto</th>
                                        <th>Quantidade</th>
                                        <th>Unitário</th>
                                        <th>Desconto</th>
                                        <th>Total</th>
                                        @if ((int) $pedido->finNF === 4 && $referenciaItemHabilitada)
                                            <th style="min-width: 360px;">Chave NF-e de origem</th>
                                            <th style="min-width: 120px;">Item origem</th>
                                        @endif
                                        <th>Ações</th>
                                    </thead>
                                    <tbody style="text-align: center">
                                        @foreach ($vendaItens as $index => $item)
                                            <tr>
                                                <td>#{{ $item['produto_id'] }}</td>
                                                <td>
                                                    {{ $item['descricao'] }}
                                                    @if (!empty($item['fiscal']))
                                                        <span class="badge badge-info ml-1" title="CFOP {{ $item['fiscal']['cfop'] }} · CST/CSOSN {{ $item['fiscal']['cst_csosn'] }}">Fiscal editado</span>
                                                    @endif
                                                </td>
                                                @if ($itemEditando !== null && (int) $itemEditando === $index)
                                                    <td style="min-width: 110px;">
                                                        <input type="number" step="any" min="0" class="form-control form-control-sm text-center" wire:model.defer="editQuantidade" wire:keydown.enter.prevent="salvarEdicaoItem" wire:keydown.escape="cancelarEdicaoItem">
                                                    </td>
                                                    <td style="min-width: 130px;">
                                                        <input type="number" step="0.01" min="0" class="form-control form-control-sm text-center" wire:model.defer="editUnitario" wire:keydown.enter.prevent="salvarEdicaoItem" wire:keydown.escape="cancelarEdicaoItem">
                                                        @error('itemEdicao')
                                                            <small class="text-danger d-block text-left">{{ $message }}</small>
                                                        @enderror
                                                    </td>
                                                @else
                                                    <td>{{ $item['quantidade'] }}</td>
                                                    <td>R$ {{ number_format($item['unitario'], 2) }}</td>
                                                @endif
                                                <td>R$ {{ number_format($item['desconto'], 2) }}</td>
                                                <td>R$ {{ number_format($item['total'], 2) }}</td>
                                                @if ((int) $pedido->finNF === 4 && $referenciaItemHabilitada)
                                                    <td>
                                                        <input
                                                            type="text"
                                                            name="vendaItens[{{ $index }}][dfe_referenciado_chave]"
                                                            wire:model.defer="vendaItens.{{ $index }}.dfe_referenciado_chave"
                                                            class="form-control @error('vendaItens.'.$index.'.dfe_referenciado_chave') is-invalid @enderror"
                                                            inputmode="numeric"
                                                            maxlength="44"
                                                            placeholder="44 dígitos"
                                                            required
                                                        >
                                                        @error('vendaItens.'.$index.'.dfe_referenciado_chave')
                                                            <small class="text-danger d-block text-left">{{ $message }}</small>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="number"
                                                            name="vendaItens[{{ $index }}][dfe_referenciado_n_item]"
                                                            wire:model.defer="vendaItens.{{ $index }}.dfe_referenciado_n_item"
                                                            class="form-control @error('vendaItens.'.$index.'.dfe_referenciado_n_item') is-invalid @enderror"
                                                            min="1"
                                                            max="990"
                                                            required
                                                        >
                                                        @error('vendaItens.'.$index.'.dfe_referenciado_n_item')
                                                            <small class="text-danger d-block text-left">{{ $message }}</small>
                                                        @enderror
                                                    </td>
                                                @endif
                                                <td class="text-nowrap">
                                                    @if ($itemEditando !== null && (int) $itemEditando === $index)
                                                        <a href="#" wire:click.prevent="salvarEdicaoItem" title="Salvar alteração" class="text-success mr-2"><i class="fa fa-check"></i></a>
                                                        <a href="#" wire:click.prevent="cancelarEdicaoItem" title="Cancelar" class="text-secondary"><i class="fa fa-times"></i></a>
                                                    @else
                                                        <a href="#" wire:click.prevent="editarItem({{ $index }})" title="Editar quantidade e valor" class="text-primary mr-2"><i class="fa fa-pen"></i></a>
                                                        <a href="#" wire:click.prevent="abrirFiscalItem({{ $index }})" title="Dados fiscais (CFOP, CST, ICMS, ST, PIS/COFINS)" class="text-info mr-2"><i class="fas fa-file-invoice-dollar"></i></a>
                                                        <a href="#" wire:click.prevent="removerProduto({{ $index }})" title="Remover Item" class="text-danger"><i class="fa fa-trash"></i></a>
                                                    @endif
                                                </td>
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

        {{-- <hr color="black">

        <label>Forma de Pagamento</label>
        <select class="form-control" name="forma" wire:model="pag">
            <option>--Escolha uma forma--</option>
            @if ($empresa != '')
                @foreach (json_decode($formas) as $for)
                    <option value="{{$for->id}}">{{$for->descricao}}</option>
                @endforeach
            @else
                @foreach ($formas as $for)
                    <option value="{{$for->id}}">{{$for->descricao}}</option>
                @endforeach
            @endif
        </select>

        <hr color="black"> --}}

        <div class="row" style="margin-bottom: 2%;">
            <div class="col">
                <label for="">Informações Complementares</label>
                <textarea class="form-control" name="info_complementares" maxlength="1500" id="info_complementares" wire:model="info_complementares" cols="30" rows="9" placeholder="Opicional..."></textarea>
            </div>
        </div>

        <div class="row" style="margin-bottom: 2%; text-align: center;">
            <div class="col">
                <button type="submit" style="width: 25%;" class="btn btn-success" @if ($itemEditando !== null) disabled title="Conclua a edição do item antes de salvar" @endif>Salvar</a>
            </div>
        </div>

        @include('livewire.partials.modal-fiscal-item')

        <script>
            window.addEventListener('abrirModalFiscal', () => $('#modalFiscalItem').modal('show'));
            window.addEventListener('fecharModalFiscal', () => $('#modalFiscalItem').modal('hide'));

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
