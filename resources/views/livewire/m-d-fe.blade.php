<div>

    <form method="POST">
        @csrf

        <main>
            <div class="row">
                <div class="col-md-6 col-xs-12">
                    <div class="row">
                        <div class="col">
                            <div class="card" style="height: 80dvh">
                                <div class="card-header">
                                    <div class="row" style="text-align: center">
                                        <div class="col">
                                            <h5>NFes importadas</h5>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div style="background-color: rgb(230, 230, 230); height: 100%; width: 100%;">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="80%"></th>
                                                    <th width="20%"></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <td>NFe {{ $numeroNFe }}/{{ $serieNFe }}</td>
                                                    <td>
                                                        <div class="row">
                                                            <div class="col">
                                                                <a wire:click.prevent="" title="Editar NFe"
                                                                    class="text-info"><i class="fa fa-edit"></i></a>
                                                            </div>
                                                            <div class="col">
                                                                <a wire:click.prevent="" title="Remover NFe"
                                                                    class="text-danger"><i class="fa fa-trash"></i></a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
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
                                            <h5>Transporte</h5>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="">Veículo de tração *</label>
                                                <select class="form-control" name="veiculoTracao"
                                                    wire:model="veiculoTracao" required>
                                                    <option value="">Selecionar</option>
                                                    @foreach ($veiculosTracao as $veiculoT)
                                                        <option value="{{ $veiculoT->id }}">{{ $veiculoT->placa }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                {{-- <input class="form-control" type="text" name="veiculoTracao"
                                                    wire:model="veiculoTracao" required
                                                    placeholder="Veículo de tração..."> --}}
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="form-group">
                                                <label for="">Motorista *</label>
                                                <select class="form-control" name="motorista" wire:model="motorista"
                                                    required>
                                                    <option value="">Selecionar</option>
                                                    @foreach ($motoristas as $motorista)
                                                        <option value="{{ $motorista->id }}">{{ $motorista->nome }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                {{-- <input class="form-control" type="text" name="motorista"
                                                    wire:model="motorista" required placeholder="Motorista..."> --}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="">Veículo de reboque</label>
                                                <select class="form-control" name="veiculoReboque"
                                                    wire:model="veiculoReboque" required>
                                                    <option value="">Selecionar</option>
                                                    @foreach ($veiculosReboque as $veiculoR)
                                                        <option value="{{ $veiculoR->id }}">{{ $veiculoR->placa }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                {{-- <input class="form-control" type="text" name="veiculoReboque"
                                                    wire:model="veiculoReboque" placeholder="Veículo de reboque..."> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xs-12">
                    <div class="row">
                        <div class="col-md-4 col-xs-6" style="margin-bottom: 2%">
                            <div class="row">
                                <div class="col">
                                    <label for="">Tipo de Transporte</label>
                                    <select class="form-control" name="tipoTransporte" wire:model="tipoTransporte"
                                        required>
                                        <option value="Carga própria">Carga própria</option>
                                        <option value="CT-e golbalizado">CT-e golbalizado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-6">
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
                        <div class="col-md-4 col-xs-6">
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
                                        <div class="col-md-5 col-xs-4">
                                            <i class="fas fa-map-marker-alt" style="margin-right: 2%"></i><label
                                                for=""><b> Local de Carregamento:</b></label>
                                        </div>
                                        <div class="col-md-6 col-xs-4">
                                            <input class="form-control" type="text" name="localCarregamento"
                                                wire:model="localCarregamento" required
                                                placeholder="Local carregamento...">
                                        </div>
                                    </div><br>

                                    <div class="row">
                                        <div class="col-md-6 col-xs-4">
                                            <i class="fas fa-map-marker-alt text-info"
                                                style="margin-right: 2%"></i><label for=""><b> Local de
                                                    Descarregamento:</b></label>
                                        </div>
                                        <div class="col-md-6 col-xs-4">
                                            <input class="form-control" type="text" name="localDescarregamento"
                                                wire:model="localDescarregamento" required
                                                placeholder="Local descarregamento...">
                                        </div>
                                    </div><br>

                                    <div class="row">
                                        <div class="col-md-3 col-xs-4">
                                            <i class="fas fa-map-marker-alt text-primary"
                                                style="margin-right: 2%"></i><label for=""><b>
                                                    Percurso:</b></label>
                                        </div>
                                        <div class="col-md-6 col-xs-4">
                                            <input class="form-control" type="text" name="percurso"
                                                wire:model="percurso" required placeholder="Percurso...">
                                        </div>
                                    </div><br>

                                    <div class="row">
                                        <div class="col-md-4 col-xs-4">
                                            <i class="fas fa-calendar-week" style="margin-right: 2%"></i><label
                                                for=""><b> Data de viagem:</b></label>
                                        </div>
                                        <div class="col-md-6 col-xs-4">
                                            <input class="form-control" type="date" name="dataInicio"
                                                wire:model="dataInicio" required placeholder="Data de viagem...">
                                        </div>
                                    </div><br>

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
                                        <div class="col-md-5 col-xs-4">
                                            <i class="fas fa-dollar-sign" style="margin-right: 2%"></i><label
                                                for=""><b> Valor total da Carga (R$):</b></label>
                                        </div>
                                        <div class="col-md-4 col-xs-4">
                                            <input class="form-control" type="number" name="valorTotal"
                                                wire:model="valorTotal" required placeholder="Valor total...">
                                        </div>
                                    </div><br>

                                    <div class="row">
                                        <div class="col-md-4 col-xs-4">
                                            <i class="fas fa-weight-hanging" style="margin-right: 2%"></i><label
                                                for=""><b> Peso total (Kg):</b></label>
                                        </div>
                                        <div class="col-md-4 col-xs-4">
                                            <input class="form-control" type="number" name="peso"
                                                wire:model="peso" required placeholder="Peso...">
                                        </div>
                                    </div><br>

                                    <div class="row">
                                        <div class="col-md-5 col-xs-5">
                                            <i class="fas fa-box-open" style="margin-right: 2%"></i><label
                                                for=""><b> Produto predominante:</b></label>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <input class="form-control" type="text" name="produtoPredominante"
                                                wire:model="produtoPredominante" required
                                                placeholder="Produto predominante...">
                                        </div>
                                    </div><br>

                                    <div class="row">
                                        <div class="col-md-4 col-xs-4">
                                            <i class="fas fa-box-open" style="margin-right: 2%"></i><label
                                                for=""><b> Tipo de carga:</b></label>
                                        </div>
                                        <div class="col-md-4 col-xs-4">
                                            <select class="form-control" name="tipoCarga" wire:model="tipoCarga"
                                                required>
                                                <option value="">Selecionar</option>
                                                @foreach ($tiposCarga as $tipoC)
                                                    <option value="{{ $tipoC }}">{{ $tipoC }}</option>
                                                @endforeach
                                            </select>
                                            {{-- <input class="form-control" type="text" name="tipoCarga"
                                                wire:model="tipoCarga" required placeholder="Tipo de carga..."> --}}
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" style="text-align: center; margin-bottom: 2%">
                <div class="col">
                    <a class="btn btn-secondary" href="{{ route('mdfe.index') }}">Cancelar</a>
                    <button class="btn btn-success" type="submit" style="width: 25%">Concluir</button>
                </div>
            </div>
        </main>

    </form>

    <div class="modal fade bd-add-modal-lg" tabindex="-1" role="dialog" id="meuModal" wire:ignore="true" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static">
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

                    <form wire:submit.prevent="salvarDocumento">

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
                                        oninput="limitarCaracteres(this, 44)" placeholder="Chave...">
                                </div>
                            </div>
                        </div>

                        <div class="row" style="text-align: center">
                            <div class="col">
                                <button class="btn btn-success" type="submit" style="width: 25%">Salvar</button>
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

    document.addEventListener('livewire:load', function () {
        Livewire.on('fecharModal', function () {
            $('#meuModal').modal('hide');
        });
    });

    function limitarCaracteres(elemento, limite) {
        let valor = elemento.value.toString();
        if (valor.length > limite) {
            valor = valor.slice(0, limite);
            elemento.value = valor;
        }
    }
</script>