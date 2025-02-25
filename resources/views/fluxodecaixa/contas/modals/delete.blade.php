<div class="modal fade" id="modalDelete{{$conta->id}}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('contas.destroy', $conta->id ) }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" id="delete-id" name="id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tem certeza?</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Deletar</button>
                </div>
            </div>
        </form>
    </div>
</div>