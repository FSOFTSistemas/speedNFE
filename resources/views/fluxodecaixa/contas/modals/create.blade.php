<div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-labelledby="modalCreateLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreateLabel">Adicionar Nova Conta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('contas.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label">Código</label>
                        <input type="text" name="codigo" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Descrição</label>
                        <input type="text" name="descricao" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-control">
                            <option value="Sintética">Sintética</option>
                            <option value="Analítica">Analítica</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Conta Pai</label>
                        <select name="conta_pai_id" class="form-control">
                            <option value="">Nenhuma</option>
                            @foreach($contas->where('tipo', 'Sintética') as $conta)
                                <option value="{{ $conta->id }}">{{ $conta->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn custom-btn custom-btn-success">Salvar Nova Conta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>