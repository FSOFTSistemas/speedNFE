<div>
    <form wire:submit.prevent="salvar">
        <div class="row">
            <div class="col-md-4">
                <label>Data de Emissão</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text py-2"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                    <input type="date" wire:model="dataEmissao" class="form-control form-control-lg py-2" >
                </div>
                <small class="form-text text-muted">Informe a data de emissão da nota fiscal.</small>
            </div>
            <div class="col-md-4">
                <label>Data de Entrada</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text py-2"><i class="fas fa-calendar-day"></i></span>
                    </div>
                    <input type="date" wire:model="dataEntrada" class="form-control form-control-lg py-2">
                </div>
                <small class="form-text text-muted">Informe a data que os produtos deram entrada no estoque.</small>
            </div>
            <div class="col-md-4">
                <label>Número da Nota</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text py-2"><i class="fas fa-file-invoice"></i></span>
                    </div>
                    <input type="text" wire:model="numeroNota" class="form-control form-control-lg py-2" required>
                </div>
                <small class="form-text text-muted">Digite o número da nota fiscal.</small>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label>Fornecedor</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text py-2"><i class="fas fa-truck"></i></span>
                    </div>
                    <input type="text" wire:model="fornecedor" class="form-control form-control-lg py-2" required>
                </div>
                <small class="form-text text-muted">Nome do fornecedor responsável pela nota.</small>
            </div>
            <div class="col-md-6">
                <label>Valor Total</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text py-2"><i class="fas fa-dollar-sign"></i></span>
                    </div>
                    <input type="text" wire:model="valor" class="form-control form-control-lg py-2"
                        oninput="this.value = this.value.replace(/[^0-9.,]/g, '')"
                        onblur="this.value = parseFloat(this.value.replace(',', '.')).toFixed(2)" required>
                </div>
                <small class="form-text text-muted">Valor total da nota fiscal, incluindo impostos e fretes.</small>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-12">
                <label>Chave</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text py-2"><i class="fas fa-key"></i></span>
                    </div>
                    <input type="text" wire:model="chave" maxlength="44" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control form-control-lg py-2">
                </div>
                <small class="form-text text-muted">Digite a chave de acesso da nota fiscal (44 dígitos).</small>
            </div>
        </div>

        <hr class="my-4">

        <h5>Adicionar Produtos (opcional)</h5>
        <div class="row">
            <div class="col-md-8">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text py-2"><i class="fas fa-box" ></i></span>
                    </div>
                    <select wire:model="produto_id" class="form-control form-control-lg py-2">
                        <option value="">Selecione um produto</option>
                        @foreach($produtos as $produto)
                            <option value="{{ $produto->id }}">{{ $produto->produto }} ({{ $produto->codigo }})</option>
                        @endforeach
                    </select>
                </div>
                <small class="form-text text-muted">Escolha o produto a ser adicionado à nota.</small>
            </div>
            <div class="col-md-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text py-2"><i class="fas fa-sort-numeric-up"></i></span>
                    </div>
                    <input type="number" wire:model="qtde" placeholder="Qtde" class="form-control form-control-lg py-2">
                </div>
                <small class="form-text text-muted">Informe a quantidade do produto a ser lançada.</small>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary w-100" wire:click="adicionarProduto">Adicionar</button>
            </div>
        </div>

        <ul class="list-group mt-3">
            @foreach($itens as $index => $item)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $item['produto_nome'] }} - {{ $item['qtde'] }} un.
                    <button type="button" wire:click="removerItem({{ $index }})" class="btn btn-danger btn-sm">Remover</button>
                </li>
            @endforeach
        </ul>

        <div class="mt-4 d-flex justify-content-center align-items-center">
            <a href="{{ route('entradas.index') }}" class="btn btn-secondary btn-lg mx-2">
                <i class="fas fa-times-circle"></i> Cancelar
            </a>
            
            <button type="submit" class="btn btn-success btn-lg mx-2">
                <i class="fas fa-save"></i> Salvar Entrada
            </button>
        </div>
    </form>
</div>