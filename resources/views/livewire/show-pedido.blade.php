<div>
        <div class="container">
            <div class="row" style="text-align: center">
                <div class="col">
                    <h5>Cabeçalho</h5>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>Empresa</label>
                    <select disabled class="form-control" name="empresa" wire:model="empresa">
                        @if ($empresa != '')
                            @foreach (json_decode($empresas) as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->fantasia }} | {{ $emp->cpf_cnpj }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 col-xs-12">
                    <label>Cliente</label>
                    <select class="form-control" name="cliente" disabled wire:model="cliente">
                        @if ($empresa != '')
                            @foreach (json_decode($clientes) as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nome }} | {{ $cliente->cpf_cnpj }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-1 col-xs-6">
                    <label>CFOP</label>
                    <input class="form-control" disabled wire:model="bcfop">
                </div>
                <div class="col-md-3 col-xs-6">
                    <label>Descrição CFOP</label>
                    <select class="form-control" name="cfop" disabled
                        wire:model="cfop">
                        @if ($empresa != '')
                            @foreach (json_decode($cfops) as $cfop)
                                <option value="{{ $cfop->id }}">{{ $cfop->cfop }} | {{ $cfop->natureza }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <br>
            <hr color="black">
        </div>

        <div class="container">
            <div class="row" style="text-align: center">
                <div class="col">
                    <h5>Itens</h5>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="overflow-auto" style="max-height: 30%">
                                <table class="table table-hover">
                                    <thead class="table-primary" style="text-align: center">
                                        <th>Id</th>
                                        <th>Produto</th>
                                        <th>Quantidade</th>
                                        <th>Unitário</th>
                                        <th>Desconto</th>
                                        <th>Total</th>
                                    </thead>
                                    <tbody style="text-align: center">
                                        @foreach ($vendaItens as $item)
                                            <tr>
                                                @foreach ($item as $i)
                                                    <td>{{ $i }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-5 col-xs-6" style="text-align: center">
                                    Soma produtos:
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <b><input disabled wire:model="subtotal" type="number" class="form-control"></b>
                                </div>
                            </div>
                        </div>
                    </div>

                    @foreach ($vendaItens as $index => $vendaItem)
                        <input type="hidden" name="vendaItens[{{ $index }}][produto_id]"
                            wire:model="vendaItens.{{ $index }}.produto_id">
                        <input type="hidden" name="vendaItens[{{ $index }}][quantidade]"
                            wire:model="vendaItens.{{ $index }}.quantidade" />
                        <input type="hidden" name="vendaItens[{{ $index }}][unitario]"
                            wire:model="vendaItens.{{ $index }}.unitario" />
                        <input type="hidden" name="vendaItens[{{ $index }}][desconto]"
                            wire:model="vendaItens.{{ $index }}.desconto" />
                        <input type="hidden" name="vendaItens[{{ $index }}][total]"
                            wire:model="vendaItens.{{ $index }}.total" />
                    @endforeach

                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $(window).keydown(function(event) {
                    if (event.keyCode == 13) {
                        event.preventDefault();
                        return false;
                    }
                });
            });
        </script>
</div>