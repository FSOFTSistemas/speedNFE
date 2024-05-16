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
                <div class="col-7">
                    <table class="table table-hover shadow p-3 mb-5 bg-body rounded">
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
                                <tr class="text-center text-blue">
                                    <td colspan="7">Sem itens...</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="col-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <img src="{{ asset('avatar/user.png') }}" class="w-100 h-15" alt="Teste de doidinnn">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <input class="form-control" type="text" name="" id="">
                                    <label for="">Cod produto</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <input class="form-control" type="text" name="" id="">
                                    <label for="">Quantidade</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <button class="btn btn-primary">+ Adicionar</button>
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
                    <h5>Valor Total: <b>R$ {{ 2 }}</b></h5>
                </div>
            </div>
        </div>
    </div>
</div>
