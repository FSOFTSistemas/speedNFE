<div>


    <div class="modal fade bd-add-modal-lg" tabindex="-1" role="dialog" id="meuModal" wire:ignore="true" aria-labelledby="myLargeModalLabel"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">

                    <div class="col" style="text-align: center">
                        <div class="modal-title" style="text: center">
                            <h4>Adicionar documento</h4>
                        </div>
                    </div>

                </div>
                <div class="modal-body">


                    <form method="POST">
                        {{-- @csrf --}}

                        <div class="row">
                            <div class="col-md-3 col-xs-3">
                                <div class="form-group">
                                    <label for="">Tipo de Documento *</label>
                                    <select class="form-control" wire:model="tipoDocumento" name="tipoDocumento"
                                        required>
                                        <option value="">Selecionar</option>
                                        @foreach ($tiposDocumentos as $tipoDocumento)
                                            <option value="{{ $tipoDocumento }}">{{ $tipoDocumento }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 col-xs-4">
                                <div class="form-group">
                                    <label for="">Local de descarregamento *</label>
                                    <select class="form-control" wire:model="localDescarregamento"
                                        name="localDescarregamento" required>
                                        <option value="">Selecionar</option>
                                        @foreach ($ufs as $uf)
                                            <option value="{{ $uf }}">{{ $uf }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-5 col-xs-5">
                                <div class="form-group">
                                    <label for="">Cidade *</label>
                                    <input class="form-control" type="text" wire:model="cidade"
                                        placeholder="Cidade..." required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="">Valor total *</label>
                                    <input class="form-control" type="number" step="0.01" min="0"
                                        wire:model="valorTotal" required placeholder="Valor...">
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <label for="">Peso (Kg) *</label>
                                    <input class="form-control" type="number" step="0.01" min="0"
                                        wire:model="peso" required placeholder="Peso...">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="">Chave de acesso *</label>
                                    <input class="form-control" type="number" wire:model="chave" required
                                        placeholder="Chave...">
                                </div>
                            </div>
                        </div>

                        <div class="row" style="text-align: center">
                            <div class="col">
                                <button class="btn btn-success" wire:click="atualizar()" style="width: 25%" type="submit">Salvar</button>
                            </div>
                        </div>

                    </form>


                </div>

                <div class="modal-footer">
                    <a class="btn btn-secondary" href="{{ route('mdfe.index') }}">Cancelar</a>
                </div>
            </div>
        </div>
    </div>

</div>


<script>
    $(document).ready(function() {
        $('#meuModal').modal('show');
    });
</script>
