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
                                    <input type="text" class="form-control" id="chNFe" placeholder="Chave NFe" value="{{ $chNFe }}"
                                        required>
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
                                    <input type="text" class="form-control" id="vNF" placeholder="Valor total" value="{{ number_format($vNF, 2) }}"
                                        required>
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
                                    <input type="text" class="form-control" id="fornecedor"
                                        placeholder="Fornecedor" value="{{ $emit['xNome'] }}" required>
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
                                    <input type="text" class="form-control" id="CNPJ" value="{{ $emit['CNPJ'] }}"
                                        placeholder="CNPJ" required>
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
                                    <input type="text" class="form-control" id="IE" value="{{ $emit['IE'] }}"
                                        placeholder="Inscrição estadual" required>
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
                                    <input type="text" class="form-control" id="fone" value="{{ (string) $emit['enderEmit']->fone }}"
                                        placeholder="Fone" required>
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
                                    <input type="text" class="form-control" id="rua" value="{{ (string) $emit['enderEmit']->xLgr }}"
                                        placeholder="Rua" required>
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
                                    <input type="text" class="form-control" id="nro" value="{{ (string) $emit['enderEmit']->nro }}"
                                        placeholder="Nº" required>
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
                                    <input type="text" class="form-control" id="bairro" value="{{ (string) $emit['enderEmit']->xBairro }}"
                                        placeholder="Bairro" required>
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
                                    <input type="text" class="form-control" id="mun" value="{{ (string) $emit['enderEmit']->xMun }}"
                                        placeholder="Município" required>
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
                                    <input type="text" class="form-control" id="uf" value="{{ (string) $emit['enderEmit']->UF }}"
                                        placeholder="UF" required>
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
                                    <input type="text" class="form-control" id="CEP" value="{{ (string) $emit['enderEmit']->CEP }}"
                                        placeholder="CEP" required>
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
                        @foreach ($prods as $item)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $item[0]['cProd'] }}" aria-expanded="true"
                                        aria-controls="collapse{{ $item[0]['cProd'] }}">
                                        {{ $item[0]['xProd'] }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $item[0]['cProd'] }}" class="accordion-collapse collapse"
                                    data-bs-parent="#accordion">
                                    <div class="accordion-body">
                                        <strong>This is the first item's accordion body.</strong> It is shown by
                                        default, until the collapse plugin adds the appropriate classes that we use to
                                        style each element. These classes control the overall appearance, as well as the
                                        showing and hiding via CSS transitions. You can modify any of this with custom
                                        CSS or overriding our default variables. It's also worth noting that just about
                                        any HTML can go within the <code>.accordion-body</code>, though the transition
                                        does limit overflow.
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
