@push('css')
<style>
    /* Estilos do Padrão Visual Definido */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    :root {
        --primary-color: #00033a;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --text-dark: #343a40;
        --text-light: #6c757d;
        --success-color: #28a745;
        --danger-color: #dc3545;
    }
    .card-main {
        background: var(--card-bg);
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px var(--shadow-color);
        padding: 30px;
    }
    .custom-btn {
        font-weight: 500;
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .custom-btn:hover { transform: translateY(-2px); }
    .custom-btn-success { background-color: var(--success-color) !important; border-color: var(--success-color) !important; color: #fff !important; }
    .custom-btn-primary { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; color: #fff !important; }
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: .5rem;
    }
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        height: 48px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 3, 58, .25);
    }
    textarea.form-control { height: auto; }
    .table thead th {
        color: var(--text-dark) !important;
        font-weight: 600;
        border-bottom: 2px solid var(--border-color) !important;
        text-transform: uppercase;
    }
    .table tbody tr:hover { background-color: #f1f1f1 !important; }
    .action-buttons a { color: var(--text-light); font-size: 1.2rem; }
    .action-buttons a:hover.text-danger { color: var(--danger-color) !important; }
    .action-buttons a:hover.text-warning { color: #ffc107 !important; }
    .modal-content { border-radius: 15px !important; border: none !important; }
    .modal-header { border-bottom: 1px solid var(--border-color) !important; }
</style>
@endpush

<div>
    <form method="POST" action="{{ route('salvar_venda') }}" enctype="multipart/form-data">
        @csrf
        <div class="card card-main">
            <div class="card-body">
                
                <h4 class="mb-3 font-weight-bold border-bottom pb-2">Cabeçalho da Nota</h4>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Empresa</label>
                        <select wire:change="atualizarArrays" class="form-control" name="empresa" wire:model="empresa" required>
                            <option value="" disabled selected>-- Escolha uma empresa --</option>
                            @foreach (json_decode($empresas) as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->fantasia }} | {{ $emp->cpf_cnpj }}</option>
                            @endforeach
                        </select>
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
                    <div class="col-md-7 mb-3">
                        <label class="form-label">Cliente</label>
                        <div class="input-group">
                            <select class="form-control" name="cliente" wire:model="cliente" required>
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
                            <button type="button" class="btn btn-outline-secondary" wire:click.prevent="$emit('abrirModalClientes')"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label">CFOP</label>
                        <div class="input-group">
                            <input wire:change="buscaCfop" class="form-control" wire:model="bcfop" style="max-width: 100px;">
                            <select wire:change="atualizarBCfop" required class="form-control" name="cfop" wire:model="cfop">
                                <option value='' disabled selected>-- Selecione --</option>
                                 @foreach (json_decode($cfops) as $cfop)
                                    <option value="{{ $cfop->id }}">{{ $cfop->cfop }} | {{ $cfop->natureza }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <h4 class="mb-3 font-weight-bold border-bottom pb-2">Adicionar Itens</h4>
                <div class="card shadow-sm mb-4">
                    <div class="card-body bg-light">
                        <div class="row align-items-end">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Produto</label>
                                <div class="input-group">
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
                                    <button type="button" class="btn btn-outline-secondary" wire:click.prevent="$emit('abrirModalProdutos')"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                            <div class="col-md-2 col-6 mb-3"><label class="form-label">Qtd.</label><input class="form-control" type="number" min="1" wire:change="atualizarTot" wire:model="quantidade"></div>
                            <div class="col-md-2 col-6 mb-3"><label class="form-label">Valor Unit.</label><input class="form-control" type="number" step="0.01" wire:change="atualizarTot" wire:model="preco"></div>
                            <div class="col-md-2 col-6 mb-3"><label class="form-label">Dsct. (R$)</label><input class="form-control" type="number" step="0.1" wire:change="atualizarTot" wire:model="desconto"></div>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-md-4 mb-3"><label class="form-label">Total do Item</label><input class="form-control font-weight-bold" type="text" value="R$ {{ number_format($total, 2, ',', '.') }}" readonly></div>
                            <div class="col-md-4 mb-3">
                                <button type="button" wire:click.prevent="salvarProd" class="btn custom-btn custom-btn-primary w-100">+ Adicionar Item</button>
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="mb-3 font-weight-bold border-bottom pb-2">Itens da Nota</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light"><tr class="text-center"><th>#</th><th class="text-left">Produto</th><th>Qtd.</th><th>Unitário</th><th>Desconto</th><th>Total</th><th>Ações</th></tr></thead>
                        <tbody class="text-center">
                            @forelse ($vendaItens as $item)
                                <tr>
                                    <td>{{ $item['produto_id'] }}</td>
                                    <td class="text-left">{{ $item['descricao'] }}</td>
                                    <td>{{ $item['quantidade'] }}</td>
                                    <td>R$ {{ number_format($item['unitario'], 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($item['desconto'], 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($item['total'], 2, ',', '.') }}</td>
                                    <td class="action-buttons">
                                        <a href="#" wire:click.prevent="editItem({{ array_search($item, $vendaItens, true) }})" title="Editar Item" class="text-warning"><i class="far fa-edit"></i></a>
                                        <a href="#" wire:click.prevent="removerProduto({{ array_search($item, $vendaItens, true) }})" title="Remover Item" class="text-danger"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted">Nenhum item adicionado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="text-right mt-3"><h5>Soma dos Produtos: <b class="text-dark">R$ {{ number_format($subtotal, 2, ',', '.') }}</b></h5></div>
                
                @foreach ($vendaItens as $index => $vendaItem)
                    <input type="hidden" name="vendaItens[{{ $index }}][produto_id]" wire:model="vendaItens.{{ $index }}.produto_id">
                    <input type="hidden" name="vendaItens[{{ $index }}][quantidade]" wire:model="vendaItens.{{ $index }}.quantidade">
                    <input type="hidden" name="vendaItens[{{ $index }}][unitario]" wire:model="vendaItens.{{ $index }}.unitario">
                    <input type="hidden" name="vendaItens[{{ $index }}][desconto]" wire:model="vendaItens.{{ $index }}.desconto">
                    <input type="hidden" name="vendaItens[{{ $index }}][total]" wire:model="vendaItens.{{ $index }}.total">
                @endforeach

                <hr class="my-4">
                <h4 class="mb-3 font-weight-bold border-bottom pb-2">Informações Adicionais</h4>
                <div class="row mb-3" id="ref_nfe_section" style="display: none" wire:ignore>
                    <div class="col-md-12"><label for="ref_nfe" class="form-label">Referência NFe</label><input type="text" class="form-control" name="ref_nfe" id="ref_nfe" minlength="44" value="{{ old('ref_nfe') }}"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12"><label for="info_complementares" class="form-label">Informações Complementares (Opcional)</label><textarea class="form-control" name="info_complementares" maxlength="1500" id="info_complementares" rows="4">{{ old('info_complementares') }}</textarea></div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-lg custom-btn custom-btn-success"><i class="fas fa-save mr-2"></i>Salvar Venda</button>
                </div>
            </div>
        </div>
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
        // Listener para a seção de NFe de devolução
        Livewire.on('section_nfe', function(value) {
            let section = document.getElementById('ref_nfe_section');
            let section2 = document.getElementById('tp_nfe_section');
            if (value == 4) {
                section.style.display = 'block';
                section2.style.display = 'block';
            } else {
                section.style.display = 'none';
                section2.style.display = 'none';
            }
        });

        // Listeners para abrir e fechar os modais
        window.livewire.on('abrirModalClientes', () => {
            $('#modalClientes').modal('show');
        });
        window.livewire.on('fecharModalClientes', () => {
            $('#modalClientes').modal('hide');
        });
        window.livewire.on('abrirModalProdutos', () => {
            $('#modalProdutos').modal('show');
        });
        window.livewire.on('fecharModalProdutos', () => {
            $('#modalProdutos').modal('hide');
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