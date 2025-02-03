<div class="modal fade" id="modalEditFluxo{{ $lancamento->id }}" tabindex="-1" aria-labelledby="modalEditFluxoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('fluxo-caixa.update', $lancamento->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-id" name="id" value="{{ $lancamento->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditFluxoLabel">Editar Fluxo de Caixa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Descrição -->
                    <div class="form-group">
                        <label for="edit-descricao">Descrição</label>
                        <input type="text" id="descricao" name="descricao" class="form-control @error('descricao') is-invalid @enderror" value="{{ old('descricao', $lancamento->descricao) }}" required>
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Data -->
                    <div class="form-group">
                        <label for="edit-data">Data</label>
                        <input type="date" id="data" name="data" class="form-control @error('data') is-invalid @enderror" value="{{ old('data', $lancamento->data) }}" required>
                        @error('data')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Valor -->
                    <div class="form-group">
                        <label for="edit-valor">Valor</label>
                        <input type="number" id="valor" name="valor" class="form-control @error('valor') is-invalid @enderror" value="{{ old('valor', $lancamento->valor) }}" required step="0.01">
                        @error('valor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tipo (Receita ou Despesa) -->
                    <div class="form-group">
                        <label for="edit-tipo">Tipo</label>
                        <select id="tipo" name="tipo" class="form-control @error('tipo') is-invalid @enderror" required>
                            <option value="Entrada" {{ old('tipo', $lancamento->tipo) == 'Entrada' ? 'selected' : '' }}>Entrada</option>
                            <option value="Saida" {{ old('tipo', $lancamento->tipo) == 'Saida' ? 'selected' : '' }}>Saida</option>
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between w-100">
                    <button type="button" class="btn btn-secondary flex-fill" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-warning flex-fill ml-2">Atualizar</button>
                </div>
            </div>
        </form>
    </div>
</div>