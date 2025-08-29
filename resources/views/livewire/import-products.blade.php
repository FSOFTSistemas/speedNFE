<div>
    <form class="row g-3 needs-validation" novalidate action="{{ route('entradas.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                <header>
                    <div class="text-center">
                        <h4>{{ $emit['xFant'] }}</h4>
                    </div>

                    <input type="hidden" class="form-control" id="nNF" name="nNF" wire:model="ide.nNF" required>

                    <div class="row g-2 p-2">
                        <div class="col-md-7">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="natOp" name="natOp"
                                        wire:model="ide.natOp" placeholder="Natureza da operação" readonly required>
                                    <label for="natOp">Natureza Operação</label>
                                    <div class="invalid-feedback">
                                        Informe uma natureza válida.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="dhEmi" name="dhEmi"
                                        placeholder="Data de emissão"
                                        value="{{ date('d/m/Y H:i:s', strtotime($ide['dhEmi'])) }}" readonly required>
                                    <label for="dhEmi">Data Emissão</label>
                                    <div class="invalid-feedback">
                                        Informe uma data válida.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="dhSaiEnt" name="dhSaiEnt"
                                        placeholder="Data de saída/entrada"
                                        value="{{ date('d/m/Y H:i:s', strtotime($ide['dhSaiEnt'] ?? '1970-01-01 00:00:00')) }}" readonly
                                        required>
                                    <label for="dhSaiEnt">Data Saída/Entrada</label>
                                    <div class="invalid-feedback">
                                        Informe uma data válida.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 p-2">
                        <div class="col-md-8">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="chNFe" name="chNFe"
                                        placeholder="Chave NFe" wire:model="chNFe" readonly required>
                                    <label for="chNFe">Chave NFe</label>
                                    <div class="invalid-feedback">
                                        Informe uma chave válida.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group has-validation">
                                <span class="input-group-text bg-secondary">R$</span>
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="vNF" name="vNF"
                                        placeholder="Valor total" wire:model="vNF" readonly required>
                                    <label for="vNF">Valor Total</label>
                                    <div class="invalid-feedback">
                                        Informe um valor válido.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 p-2">
                        <div class="col-md-7">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="fornecedor" name="fornecedor"
                                        placeholder="Fornecedor" wire:model="emit.xNome" readonly required>
                                    <label for="fornecedor">Fornecedor</label>
                                    <div class="invalid-feedback">
                                        Informe um fornecedor válido.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="CNPJ" name="CNPJ"
                                        wire:model="emit.CNPJ" placeholder="CNPJ" readonly required>
                                    <label for="CNPJ">CNPJ</label>
                                    <div class="invalid-feedback">
                                        Informe CNPJ válido.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="IE" name="IE"
                                        wire:model="emit.IE" placeholder="Inscrição estadual" readonly required>
                                    <label for="IE">IE</label>
                                    <div class="invalid-feedback">
                                        Informe IE válido.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 p-2">
                        <div class="col-md">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="fone" name="fone"
                                        wire:model="emit.enderEmit.fone" placeholder="Fone" readonly required>
                                    <label for="fone">Fone</label>
                                    <div class="invalid-feedback">
                                        Informe um fone válido.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="rua" name="rua"
                                        wire:model="emit.enderEmit.xLgr" placeholder="Rua" readonly required>
                                    <label for="rua">Rua</label>
                                    <div class="invalid-feedback">
                                        Informe uma rua válida.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="nro" name="nro"
                                        wire:model="emit.enderEmit.nro" placeholder="Nº" readonly required>
                                    <label for="nro">Nº</label>
                                    <div class="invalid-feedback">
                                        Informe Nº válido.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 p-2">
                        <div class="col-md">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="bairro" name="bairro"
                                        wire:model="emit.enderEmit.xBairro" placeholder="Bairro" readonly required>
                                    <label for="bairro">Bairro</label>
                                    <div class="invalid-feedback">
                                        Informe um bairro válido.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="mun" name="mun"
                                        wire:model="emit.enderEmit.xMun" placeholder="Município" readonly required>
                                    <label for="mun">Município</label>
                                    <div class="invalid-feedback">
                                        Informe um município válido.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="uf" name="uf"
                                        wire:model="emit.enderEmit.UF" placeholder="UF" readonly required>
                                    <label for="uf">UF</label>
                                    <div class="invalid-feedback">
                                        Informe UF válido.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="CEP" name="CEP"
                                        wire:model="emit.enderEmit.CEP" placeholder="CEP" readonly required>
                                    <label for="CEP">CEP</label>
                                    <div class="invalid-feedback">
                                        Informe um CEP válido.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>

            </header>
            <main>
                <section>
                    <div class="text-center">
                        <h4>Produtos</h4>
                    </div>
                    <div class="accordion" id="accordion" wire:ignore>
                        @foreach ($prods as $index => $item)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $item[0]['cProd'] . $index }}"
                                        aria-expanded="true" aria-controls="collapse{{ $item[0]['cProd'] . $index }}">
                                        <input class="form-control-plaintext"
                                            wire:model="prods.{{ $index }}.0.xProd" readonly>
                                    </button>
                                </h2>
                                <div id="collapse{{ $item[0]['cProd'] . $index }}" class="accordion-collapse collapse"
                                    data-bs-parent="#accordion">
                                    <div class="accordion-body">
                                        <fieldset class="border border-secondary p-2 rounded">
                                            <legend>Produto</legend>
                                            <div class="row g-2 p-2">
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control"
                                                                id="prods[{{ $index }}][0][xProd]"
                                                                name="prods[{{ $index }}][0][xProd]"
                                                                wire:model="prods.{{ $index }}.0.xProd"
                                                                oninput="this.value = this.value.toUpperCase()"
                                                                placeholder="Produto" required>
                                                            <label for="xProd">Produto</label>
                                                            <div class="invalid-feedback">
                                                                Informe um produto válido.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-2 p-2">
                                                <div class="col-md-6">
                                                    <div class="input-group has-validation">
                                                        <div class="form-floating">
                                                            <select class="form-select"
                                                                name="prods[{{ $index }}][0][categoria]"
                                                                required>
                                                                <option value="">Selecione um item</option>
                                                                @foreach (json_decode($categorias) as $cat)
                                                                    <option value="{{ $cat->id }}">
                                                                        {{ $cat->descricao }}</option>
                                                                @endforeach
                                                            </select>
                                                            <label for="">Categoria</label>
                                                            <div class="invalid-feedback">
                                                                Informe uma categoria válida.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="input-group has-validation">
                                                        <span class="input-group-text bg-secondary">R$</span>
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control"
                                                                wire:model="prods.{{ $index }}.0.ST"
                                                                step="0.01" placeholder="ST" readonly>
                                                            <label for="">CST</label>
                                                            <div class="invalid-feedback">
                                                                Informe um cst válido.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row g-2 p-2">
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control"
                                                                id="prods[{{ $index }}][0][cEAN]"
                                                                name="prods[{{ $index }}][0][cEAN]"
                                                                wire:model="prods.{{ $index }}.0.cEAN"
                                                                placeholder="EAN" required>
                                                            <label for="cEAN">EAN</label>
                                                            <div class="invalid-feedback">
                                                                Informe um EAN válido.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control"
                                                                id="prods[{{ $index }}][0][uCom]"
                                                                name="prods[{{ $index }}][0][uCom]"
                                                                wire:model="prods.{{ $index }}.0.uCom"
                                                                placeholder="Unidade" required>
                                                            <label for="uCom">Unidade</label>
                                                            <div class="invalid-feedback">
                                                                Informe uma unidade válida.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <div class="form-floating">
                                                            <input type="number" class="form-control" readonly
                                                                id="prods[{{ $index }}][0][qCom]"
                                                                name="prods[{{ $index }}][0][qCom]"
                                                                wire:model="prods.{{ $index }}.0.qCom"
                                                                placeholder="Quantidade" required>
                                                            <label for="qCom">Quantidade</label>
                                                            <div class="invalid-feedback">
                                                                Informe uma quantidade válida.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control"
                                                                id="prods[{{ $index }}][0][NCM]"
                                                                name="prods[{{ $index }}][0][NCM]"
                                                                wire:model="prods.{{ $index }}.0.NCM"
                                                                placeholder="NCM" readonly>
                                                            <label for="NCM">NCM</label>
                                                            <div class="invalid-feedback">
                                                                Informe um NCM válido.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-2 p-2">
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <span class="input-group-text bg-secondary">R$</span>
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control"
                                                                id="prods[{{ $index }}][0][vProd]"
                                                                name="prods[{{ $index }}][0][vProd]"
                                                                wire:model="prods.{{ $index }}.0.vProd"
                                                                step="0.01" readonly placeholder="Valor de custo"
                                                                required>
                                                            <label for="vProd">Valor de Custo</label>
                                                            <div class="invalid-feedback">
                                                                Informe um valor de custo válido.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <span class="input-group-text bg-secondary">R$</span>
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control"
                                                                id="prods[{{ $index }}][0][vVendaProd]"
                                                                name="prods[{{ $index }}][0][vVendaProd]"
                                                                wire:model="prods.{{ $index }}.0.vVendaProd"
                                                                step="0.01"
                                                                oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                                                placeholder="Valor de venda" required>
                                                            <label for="vProd">Valor de Venda</label>
                                                            <div class="invalid-feedback">
                                                                Informe um valor de venda válido.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <div class="form-floating">
                                                            <input type="number" class="form-control"
                                                                wire:model="prods.{{ $index }}.0.margem"
                                                                wire:input="calcValorVenda({{ $index }})"
                                                                step="0.01" min="0"
                                                                placeholder="Margem de lucro">
                                                            <label for="margem">Margem de lucro</label>
                                                            <div class="invalid-feedback">
                                                                Informe uma margem de lucro válida.
                                                            </div>
                                                        </div>
                                                        <span class="input-group-text bg-secondary">%</span>
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control"
                                                                id="prods[{{ $index }}][0][CFOP]"
                                                                name="prods[{{ $index }}][0][CFOP]"
                                                                wire:model="prods.{{ $index }}.0.CFOP"
                                                                placeholder="CFOP" disabled>
                                                            <label for="CFOP">CFOP</label>
                                                            <div class="invalid-feedback">
                                                                Informe um CFOP válido.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                                <input type="hidden" name="prods[{{ $index }}][0][tpProd]"
                                    wire:model="prods.{{ $index }}.0.tpProd" required>
                                <input type="hidden" name="prods[{{ $index }}][0][ICMS]"
                                    wire:model="prods.{{ $index }}.0.ICMS" required>
                                <input type="hidden" name="prods[{{ $index }}][0][CFOP_INTERNO]"
                                    wire:model="prods.{{ $index }}.0.CFOP_INTERNO" required>
                                <input type="hidden" name="prods[{{ $index }}][0][CFOP_EXTERNO]"
                                    wire:model="prods.{{ $index }}.0.CFOP_EXTERNO" required>
                                <input type="hidden" name="prods[{{ $index }}][0][PIS]"
                                    wire:model="prods.{{ $index }}.0.PIS" required>
                                <input type="hidden" name="prods[{{ $index }}][0][IPI]"
                                    wire:model="prods.{{ $index }}.0.IPI" required>
                                <input type="hidden" name="prods[{{ $index }}][0][COFINS]"
                                    wire:model="prods.{{ $index }}.0.COFINS" required>
                                <input type="hidden" name="prods[{{ $index }}][0][CST]"
                                    wire:model="prods.{{ $index }}.0.CST" required>
                                <input type="hidden" name="prods[{{ $index }}][0][CSOSN]"
                                    wire:model="prods.{{ $index }}.0.CSOSN" required>
                                <input type="hidden" name="prods[{{ $index }}][0][CST_PIS]"
                                    wire:model="prods.{{ $index }}.0.CST_PIS" required>
                                <input type="hidden" name="prods[{{ $index }}][0][CST_COFINS]"
                                    wire:model="prods.{{ $index }}.0.CST_COFINS" required>
                                @if ($prods[$index][0]['tpProd'] == 1)
                                    <input type="hidden" name="prods[{{ $index }}][0][tpOp]"
                                        wire:model="prods.{{ $index }}.0.tpOp" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][chassi]"
                                        wire:model="prods.{{ $index }}.0.chassi" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][cCor]"
                                        wire:model="prods.{{ $index }}.0.cCor" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][xCor]"
                                        wire:model="prods.{{ $index }}.0.xCor" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][pot]"
                                        wire:model="prods.{{ $index }}.0.pot" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][cilin]"
                                        wire:model="prods.{{ $index }}.0.cilin" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][pesoL]"
                                        wire:model="prods.{{ $index }}.0.pesoL" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][pesoB]"
                                        wire:model="prods.{{ $index }}.0.pesoB" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][nSerie]"
                                        wire:model="prods.{{ $index }}.0.nSerie" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][tpComb]"
                                        wire:model="prods.{{ $index }}.0.tpComb" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][nMotor]"
                                        wire:model="prods.{{ $index }}.0.nMotor" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][CMT]"
                                        wire:model="prods.{{ $index }}.0.CMT" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][dist]"
                                        wire:model="prods.{{ $index }}.0.dist" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][anoMod]"
                                        wire:model="prods.{{ $index }}.0.anoMod" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][anoFab]"
                                        wire:model="prods.{{ $index }}.0.anoFab" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][tpPint]"
                                        wire:model="prods.{{ $index }}.0.tpPint" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][espVeic]"
                                        wire:model="prods.{{ $index }}.0.espVeic" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][VIN]"
                                        wire:model="prods.{{ $index }}.0.VIN" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][condVeic]"
                                        wire:model="prods.{{ $index }}.0.condVeic" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][cMod]"
                                        wire:model="prods.{{ $index }}.0.cMod" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][cCorDENATRAN]"
                                        wire:model="prods.{{ $index }}.0.cCorDENATRAN" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][lota]"
                                        wire:model="prods.{{ $index }}.0.lota" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][tpRest]"
                                        wire:model="prods.{{ $index }}.0.tpRest" required>
                                    <input type="hidden" name="prods[{{ $index }}][0][tpVeic]"
                                        wire:model="prods.{{ $index }}.0.tpVeic" required>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            </main>
            <div class="card-footer">
                <div class="text-center">
                    <button type="submit" class="btn btn-outline-success btn-lg">Finalizar</button>
                </div>
            </div>
        </div>
    </form>
</div>

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .invalid-accordion {
            border: 1px solid #dc3545;
            border-radius: 0.25rem;
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
