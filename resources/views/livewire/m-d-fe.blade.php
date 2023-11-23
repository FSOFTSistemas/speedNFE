<div>

    <form action="" method="POST">

        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <div class="row" style="text-align: center">
                                    <div class="col">
                                        <h5>NFes importadas</h5>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <h5>{{ $chave }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <div class="row" style="text-align: center">
                                    <div class="col">
                                        <h5>Transporte</h5>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="">Veículo de tração *</label>
                                            <input class="form-control" type="text" name="veiculoTracao"
                                                wire:model="veiculoTracao" required placeholder="Veículo de tração...">
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-group">
                                            <label for="">Motorista *</label>
                                            <input class="form-control" type="text" name="motorista"
                                                wire:model="motorista" required placeholder="Motorista...">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="">Veículo de reboque</label>
                                            <input class="form-control" type="text" name="veiculoReboque"
                                                wire:model="veiculoReboque" placeholder="Veículo de reboque...">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="row">
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <label for="">Tipo de Transporte</label>
                                <select class="form-control" name="tipoTransporte" wire:model="tipoTransporte" required>
                                    <option value="">Carga própria</option>
                                    <option value="">CT-e golbalizado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="">Nº documento</label>
                                    <input class="form-control" type="number" name="numero" wire:model="numero"
                                        required placeholder="Nº...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="">Série</label>
                                    <input class="form-control" type="number" name="serie" wire:model="serie"
                                        required placeholder="Série...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <div class="row" style="text-align: center">
                                    <div class="col">
                                        <h5>Viagem</h5>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <p><i class="fas fa-map-marker-alt text-dark"></i><b> Local de Carregamento:</b>
                                            {{ $localCarregamento }}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <p><i class="fas fa-map-marker-alt text-info"></i><b> Local de
                                                Descarregamento:</b> {{ $localDescarregamento }}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <p><i class="fas fa-map-marker-alt text-primary"></i><b> Percurso:</b>
                                            {{ $percurso }}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <p><i class="fas fa-calendar-week"></i><b> Data de início da Viagem:</b>
                                            {{ $dataInicio }}</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <div class="row" style="text-align: center">
                                    <div class="col">
                                        <h5>Carga</h5>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-4 col-xs-4">
                                        <i class="fas fa-dollar-sign"></i><label for=""><b> Valor total da Carga:</b></label>
                                    </div>
                                    <div class="col-md-4 col-xs-4">
                                        <input class="form-control" type="number" name="valorTotal" wire:model="valorTotal" required placeholder="Valor total...">
                                    </div>
                                </div><br>

                                <div class="row">
                                    <div class="col-md-4 col-xs-4">
                                        <i class="fas fa-weight-hanging"></i><label for=""><b> Peso total:</b></label>
                                    </div>
                                    <div class="col-md-4 col-xs-4">
                                        <input class="form-control" type="number" name="peso" wire:model="peso" required placeholder="Peso...">
                                    </div>
                                </div><br>

                                <div class="row">
                                    <div class="col-md-5 col-xs-5">
                                        <i class="fas fa-box-open"></i><label for=""><b> Produto predominante:</b></label>
                                    </div>
                                    <div class="col-md-5 col-xs-5">
                                        <input class="form-control" type="text" name="produtoPredominante" wire:model="produtoPredominante" required placeholder="Produto predominante...">
                                    </div>
                                </div><br>

                                <div class="row">
                                    <div class="col-md-4 col-xs-4">
                                        <i class="fas fa-box-open"></i><label for=""><b> Tipo de carga:</b></label>
                                    </div>
                                    <div class="col-md-4 col-xs-4">
                                        <input class="form-control" type="text" name="tipoCarga" wire:model="tipoCarga" required placeholder="Tipo de carga...">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>

    <div class="modal fade bd-add-modal-lg" tabindex="-1" role="dialog" id="meuModal" wire:ignore="true"
        aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static">
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

                    <form wire:submit.prevent="salvarDocumento()">

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
                                <button class="btn btn-success" wire:click="salvarDocumento()"
                                    style="width: 25%">Salvar</button>
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
