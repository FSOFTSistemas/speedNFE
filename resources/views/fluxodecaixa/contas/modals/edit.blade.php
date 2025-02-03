<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('contas.update', $conta->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-id" name="id" value="{{ $conta->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Editar Conta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="edit-codigo">Código</label>
                    <input type="text" id="edit-codigo" name="codigo" class="form-control" value="{{ $conta->codigo }}" required>

                    <label for="edit-descricao">Descrição</label>
                    <input type="text" id="edit-descricao" name="descricao" class="form-control" value="{{ $conta->descricao }}" required>

                    <label for="edit-tipo">Tipo</label>
                    <select id="edit-tipo" name="tipo" class="form-control">
                        <option value="Receita" {{ $conta->tipo == 'Receita' ? 'selected' : '' }}>Receita</option>
                        <option value="Despesa" {{ $conta->tipo == 'Despesa' ? 'selected' : '' }}>Despesa</option>
                        <option value="Ativo" {{ $conta->tipo == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="Passivo" {{ $conta->tipo == 'Passivo' ? 'selected' : '' }}>Passivo</option>
                    </select>

                    <label for="edit-conta-pai">Conta Pai</label>
                    <select id="edit-conta-pai" name="conta_pai_id" class="form-control">
                        <option value="">Nenhuma</option>
                        @foreach($contas as $contaOption)
                            <option value="{{ $contaOption->id }}" {{ $conta->conta_pai_id == $contaOption->id ? 'selected' : '' }}>
                                {{ $contaOption->descricao }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Atualizar</button>
                </div>
            </div>
        </form>
    </div>
</div>