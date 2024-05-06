<div>
    <form class="row g-3 needs-validation" novalidate action="{{ route('entradas.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                <header>
                    <div class="text-center">
                        <h4>{{ $emit['xFant'] }}</h4>
                    </div>
                    <div class="row g-2 p-2">
                        <div class="col-md-7">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="natOp" name="natOp" wire:model="ide.natOp"
                                        placeholder="Natureza da operação" required>
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
                                        placeholder="Data de emissão" value="{{ date('d/m/Y H:i:s', strtotime($ide['dhEmi'])) }}" required>
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
                                        placeholder="Data de saída/entrada" value="{{ date('d/m/Y H:i:s', strtotime($ide['dhSaiEnt'])) }}" required>
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
                                    <input type="text" class="form-control" id="chNFe" name="chNFe" placeholder="Chave NFe"
                                        wire:model="chNFe" required>
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
                                    <input type="text" class="form-control" id="vNF" name="vNF" placeholder="Valor total"
                                        wire:model="vNF" required>
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
                                    <input type="text" class="form-control" id="fornecedor" name="fornecedor" placeholder="Fornecedor"
                                        wire:model="emit.xNome" required>
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
                                        wire:model="emit.CNPJ" placeholder="CNPJ" required>
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
                                        wire:model="emit.IE" placeholder="Inscrição estadual" required>
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
                                        wire:model="emit.enderEmit.fone" placeholder="Fone" required>
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
                                        wire:model="emit.enderEmit.xLgr" placeholder="Rua" required>
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
                                        wire:model="emit.enderEmit.nro" placeholder="Nº" required>
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
                                        wire:model="emit.enderEmit.xBairro" placeholder="Bairro"
                                        required>
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
                                        wire:model="emit.enderEmit.xMun" placeholder="Município"
                                        required>
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
                                        wire:model="emit.enderEmit.UF" placeholder="UF" required>
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
                                        wire:model="emit.enderEmit.CEP" placeholder="CEP" required>
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
                    <div class="accordion" id="accordion">
                        @foreach ($prods as $index => $item)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $item[0]['cProd'] . $index }}"
                                        aria-expanded="true"
                                        aria-controls="collapse{{ $item[0]['cProd'] . $index }}">
                                        {{ $item[0]['xProd'] }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $item[0]['cProd'] . $index }}"
                                    class="accordion-collapse collapse" data-bs-parent="#accordion">
                                    <div class="accordion-body">
                                        <fieldset class="border border-secondary p-2 rounded">
                                            <legend>Produto</legend>
                                            <div class="row g-2 p-2">
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control" id="xProd" name="xProd" wire:model="prods.{{ $index }}.0.xProd" placeholder="Produto"
                                                                required>
                                                            <label for="xProd">Produto</label>
                                                            <div class="invalid-feedback">
                                                                Informe um produto válido.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-2 p-2">
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control" id="cEAN" name="cEAN"
                                                                wire:model="prods.{{ $index }}.0.cEAN" placeholder="EAN"
                                                                required>
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
                                                            <input type="text" class="form-control" id="uCom" name="uCom"
                                                                wire:model="prods.{{ $index }}.0.uCom" placeholder="Unidade"
                                                                required>
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
                                                            <input type="text" class="form-control" id="NCM" name="NCM"
                                                                wire:model="prods.{{ $index }}.0.NCM" placeholder="NCM"
                                                                required>
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
                                                            <input type="number" class="form-control" id="vUnCom" name="vUnCom"
                                                                wire:model="prods.{{ $index }}.0.vUnCom"
                                                                placeholder="Valor unitário" required>
                                                            <label for="vUnCom">Valor Unitário</label>
                                                            <div class="invalid-feedback">
                                                                Informe um valor unitário válido.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <div class="form-floating">
                                                            <input type="number" class="form-control" id="qCom" id="qCom"
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
                                                        <span class="input-group-text bg-secondary">R$</span>
                                                        <div class="form-floating">
                                                            <input type="number" class="form-control" id="vProd" name="vProd"
                                                                wire:model="prods.{{ $index }}.0.vProd"
                                                                placeholder="Valor total" required>
                                                            <label for="vProd">Valor Total</label>
                                                            <div class="invalid-feedback">
                                                                Informe um valor total válido.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control" id="CFOP" name="CFOP"
                                                                wire:model="prods.{{ $index }}.0.CFOP" placeholder="CFOP"
                                                                required>
                                                            <label for="CFOP">CFOP</label>
                                                            <div class="invalid-feedback">
                                                                Informe um CFOP válido.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="border border-secondary mt-3 p-2 rounded">
                                            <legend>Tributos</legend>

                                            <div class="row g-2 p-2">
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <div class="form-floating">
                                                            <input type="number" class="form-control" id="icms" name="icms"
                                                                wire:model="prods.{{ $index }}.0.ICMS"
                                                                placeholder="ICMS" required>
                                                            <label for="icms">ICMS</label>
                                                            <div class="invalid-feedback">
                                                                Informe um ICMS válido.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <div class="form-floating">
                                                            <input type="number" class="form-control" id="ipi" name="ipi"
                                                                wire:model="prods.{{ $index }}.0.IPI"
                                                                placeholder="IPI" required>
                                                            <label for="ipi">IPI</label>
                                                            <div class="invalid-feedback">
                                                                Informe um IPI válido.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <div class="form-floating">
                                                            <input type="number" class="form-control" id="pis" name="pis"
                                                                wire:model="prods.{{ $index }}.0.PIS"
                                                                placeholder="PIS" required>
                                                            <label for="pis">PIS</label>
                                                            <div class="invalid-feedback">
                                                                Informe um PIS válido.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <div class="input-group has-validation">
                                                        <div class="form-floating">
                                                            <input type="number" class="form-control" id="cofins" name="cofins"
                                                                wire:model="prods.{{ $index }}.0.COFINS" placeholder="COFINS"
                                                                required>
                                                            <label for="cofins">COFINS</label>
                                                            <div class="invalid-feedback">
                                                                Informe um COFINS válido.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
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
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
@endsection
