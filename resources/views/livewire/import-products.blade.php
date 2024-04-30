<div>
    <form class="row g-3 needs-validation" novalidate action="{{ route('entradas.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                <header>
                    <div class="text-center">
                        {{-- @dd($ide) --}}
                        <h4>{{ $emit['xFant'] }}</h4>
                    </div>
                    <div class="row g-2 p-2">
                        <div class="col-md-7">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="natOp"
                                        placeholder="Natureza da operação" value="{{ $ide['natOp'] }}" required>
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
                                    <input type="text" class="form-control" id="dhEmi"
                                        placeholder="Data de emissão" value="{{ $ide['dhEmi'] }}" required>
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
                                    <input type="text" class="form-control" id="dhSaiEnt"
                                        placeholder="Data de saída/entrada" value="{{ $ide['dhSaiEnt'] }}" required>
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
                                    <input type="text" class="form-control" id="chNFe" placeholder="Chave NFe"
                                        value="{{ $chNFe }}" required>
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
                                    <input type="text" class="form-control" id="vNF" placeholder="Valor total"
                                        value="{{ number_format($vNF, 2) }}" required>
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
                                    <input type="text" class="form-control" id="fornecedor" placeholder="Fornecedor"
                                        value="{{ $emit['xNome'] }}" required>
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
                                    <input type="text" class="form-control" id="CNPJ"
                                        value="{{ $emit['CNPJ'] }}" placeholder="CNPJ" required>
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
                                    <input type="text" class="form-control" id="IE"
                                        value="{{ $emit['IE'] }}" placeholder="Inscrição estadual" required>
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
                                    <input type="text" class="form-control" id="fone"
                                        value="{{ (string) $emit['enderEmit']->fone }}" placeholder="Fone" required>
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
                                    <input type="text" class="form-control" id="rua"
                                        value="{{ (string) $emit['enderEmit']->xLgr }}" placeholder="Rua" required>
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
                                    <input type="text" class="form-control" id="nro"
                                        value="{{ (string) $emit['enderEmit']->nro }}" placeholder="Nº" required>
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
                                    <input type="text" class="form-control" id="bairro"
                                        value="{{ (string) $emit['enderEmit']->xBairro }}" placeholder="Bairro"
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
                                    <input type="text" class="form-control" id="mun"
                                        value="{{ (string) $emit['enderEmit']->xMun }}" placeholder="Município"
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
                                    <input type="text" class="form-control" id="uf"
                                        value="{{ (string) $emit['enderEmit']->UF }}" placeholder="UF" required>
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
                                    <input type="text" class="form-control" id="CEP"
                                        value="{{ (string) $emit['enderEmit']->CEP }}" placeholder="CEP" required>
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
                                                            <input type="text" class="form-control" id="xProd"
                                                                value="{{ $item[0]['xProd'] }}" placeholder="Produto"
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
                                                            <input type="text" class="form-control" id="cEAN"
                                                                value="{{ $item[0]['cEAN'] }}" placeholder="EAN"
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
                                                            <input type="text" class="form-control" id="uCom"
                                                                value="{{ $item[0]['uCom'] }}" placeholder="Unidade"
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
                                                            <input type="text" class="form-control" id="NCM"
                                                                value="{{ $item[0]['NCM'] }}" placeholder="NCM"
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
                                                            <input type="number" class="form-control" id="vUnCom"
                                                                value="{{ $item[0]['vUnCom'] }}"
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
                                                            <input type="number" class="form-control" id="qCom"
                                                                value="{{ $item[0]['qCom'] }}"
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
                                                            <input type="number" class="form-control" id="vProd"
                                                                value="{{ $item[0]['vProd'] }}"
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
                                                            <input type="text" class="form-control" id="CFOP"
                                                                value="{{ $item[0]['CFOP'] }}" placeholder="CFOP"
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
                                                        <span class="input-group-text bg-secondary">R$</span>
                                                        <div class="form-floating">
                                                            <input type="number" class="form-control" id="vUnCom"
                                                                value="{{ $item[0]['vUnCom'] }}"
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
                                                            <input type="number" class="form-control" id="qCom"
                                                                value="{{ $item[0]['qCom'] }}"
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
                                                            <input type="number" class="form-control" id="vProd"
                                                                value="{{ $item[0]['vProd'] }}"
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
                                                            <input type="text" class="form-control" id="CFOP"
                                                                value="{{ $item[0]['CFOP'] }}" placeholder="CFOP"
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
