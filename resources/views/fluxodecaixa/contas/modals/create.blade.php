<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('contas.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Conta</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <label>Código</label>
                    <input type="text" name="codigo" class="form-control" required>

                    <label>Descrição</label>
                    <input type="text" name="descricao" class="form-control" required>

                    <label>Tipo</label>
                    <select name="tipo" class="form-control">
                        <option value="Receita">Receita</option>
                        <option value="Despesa">Despesa</option>
                        <option value="Ativo">Ativo</option>
                        <option value="Passivo">Passivo</option>
                    </select>

                    <label>Conta Pai</label>
                    <select name="conta_pai_id" class="form-control">
                        <option value="">Nenhuma</option>
                        @foreach($contas as $conta)
                            <option value="{{ $conta->id }}">{{ $conta->descricao }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>