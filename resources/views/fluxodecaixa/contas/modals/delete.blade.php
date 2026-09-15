<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteLabel">Confirmar Exclusão</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p>Tem certeza que deseja apagar a conta <strong id="deleteContaDescricao" class="text-danger"></strong>?</p>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn custom-btn custom-btn-danger">Sim, Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>