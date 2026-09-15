@push('css')
<style>
    /* Estilos importados para consistência */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    :root {
        --primary-color: #00033a;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --text-dark: #343a40;
        --text-light: #6c757d;
        --success-color: #28a745;
        --input-focus-border: #80bdff;
        --input-focus-shadow: rgba(0, 3, 58, .25);
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
    .custom-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .custom-btn-primary {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        color: #fff !important;
    }
    .custom-btn-primary:hover {
        background-color: #00045e !important;
        border-color: #00045e !important;
    }
    .custom-btn-success {
        background-color: var(--success-color) !important;
        border-color: var(--success-color) !important;
        color: #fff !important;
    }
    .custom-btn-success:hover {
        background-color: #218838 !important;
        border-color: #1e7e34 !important;
    }
    .custom-btn-secondary {
        background-color: var(--text-light) !important;
        border-color: var(--text-light) !important;
        color: #fff !important;
    }
    .custom-btn-secondary:hover {
        background-color: #5a6268 !important;
        border-color: #545b62 !important;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: .5rem;
    }
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        padding: 10px 15px;
        height: auto;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--input-focus-border);
        box-shadow: 0 0 0 0.2rem var(--input-focus-shadow);
    }
    
    /* Estilos customizados para o Select2 */
    .select2-container--default .select2-selection--single {
        border-radius: 8px !important;
        border: 1px solid var(--border-color) !important;
        height: calc(1.5em + .75rem + 12px) !important;
        padding: 8px 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + .75rem + 10px) !important;
    }
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--input-focus-border) !important;
        box-shadow: 0 0 0 0.2rem var(--input-focus-shadow) !important;
    }
    .select2-dropdown {
        border-radius: 8px !important;
        border: 1px solid var(--border-color) !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 0 !important;
        line-height: normal !important;
    }

    /* Estilo da lista de produtos */
    .list-group-item {
        border-radius: 8px !important;
        margin-bottom: 5px;
        border: 1px solid var(--border-color);
    }
</style>
@endpush

<div>
    <form wire:submit.prevent="salvar" class="needs-validation" novalidate>
        <div class="card card-main">
            <div class="card-body">
                {{-- SEÇÃO 1: DADOS DA NOTA --}}
                <h4 class="mb-4" style="font-weight: 600;">Dados da Nota Fiscal</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="dataEmissao" class="form-label">Data de Emissão</label>
                        <input type="date" wire:model.defer="dataEmissao" id="dataEmissao" class="form-control" required>
                        <div class="invalid-feedback">Informe a data de emissão.</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="dataEntrada" class="form-label">Data de Entrada</label>
                        <input type="date" wire:model.defer="dataEntrada" id="dataEntrada" class="form-control" required>
                         <div class="invalid-feedback">Informe a data de entrada.</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="numeroNota" class="form-label">Número da Nota</label>
                        <input type="text" wire:model.defer="numeroNota" id="numeroNota" class="form-control" required>
                        <div class="invalid-feedback">Informe o número da nota.</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fornecedor" class="form-label">Fornecedor</label>
                        <input type="text" wire:model.defer="fornecedor" id="fornecedor" class="form-control" required>
                        <div class="invalid-feedback">Informe o nome do fornecedor.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="valor" class="form-label">Valor Total</label>
                        <input type="text" wire:model.defer="valor" id="valor" class="form-control"
                               oninput="this.value = this.value.replace(/[^0-9.,]/g, '')" required>
                        <div class="invalid-feedback">Informe o valor total da nota.</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="chave" class="form-label">Chave de Acesso (Opcional)</label>
                        <input type="text" wire:model.defer="chave" id="chave" maxlength="44" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control">
                    </div>
                </div>

                <hr class="my-4">

                {{-- SEÇÃO 2: ADICIONAR PRODUTOS --}}
                <h4 class="mb-4" style="font-weight: 600;">Adicionar Produtos à Nota</h4>
                <div class="row align-items-end">
                    <div class="col-md-7 mb-3" wire:ignore>
                        <label for="produto_id" class="form-label">Produto</label>
                        <select id="produto_id" class="form-control select2-basic">
                            <option value="">Selecione um produto</option>
                            @foreach($produtos as $produto)
                                <option value="{{ $produto->id }}">{{ $produto->produto }} ({{ $produto->codigo }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="qtde" class="form-label">Quantidade</label>
                        <input type="number" wire:model.defer="qtde" id="qtde" placeholder="Qtde" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <button type="button" class="btn custom-btn custom-btn-primary btn-block" wire:click="adicionarProduto">
                            <i class="fas fa-plus mr-1"></i> Adicionar
                        </button>
                    </div>
                </div>

                {{-- SEÇÃO 3: LISTA DE PRODUTOS ADICIONADOS --}}
                @if(!empty($itens))
                    <div class="mt-4">
                        <h5 class="mb-3">Produtos na Entrada</h5>
                        <ul class="list-group">
                            @foreach($itens as $index => $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="font-weight-bold">{{ $item['produto_nome'] }}</span>
                                    </div>
                                    <div>
                                        <span class="badge badge-primary badge-pill mr-3">Qtd: {{ $item['qtde'] }}</span>
                                        <button type="button" wire:click="removerItem({{ $index }})" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                {{-- SEÇÃO 4: BOTÕES DE AÇÃO --}}
                <div class="row mt-5">
                    <div class="col-md-6 mx-auto d-flex justify-content-center">
                        <a href="{{ route('entradas.index') }}" class="btn custom-btn custom-btn-secondary mx-2">
                            <i class="fas fa-times-circle mr-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn custom-btn custom-btn-success mx-2">
                            <i class="fas fa-save mr-1"></i> Salvar Entrada
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('js')
<script>
    document.addEventListener('livewire:load', function () {
        // Inicializa o Select2
        $('#produto_id').select2({
            placeholder: "Selecione um produto",
            width: '100%'
        });

        // Ouve a mudança no Select2 e atualiza a propriedade do Livewire
        $('#produto_id').on('change', function (e) {
            @this.set('produto_id', e.target.value);
        });

        // Ouve um evento do Livewire para limpar o Select2
        Livewire.on('resetSelect2', () => {
            $('#produto_id').val(null).trigger('change');
        });
    });

    // Validação Bootstrap
    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })();
</script>
@endpush

