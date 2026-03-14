<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('fluxo-caixa.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Lançamento</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <label>Data</label>
                    <input type="date" name="data" class="form-control" required>

                    <label>Descrição</label>
                    <input type="text" name="descricao" class="form-control" required>

                    <label>Valor</label>
                    <input type="number" name="valor" class="form-control" step="0.01" required>

                    <label>Tipo</label>
                    <select name="tipo" class="form-control">
                        <option value="Entrada">Entrada</option>
                        <option value="Saída">Saída</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>