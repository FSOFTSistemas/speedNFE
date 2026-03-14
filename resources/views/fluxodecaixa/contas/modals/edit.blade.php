<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel">Editar Conta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label for="edit_codigo" class="form-label">Código</label>
                        <input type="text" id="edit_codigo" name="codigo" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_descricao" class="form-label">Descrição</label>
                        <input type="text" id="edit_descricao" name="descricao" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_tipo" class="form-label">Tipo</label>
                        <select id="edit_tipo" name="tipo" class="form-control">
                            <option value="Sintética">Sintética</option>
                            <option value="Analítica">Analítica</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_conta_pai_id" class="form-label">Conta Pai</label>
                        <select id="edit_conta_pai_id" name="conta_pai_id" class="form-control">
                            <option value="">Nenhuma</option>
                            @foreach($contas->where('tipo', 'Sintética') as $contaPai)
                                <option value="{{ $contaPai->id }}">{{ $contaPai->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn custom-btn custom-btn-warning">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>