@push('css')
<style>
    /* Estilos específicos desta tela (o restante vem do tema global) */
    textarea.form-control { height: auto; }
</style>
@endpush

<div>
    <form method="POST" action="{{ route('salvar_venda') }}" enctype="multipart/form-data">
        @csrf
        <div class="card card-main">
            <div class="card-body">
                
                <h4 class="mb-3 font-weight-bold border-bottom pb-2">Cabeçalho da Nota</h4>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Confira os campos destacados abaixo antes de salvar.</strong>
                    </div>
                @endif
                <div class="row">
                    <div class="col-12 col-md-8 mb-3">
                        <label class="form-label">Empresa</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                            </div>
                            <select wire:change="atualizarArrays" class="form-control @error('empresa') is-invalid @enderror" name="empresa" wire:model="empresa" required>
                                <option value="" disabled selected>-- Escolha uma empresa --</option>
                                @foreach (json_decode($empresas) as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->fantasia }} | {{ $emp->cpf_cnpj }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('empresa')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <label class="form-label">Finalidade</label>
                        <select class="form-control" name="finalidade" wire:model="finalidade" wire:change="refNFeSection" required>
                            <option value="1">Venda</option>
                            <option value="4">Devolução</option>
                            <option value="0">Compra</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-6 mb-3" id="tp_nfe_section" style="display: none" wire:ignore>
                        <label class="form-label">Tipo</label>
                        <select class="form-control" name="tipo" wire:model="tipo" required>
                            <option value="1">Saída</option>
                            <option value="0">Entrada</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-7 mb-3">
                        <label class="form-label">Cliente</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <select class="form-control @error('cliente') is-invalid @enderror" name="cliente" wire:model="cliente" required>
                                <option value="" disabled selected>-- Escolha um cliente --</option>
                                @if ($empresaL != 1)
                                    @foreach (json_decode($clientes) as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nome }} | {{ $cliente->cpf_cnpj }}</option>
                                    @endforeach
                                @else
                                    @foreach ($clientes as $cli)
                                        <option value="{{ $cli->id }}">{{ $cli->nome }} | {{ $cli->cpf_cnpj }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" wire:click.prevent="$emit('abrirModalClientes')"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                        @error('cliente')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-12 col-md-5 mb-3">
                        <label class="form-label">CFOP</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                            </div>
                            <input wire:change="buscaCfop" class="form-control" wire:model="bcfop" style="max-width: 100px;">
                            <select wire:change="atualizarBCfop" required class="form-control @error('cfop') is-invalid @enderror" name="cfop" wire:model="cfop">
                                <option value='' disabled selected>-- Selecione --</option>
                                 @foreach (json_decode($cfops) as $cfop)
                                    <option value="{{ $cfop->id }}">{{ $cfop->cfop }} | {{ $cfop->natureza }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('cfop')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-group mb-0">
                            <label for="aut_xml">CPF/CNPJ autorizado para XML</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                </div>
                                <input
                                    type="text"
                                    name="aut_xml"
                                    id="aut_xml"
                                    class="form-control @error('aut_xml') is-invalid @enderror"
                                    placeholder="CPF ou CNPJ autorizado"
                                    maxlength="18"
                                    value="{{ old('aut_xml') }}"
                                >
                            </div>
                            @error('aut_xml')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                            <small class="text-muted">
                                Informe somente se desejar autorizar terceiro a baixar o XML.
                            </small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <h4 class="mb-3 font-weight-bold border-bottom pb-2">Adicionar Itens</h4>
                <div class="card shadow-sm mb-4">
                    <div class="card-body bg-light">
                        <div class="row align-items-end">
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label">Produto</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-box"></i></span>
                                    </div>
                                    <select wire:change="atualizarProds" class="form-control" wire:model="produto">
                                        <option value="" disabled selected>-- Escolha um produto --</option>
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
                                        <button type="button" class="btn btn-outline-secondary" wire:click.prevent="$emit('abrirModalProdutos')"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-md-2 mb-3"><label class="form-label">Qtd.</label><input class="form-control" type="number" min="1" wire:change="atualizarTot" wire:model="quantidade"></div>
                            <div class="col-4 col-md-2 mb-3"><label class="form-label">Valor Unit.</label><input class="form-control" type="number" step="0.01" wire:change="atualizarTot" wire:model="preco"></div>
                            <div class="col-4 col-md-2 mb-3"><label class="form-label">Dsct. (R$)</label><input class="form-control" type="number" step="0.1" wire:change="atualizarTot" wire:model="desconto"></div>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-12 col-md-4 mb-3"><label class="form-label">Total do Item</label><input class="form-control font-weight-bold" type="text" value="R$ {{ number_format($total, 2, ',', '.') }}" readonly></div>
                            <div class="col-12 col-md-4 mb-3">
                                <button type="button" wire:click.prevent="salvarProd" class="btn custom-btn custom-btn-primary w-100">+ Adicionar Item</button>
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="mb-3 font-weight-bold border-bottom pb-2">Itens da Nota</h4>
                @if ((int) $finalidade === 4 && $referenciaItemHabilitada)
                    <div class="alert alert-info">
                        Para cada item devolvido, informe a chave da NF-e de origem e o número correspondente no documento original.
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light"><tr class="text-center"><th>#</th><th class="text-left">Produto</th><th>Qtd.</th><th>Unitário</th><th>Desconto</th><th>Total</th>@if ((int) $finalidade === 4 && $referenciaItemHabilitada)<th style="min-width: 360px;">Chave NF-e de origem</th><th style="min-width: 120px;">Item origem</th>@endif<th>Ações</th></tr></thead>
                        <tbody class="text-center">
                            @forelse ($vendaItens as $index => $item)
                                <tr>
                                    <td>{{ $item['produto_id'] }}</td>
                                    <td class="text-left">
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
                                        <td>R$ {{ number_format($item['unitario'], 2, ',', '.') }}</td>
                                    @endif
                                    <td>R$ {{ number_format($item['desconto'], 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($item['total'], 2, ',', '.') }}</td>
                                    @if ((int) $finalidade === 4 && $referenciaItemHabilitada)
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
                                    <td class="action-buttons">
                                        @if ($itemEditando !== null && (int) $itemEditando === $index)
                                            <span class="d-none d-md-inline-flex">
                                                <a href="#" wire:click.prevent="salvarEdicaoItem" title="Salvar alteração" class="text-success"><i class="fa fa-check"></i></a>
                                                <a href="#" wire:click.prevent="cancelarEdicaoItem" title="Cancelar" class="text-secondary"><i class="fa fa-times"></i></a>
                                            </span>
                                            <div class="mobile-actions d-md-none">
                                                <a href="#" wire:click.prevent="salvarEdicaoItem" class="btn btn-sm btn-outline-success"><i class="fa fa-check"></i> Salvar</a>
                                                <a href="#" wire:click.prevent="cancelarEdicaoItem" class="btn btn-sm btn-outline-secondary"><i class="fa fa-times"></i> Cancelar</a>
                                            </div>
                                        @else
                                            <span class="d-none d-md-inline-flex">
                                                <a href="#" wire:click.prevent="editarItem({{ $index }})" title="Editar quantidade e valor" class="text-primary"><i class="fa fa-pen"></i></a>
                                                <a href="#" wire:click.prevent="abrirFiscalItem({{ $index }})" title="Dados fiscais (CFOP, CST, ICMS, ST, PIS/COFINS)" class="text-info"><i class="fas fa-file-invoice-dollar"></i></a>
                                                <a href="#" wire:click.prevent="removerProduto({{ $index }})" title="Remover Item" class="text-danger"><i class="fa fa-trash"></i></a>
                                            </span>
                                            <div class="mobile-actions d-md-none">
                                                <a href="#" wire:click.prevent="editarItem({{ $index }})" class="btn btn-sm btn-outline-primary"><i class="fa fa-pen"></i> Editar</a>
                                                <a href="#" wire:click.prevent="abrirFiscalItem({{ $index }})" class="btn btn-sm btn-outline-info"><i class="fas fa-file-invoice-dollar"></i> Fiscal</a>
                                                <a href="#" wire:click.prevent="removerProduto({{ $index }})" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i> Remover</a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ (int) $finalidade === 4 && $referenciaItemHabilitada ? 9 : 7 }}" class="text-center text-muted">Nenhum item adicionado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @error('vendaItens')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror

                @foreach ($vendaItens as $index => $vendaItem)
                    <input type="hidden" name="vendaItens[{{ $index }}][produto_id]" wire:model="vendaItens.{{ $index }}.produto_id">
                    <input type="hidden" name="vendaItens[{{ $index }}][quantidade]" wire:model="vendaItens.{{ $index }}.quantidade">
                    <input type="hidden" name="vendaItens[{{ $index }}][unitario]" wire:model="vendaItens.{{ $index }}.unitario">
                    <input type="hidden" name="vendaItens[{{ $index }}][desconto]" wire:model="vendaItens.{{ $index }}.desconto">
                    <input type="hidden" name="vendaItens[{{ $index }}][total]" wire:model="vendaItens.{{ $index }}.total">
                @endforeach

                <div class="order-summary-bar">
                    <div class="order-summary-info">
                        <span class="order-summary-count">{{ count($vendaItens) }} {{ count($vendaItens) == 1 ? 'item' : 'itens' }}</span>
                        <span class="order-summary-total">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                    </div>
                    <button type="submit" class="btn btn-lg custom-btn custom-btn-success" @if ($itemEditando !== null) disabled title="Conclua a edição do item antes de salvar" @endif>
                        <i class="fas fa-save mr-2"></i>Salvar Venda
                    </button>
                </div>

                <hr class="my-4">
                <h4 class="mb-3 font-weight-bold border-bottom pb-2">Informações Adicionais</h4>
                @if (!$referenciaItemHabilitada)
                    <div class="row mb-3" id="ref_nfe_section" style="display: none" wire:ignore>
                        <div class="col-12"><label for="ref_nfe" class="form-label">Referência NFe</label><input type="text" class="form-control" name="ref_nfe" id="ref_nfe" minlength="44" value="{{ old('ref_nfe') }}"></div>
                    </div>
                @endif
                <div class="row mb-3">
                    <div class="col-12"><label for="info_complementares" class="form-label">Informações Complementares (Opcional)</label><textarea class="form-control" name="info_complementares" maxlength="1500" id="info_complementares" rows="4">{{ old('info_complementares') }}</textarea></div>
                </div>
            </div>
        </div>

        @include('livewire.partials.modal-fiscal-item')
    </form>

    <div wire:ignore.self class="modal fade" id="modalClientes" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Buscar Cliente</h5><button type="button" class="close" data-dismiss="modal" wire:click="$emit('fecharModalClientes')"><span>&times;</span></button></div>
            <div class="modal-body">
                <input type="text" class="form-control mb-3" placeholder="Nome ou CPF/CNPJ" wire:model.debounce.500ms="buscaCliente">
                <table class="table table-hover"><thead><tr><th>Nome</th><th>CPF/CNPJ</th><th></th></tr></thead><tbody>
                    @foreach ($clientesModal as $cliente)
                        <tr>
                            <td>{{ is_object($cliente) ? $cliente->nome : $cliente['nome'] }}</td>
                            <td>{{ is_object($cliente) ? $cliente->cpf_cnpj : $cliente['cpf_cnpj'] }}</td>
                            <td><button type="button" class="btn btn-sm btn-primary" wire:click="selecionarCliente({{ is_object($cliente) ? $cliente->id : $cliente['id'] }})">Selecionar</button></td>
                        </tr>
                    @endforeach
                </tbody></table>
            </div>
        </div></div>
    </div>

    <div wire:ignore.self class="modal fade" id="modalProdutos" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Buscar Produto</h5><button type="button" class="close" data-dismiss="modal" wire:click="$emit('fecharModalProdutos')"><span>&times;</span></button></div>
            <div class="modal-body">
                <input type="text" class="form-control mb-3" placeholder="Nome do produto" wire:model.debounce.500ms="buscaProduto">
                <table class="table table-hover"><thead><tr><th>Produto</th><th>Valor</th></tr></thead><tbody>
                    @foreach ($produtosModal as $produto)
                        <tr style="cursor:pointer" ondblclick="Livewire.emit('selecionarProduto', {{ is_object($produto) ? $produto->id : $produto['id'] }})">
                            <td>{{ is_object($produto) ? $produto->produto : $produto['produto'] }}</td>
                            <td>R$ {{ number_format(is_object($produto) ? $produto->precovenda : $produto['precovenda'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody></table>
            </div>
        </div></div>
    </div>
</div>

@push('js')
<script>
    // Mantendo 100% da sua lógica JS original
    document.addEventListener('livewire:load', function() {
        // Listener para o tipo/referência da NF-e de devolução
        Livewire.on('section_nfe', function(value) {
            let section = document.getElementById('ref_nfe_section');
            let section2 = document.getElementById('tp_nfe_section');
            if (value == 4) {
                if (section) section.style.display = 'block';
                section2.style.display = 'block';
            } else {
                if (section) section.style.display = 'none';
                section2.style.display = 'none';
            }
        });

        // Listeners para abrir os modais (via $emit, canal de eventos do Livewire)
        window.livewire.on('abrirModalClientes', () => {
            $('#modalClientes').modal('show');
        });
        window.livewire.on('abrirModalProdutos', () => {
            $('#modalProdutos').modal('show');
        });

        // Listeners para fechar os modais (via dispatchBrowserEvent, evento nativo do navegador)
        window.addEventListener('fecharModalClientes', () => {
            $('#modalClientes').modal('hide');
        });
        window.addEventListener('fecharModalProdutos', () => {
            $('#modalProdutos').modal('hide');
        });

        // Modal de dados fiscais do item
        window.addEventListener('abrirModalFiscal', () => {
            $('#modalFiscalItem').modal('show');
        });
        window.addEventListener('fecharModalFiscal', () => {
            $('#modalFiscalItem').modal('hide');
        });
    });

    // Previne que a tecla Enter submeta o formulário
    $(document).ready(function() {
        $(window).keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    });
</script>
@endpush
