<div>
    <form action="/visualizar/{{$pedido}}" method="POST">
        @csrf
        <label>Empresa</label>
        <select disabled class="form-control" name="empresa" wire:model="empresa">
            <option value="" disabled selected>--Escolha uma empresa--</option>
            @if($empresa != '')
                @foreach(json_decode($empresas) as $emp)
                    <option value="{{$emp->id}}">{{$emp->fantasia}} | {{$emp->cpf_cnpj}}</option>
                @endforeach
            @else
                @foreach($empresas as $emp)
                    <option value="{{$emp->id}}">{{$emp->fantasia}} | {{$emp->cpf_cnpj}}</option>
                @endforeach
            @endif
        </select>


        <div class="row">
            <div class="col-8">
                <label>Cliente</label>
                <select class="form-control" name="cliente" wire:model="cliente">
                    <option value="" disabled selected>--Escolha um cliente--</option>
                    @if($empresa != '')
                        @foreach(json_decode($clientes) as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->nome}} | {{$cliente->cpf_cnpj}}</option>
                        @endforeach
                    @else
                        @foreach($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->nome}} | {{$cliente->cpf_cnpj}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-1">
                <label>CFOP</label>
                <input wire:change="buscaCfop()" class="form-control" wire:model="bcfop">
            </div>
            <div class="col-3">
                <label>Descrição CFOP</label>
                <select wire:change="atualizarBCfop()" required class="form-control" name="cfop" wire:model="cfop">
                    <option value='' disabled selected>--Selecione o CFOP da Nota--</option>
                    @if($empresa != '')
                        @foreach(json_decode($cfops) as $cfop)
                            <option value="{{$cfop->id}}">{{$cfop->cfop}} | {{$cfop->natureza}}</option>
                        @endforeach
                    @else
                        @foreach($cfops as $cfop)
                            <option value="{{$cfop->id}}">{{$cfop->cfop}} | {{$cfop->natureza}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <hr color="black">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-2">
                                <label>Cód Barras</label>
                                <input type="text" wire:model="barras" wire:keydown.enter="buscaProd()" class="form-control">
                            </div>

                            <div class="col-5">
                                {{-- <label>Produto</label>
                                <input type="text" wire:model="produto" class="form-control"> --}}
                                <label>Produto</label>
                                <select wire:change="atualizarProds()" class="form-control" wire:model="produto">
                                    <option value="" disabled selected>--Escolha um produto--</option>
                                    @if($empresa != '')
                                        @foreach(json_decode($produtos) as $produto)
                                            <option value="{{$produto->id}}">{{$produto->produto}}</option>
                                        @endforeach
                                    @else
                                        @foreach($produtos as $produto)
                                            <option value="{{$produto->id}}">{{$produto->produto}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-1">
                                <label>Qtd</label>
                                <input class="form-control" type="number" step="0.1" wire:change="atualizarTot()" wire:model="quantidade">
                            </div>

                            <div class="col-1">
                                <label>Valor</label>
                                <input class="form-control" type="number" step="0.01" wire:change="atualizarTot()" wire:model="preco">
                            </div>

                            <div class="col-1">
                                <label>Dsct. (%)</label>
                                <input class="form-control" type="number" step="0.1" wire:change="atualizarTot()" wire:model="desconto">
                            </div>

                            <div class="col-1">
                                <label>Total</label>
                                <input class="form-control" type="number" step="0.01" wire:model="total">
                            </div>

                            <div class="col-1">
                                <label>&nbsp;</label>
                                <a wire:click.prevent="salvarProd()" class="btn btn-success">Adicionar</a>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="overflow-auto" style="max-height: 30%">
                            <table class="table table-striped">
                                <thead>
                                    <th>Id</th>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Unitário</th>
                                    <th>Desconto</th>
                                    <th>Total</th>
                                    <th>Ações</th>
                                </thead>
                                <tbody>
                                    @foreach($vendaItens as $item)
                                        <tr>
                                        @foreach($item as $i)
                                            <td>{{$i}}</td>
                                        @endforeach
                                            <td><button wire:click.prevent="removerProduto({{array_search($item, $vendaItens, true)}})" class="btn btn-danger">rmv</button></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-3">
                                Soma produtos:
                            </div>
                            <div class="col-9">
                                <b><input disabled wire:model="subtotal" type="number" class="form-control"></b>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($vendaItens as $index => $vendaItem)
                    <input type="hidden" name="vendaItens[{{$index}}][produto_id]" wire:model="vendaItens.{{$index}}.produto_id">
                    <input type="hidden" name="vendaItens[{{ $index }}][quantidade]" wire:model="vendaItens.{{ $index }}.quantidade"/>
                    <input type="hidden" name="vendaItens[{{ $index }}][unitario]" wire:model="vendaItens.{{ $index }}.unitario"/>
                    <input type="hidden" name="vendaItens[{{ $index }}][desconto]" wire:model="vendaItens.{{ $index }}.desconto"/>
                    <input type="hidden" name="vendaItens[{{ $index }}][total]" wire:model="vendaItens.{{ $index }}.total"/>
                @endforeach

            </div>
        </div>

        <hr color="black">

        <label>Forma de Pagamento</label>
        <select class="form-control" name="forma" wire:model="pag">
            <option>--Escolha uma forma--</option>
            @if($empresa != '')
                @foreach(json_decode($formas) as $for)
                    <option value="{{$for->id}}">{{$for->descricao}}</option>
                @endforeach
            @else
                @foreach($formas as $for)
                    <option value="{{$for->id}}">{{$for->descricao}}</option>
                @endforeach
            @endif
        </select>

        <hr color="black">

        <div class="row">
            <div class="col-6" style="text-align: end">
                <a wire:click.prevent="cancelar()" class="btn btn-danger">Cancelar</a>
            </div>

            <div class="col-6">
                <button type="submit" class="btn btn-success">Salvar</a>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $(window).keydown(function(event){
                    if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                    }
                });
            });
        </script>

    </form>
</div>
