<div>
    <div class="card">
        <div class="card-header">
            <div class="row text-center">
                <div class="col">
                    <h4>Venda em Aberto</h4>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col col-md-7">
                    <table class="table table-hover table-responsive shadow p-3 mb-5 bg-body rounded">
                        <thead class="table-primary">
                            <tr>
                                <th>Item</th>
                                <th>Qtde.</th>
                                <th>Nome</th>
                                <th>Unitário</th>
                                <th>Desconto</th>
                                <th>Acrescimo</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ([] as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @empty
                                <tr class="text-center">
                                    <td class="text-blue" colspan="7">Sem itens...</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="col col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <img src="{{ asset('logo.png') }}" class="w-100" alt="Teste de doidinnn">
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="prod" wire:model="prod"
                                                wire:keydown.enter="searchProds()" placeholder=" " required>
                                            <label for="prod">Produto</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" name="qtde" wire:model="qtde"
                                                placeholder=" " min="0" required>
                                            <label for="qtde">Quantidade</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" wire:model="desconto"
                                                placeholder=" " min="0" step="0.01" required>
                                            <label for="desconto">Desconto</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" wire:model="acrescimo"
                                                placeholder=" " min="0" step="0.01" required>
                                            <label for="acrescimo">Acrescimo</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" name="qtde" wire:model="qtde"
                                                placeholder=" " min="0" required>
                                            <label for="qtde">Total</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row text-center mt-2">
                                <div class="col">
                                    <button class="btn btn-primary" wire:click="addProd">+ Adicionar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row text-center">
                <div class="col">
                    <h5>Valor Total: <b>R$ {{ number_format($valorTotal, 2) }}</b></h5>
                </div>
            </div>
        </div>
    </div>
</div>

@if (!empty($results))
    @component('components.modal', [
        'modalId' => 'AddProdModal',
        'modalTitle' => 'Adicionar Produtos',
        'sizeModal' => 'modal-lg',
    ])
        @foreach ($results as $result)
            <ol>
                <li>{{ $result->id }}</li>
            </ol>
        @endforeach
    @endcomponent
@endif

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
                        const accordion = document.getElementById('accordion');
                        accordion.classList.add('invalid-accordion');
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
@endsection


<script>
    document.addEventListener('livewire:load', function() {
        Livewire.on('OpenAddProdModal', function() {
            $('#AddProdModal').modal('show');
        });
    });
</script>
