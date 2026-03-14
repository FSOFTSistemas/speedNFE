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
    .custom-btn-success {
        background-color: var(--success-color) !important;
        border-color: var(--success-color) !important;
        color: #fff !important;
        font-weight: 500;
        border-radius: 8px;
        padding: 12px 20px;
        transition: all 0.3s ease;
    }
    .custom-btn-success:hover {
        background-color: #218838 !important;
        border-color: #1e7e34 !important;
        transform: translateY(-2px);
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
    .form-control:read-only {
        background-color: #e9ecef;
        opacity: 1;
    }
    .accordion .card {
        border-radius: 8px !important;
        border: 1px solid var(--border-color) !important;
        overflow: hidden;
    }
    .accordion .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid var(--border-color);
        padding: .75rem 1.25rem;
    }
    .accordion .btn-link {
        color: var(--text-dark);
        text-decoration: none;
        font-size: 1rem;
    }
    .accordion .btn-link:hover {
        text-decoration: none;
    }
    .select2-container--default .select2-selection--single {
        border-radius: 8px !important;
        border: 1px solid var(--border-color) !important;
        height: calc(1.5em + .75rem + 12px) !important;
        padding: 8px 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + .75rem + 10px) !important;
    }
</style>
@endpush

<div>
    <form class="needs-validation" novalidate action="{{ route('entradas.store') }}" method="POST">
        @csrf
        <div class="card card-main">
            <div class="card-body">
                <header>
                    <h4 class="mb-4 text-center" style="font-weight: 600;">{{ $emit['xFant'] }}</h4>
                    <input type="hidden" class="form-control" name="nNF" wire:model="ide.nNF" required>
                    
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label class="form-label">Natureza da Operação</label>
                            <input type="text" class="form-control" name="natOp" wire:model="ide.natOp" readonly required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Data de Emissão</label>
                            <input type="text" class="form-control" name="dhEmi" value="{{ date('d/m/Y H:i:s', strtotime($ide['dhEmi'])) }}" readonly required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="chNFe" class="form-label">Chave NFe</label>
                            <input type="text" class="form-control" id="chNFe" name="chNFe" wire:model="chNFe" readonly required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="vNF" class="form-label">Valor Total da Nota</label>
                            <input type="text" class="form-control" id="vNF" name="vNF" wire:model="vNF" readonly required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label class="form-label">Fornecedor</label>
                            <input type="text" class="form-control" name="fornecedor" wire:model="emit.xNome" readonly required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">CNPJ / CPF</label>
                            <input type="text" class="form-control" name="CNPJ" wire:model="emit.CNPJ" readonly required>
                        </div>
                    </div>
                     <input type="hidden" name="IE" wire:model="emit.IE">
                     <input type="hidden" name="fone" wire:model="emit.enderEmit.fone">
                     <input type="hidden" name="rua" wire:model="emit.enderEmit.xLgr">
                     <input type="hidden" name="nro" wire:model="emit.enderEmit.nro">
                     <input type="hidden" name="bairro" wire:model="emit.enderEmit.xBairro">
                     <input type="hidden" name="mun" wire:model="emit.enderEmit.xMun">
                     <input type="hidden" name="uf" wire:model="emit.enderEmit.UF">
                     <input type="hidden" name="CEP" wire:model="emit.enderEmit.CEP">
                     <input type="hidden" name="dhSaiEnt" value="{{ date('d/m/Y H:i:s', strtotime($ide['dhSaiEnt'] ?? now())) }}">
                </header>
                
                <hr class="my-4">

                <main>
                    <section>
                        <h4 class="mb-3" style="font-weight: 600;">Produtos da Nota</h4>
                        <div class="alert alert-warning" role="alert">
                            <strong>Atenção:</strong> Por favor, selecione a categoria de cada produto abaixo.
                        </div>
                        <div class="accordion" id="accordionProducts">
                            @foreach ($prods as $index => $item)
                                <div class="card mb-2" wire:ignore.self>
                                    <div class="card-header" id="heading{{ $index }}">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapse{{ $item[0]['cProd'] . $index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $item[0]['cProd'] . $index }}">
                                                <span class="font-weight-bold text-dark">{{ $item[0]['xProd'] }}</span>
                                                <span class="badge badge-primary">Qtd: {{ $item[0]['qCom'] }}</span>
                                                <i class="fas fa-chevron-down ml-2"><small class="text-muted ml-2">(clique para expandir)</small></i>
                                            </button>
                                        </h2>
                                    </div>

                                    <div id="collapse{{ $item[0]['cProd'] . $index }}" class="collapse @if($loop->first) show @endif" aria-labelledby="heading{{ $index }}" data-parent="#accordionProducts" wire:ignore.self>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8 mb-3">
                                                    <label class="form-label">Nome do Produto no Sistema</label>
                                                    <input type="text" class="form-control" name="prods[{{ $index }}][0][xProd]" wire:model="prods.{{ $index }}.0.xProd" oninput="this.value = this.value.toUpperCase()" required>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Categoria</label>
                                                    <div wire:ignore>
                                                        <select class="form-control select2-basic" id="categoria_{{$index}}" name="prods[{{ $index }}][0][categoria]" required>
                                                            <option value="">Selecione</option>
                                                            @foreach (json_decode($categorias) as $cat)
                                                                <option value="{{ $cat->id }}">{{ $cat->descricao }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label">Cód. Barras (EAN)</label>
                                                    <input type="text" class="form-control" name="prods[{{ $index }}][0][cEAN]" wire:model="prods.{{ $index }}.0.cEAN" required>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label">Unidade</label>
                                                    <input type="text" class="form-control" name="prods[{{ $index }}][0][uCom]" wire:model="prods.{{ $index }}.0.uCom" required>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label">Preço de Custo (UN)</label>
                                                    <input type="text" class="form-control" name="prods[{{ $index }}][0][vProd]" wire:model="prods.{{ $index }}.0.vProd" readonly>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label">Preço de Venda (UN)</label>
                                                    <input type="text" class="form-control" name="prods[{{ $index }}][0][vVendaProd]" wire:model="prods.{{ $index }}.0.vVendaProd" oninput="this.value = this.value.replace(/[^0-9.]/g, '');" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Inputs Hidden --}}
                                <input type="hidden" name="prods[{{ $index }}][0][qCom]" wire:model="prods.{{ $index }}.0.qCom">
                                <input type="hidden" name="prods[{{ $index }}][0][NCM]" wire:model="prods.{{ $index }}.0.NCM">
                                <input type="hidden" name="prods[{{ $index }}][0][CFOP]" wire:model="prods.{{ $index }}.0.CFOP">
                                <input type="hidden" name="prods[{{ $index }}][0][tpProd]" wire:model="prods.{{ $index }}.0.tpProd">
                                <input type="hidden" name="prods[{{ $index }}][0][ICMS]" wire:model="prods.{{ $index }}.0.ICMS">
                                <input type="hidden" name="prods[{{ $index }}][0][CFOP_INTERNO]" wire:model="prods.{{ $index }}.0.CFOP_INTERNO">
                                <input type="hidden" name="prods[{{ $index }}][0][CFOP_EXTERNO]" wire:model="prods.{{ $index }}.0.CFOP_EXTERNO">
                                <input type="hidden" name="prods[{{ $index }}][0][PIS]" wire:model="prods.{{ $index }}.0.PIS">
                                <input type="hidden" name="prods[{{ $index }}][0][IPI]" wire:model="prods.{{ $index }}.0.IPI">
                                <input type="hidden" name="prods[{{ $index }}][0][COFINS]" wire:model="prods.{{ $index }}.0.COFINS">
                                <input type="hidden" name="prods[{{ $index }}][0][CST]" wire:model="prods.{{ $index }}.0.CST">
                                <input type="hidden" name="prods[{{ $index }}][0][CSOSN]" wire:model="prods.{{ $index }}.0.CSOSN">
                                <input type="hidden" name="prods[{{ $index }}][0][CST_PIS]" wire:model="prods.{{ $index }}.0.CST_PIS">
                                <input type="hidden" name="prods[{{ $index }}][0][CST_COFINS]" wire:model="prods.{{ $index }}.0.CST_COFINS">
                                @if (isset($prods[$index][0]['tpProd']) && $prods[$index][0]['tpProd'] == 1)
                                    <input type="hidden" name="prods[{{ $index }}][0][tpOp]" wire:model="prods.{{ $index }}.0.tpOp">
                                    <input type="hidden" name="prods[{{ $index }}][0][chassi]" wire:model="prods.{{ $index }}.0.chassi">
                                    <input type="hidden" name="prods[{{ $index }}][0][cCor]" wire:model="prods.{{ $index }}.0.cCor">
                                    <input type="hidden" name="prods[{{ $index }}][0][xCor]" wire:model="prods.{{ $index }}.0.xCor">
                                    <input type="hidden" name="prods[{{ $index }}][0][pot]" wire:model="prods.{{ $index }}.0.pot">
                                    <input type="hidden" name="prods[{{ $index }}][0][cilin]" wire:model="prods.{{ $index }}.0.cilin">
                                    <input type="hidden" name="prods[{{ $index }}][0][pesoL]" wire:model="prods.{{ $index }}.0.pesoL">
                                    <input type="hidden" name="prods[{{ $index }}][0][pesoB]" wire:model="prods.{{ $index }}.0.pesoB">
                                    <input type="hidden" name="prods[{{ $index }}][0][nSerie]" wire:model="prods.{{ $index }}.0.nSerie">
                                    <input type="hidden" name="prods[{{ $index }}][0][tpComb]" wire:model="prods.{{ $index }}.0.tpComb">
                                    <input type="hidden" name="prods[{{ $index }}][0][nMotor]" wire:model="prods.{{ $index }}.0.nMotor">
                                    <input type="hidden" name="prods[{{ $index }}][0][CMT]" wire:model="prods.{{ $index }}.0.CMT">
                                    <input type="hidden" name="prods[{{ $index }}][0][dist]" wire:model="prods.{{ $index }}.0.dist">
                                    <input type="hidden" name="prods[{{ $index }}][0][anoMod]" wire:model="prods.{{ $index }}.0.anoMod">
                                    <input type="hidden" name="prods[{{ $index }}][0][anoFab]" wire:model="prods.{{ $index }}.0.anoFab">
                                    <input type="hidden" name="prods[{{ $index }}][0][tpPint]" wire:model="prods.{{ $index }}.0.tpPint">
                                    <input type="hidden" name="prods[{{ $index }}][0][espVeic]" wire:model="prods.{{ $index }}.0.espVeic">
                                    <input type="hidden" name="prods[{{ $index }}][0][VIN]" wire:model="prods.{{ $index }}.0.VIN">
                                    <input type="hidden" name="prods[{{ $index }}][0][condVeic]" wire:model="prods.{{ $index }}.0.condVeic">
                                    <input type="hidden" name="prods[{{ $index }}][0][cMod]" wire:model="prods.{{ $index }}.0.cMod">
                                    <input type="hidden" name="prods[{{ $index }}][0][cCorDENATRAN]" wire:model="prods.{{ $index }}.0.cCorDENATRAN">
                                    <input type="hidden" name="prods[{{ $index }}][0][lota]" wire:model="prods.{{ $index }}.0.lota">
                                    <input type="hidden" name="prods[{{ $index }}][0][tpRest]" wire:model="prods.{{ $index }}.0.tpRest">
                                    <input type="hidden" name="prods[{{ $index }}][0][tpVeic]" wire:model="prods.{{ $index }}.0.tpVeic">
                                @endif
                            @endforeach
                        </div>
                    </section>
                </main>
            </div>

            <div class="card-footer bg-transparent border-0 mt-4">
                <div class="row">
                    <div class="col-md-6 mx-auto text-center">
                        <button type="submit" class="btn custom-btn-success btn-block">
                            <div wire:loading.remove wire:target="submit">
                                <i class="fas fa-check-circle mr-1"></i> Finalizar e Dar Entrada
                            </div>
                            <div wire:loading wire:target="submit">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processando...
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('.select2-basic').select2({
            width: '100%',
            placeholder: "Selecione uma opção",
            allowClear: true
        });

        // Loop para cada select e setar o valor inicial e o listener
        @foreach ($prods as $index => $item)
            var initialValue = @json($item[0]['categoria'] ?? null);
            var selector = '#categoria_{{ $index }}';

            $(selector).val(initialValue).trigger('change');
            
            $(selector).on('change', function (e) {
                let localIndex = {{ $index }};
                @this.set('prods.' + localIndex + '.0.categoria', e.target.value);
            });
        @endforeach

        // Validação Bootstrap
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
        let openAccordionId = null;

        // Antes do Livewire enviar uma atualização, salva o ID do acordeão aberto
        Livewire.hook('message.sent', () => {
            const openAccordion = document.querySelector('#accordionProducts .collapse.show');
            if (openAccordion) {
                openAccordionId = openAccordion.id;
            }
        });

        // Depois que o Livewire atualiza o DOM, reabre o acordeão que estava aberto
        Livewire.hook('message.processed', () => {
            // Re-inicializa o Select2 para os elementos que podem ter sido atualizados
            initSelect2();

            if (openAccordionId) {
                $('#' + openAccordionId).collapse('show');
                openAccordionId = null; // Limpa o ID para a próxima requisição
            }
        });
    });
</script>
@endpush

