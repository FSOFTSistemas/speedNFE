<div>

    <form action="{{ route('mdfe.update', [$MDFe->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <main>
            <div class="row">
                <div class="col-md-6 col-xs-12">
                    <div class="row">
                        <div class="col">
                            <div class="card" style="height: 80dvh">
                                <div class="card-header">
                                    <div class="row" style="text-align: center">
                                        <div class="col">
                                            <h5>Notas importadas</h5>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div
                                        style="background-color: rgb(230, 230, 230); height: 85%; width: 100%; max-height: 85%; overflow-y: auto;">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="50%"></th>
                                                    <th width="30%"></th>
                                                    <th width="20%"></th>
                                                </tr>
                                            </thead>

                                            <tbody style="font-size: 70%">
                                                @foreach ($notas as $index => $nt)
                                                    <tr>
                                                        <td>{{ $nt['tipoDocumento'] }}
                                                            {{ $nt['numeroNota'] }}/{{ $nt['serieNota'] }}</td>
                                                        <td>{{ $nt['cidade'] }}/{{ $nt['ufNota'] }}
                                                        </td>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col">
                                                                    <a wire:click.prevent="editNote({{ json_encode($nt) }}, {{ $index }})"
                                                                        title="Editar {{ $nt['tipoDocumento'] }}"
                                                                        class="text-info"><i class="fa fa-edit"></i></a>
                                                                </div>
                                                                @if (count($notas) > 1)
                                                                    <div class="col">
                                                                        <a wire:click.prevent="deleteNote({{ $index }})"
                                                                            title="Remover {{ $nt['tipoDocumento'] }}"
                                                                            class="text-danger"><i
                                                                                class="fa fa-trash"></i></a>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        @foreach ($notas as $index => $nt)
                                            <input type="hidden" name="notas[{{ $index }}][nota_id]"
                                                wire:model="notas.{{ $index }}.nota_id">
                                            <input type="hidden" name="notas[{{ $index }}][tipoDocumento]"
                                                wire:model="notas.{{ $index }}.tipoDocumento">
                                            <input type="hidden" name="notas[{{ $index }}][ufNota]"
                                                wire:model="notas.{{ $index }}.ufNota">
                                            <input type="hidden" name="notas[{{ $index }}][cidade]"
                                                wire:model="notas.{{ $index }}.cidade">
                                            <input type="hidden" name="notas[{{ $index }}][codMun]"
                                                wire:model="notas.{{ $index }}.codMun">
                                            <input type="hidden" name="notas[{{ $index }}][valor]"
                                                wire:model="notas.{{ $index }}.valor">
                                            <input type="hidden" name="notas[{{ $index }}][peso]"
                                                wire:model="notas.{{ $index }}.peso">
                                            <input type="hidden" name="notas[{{ $index }}][chave]"
                                                wire:model="notas.{{ $index }}.chave">
                                            <input type="hidden" name="notas[{{ $index }}][serieNota]"
                                                wire:model="notas.{{ $index }}.serieNota">
                                            <input type="hidden" name="notas[{{ $index }}][numeroNota]"
                                                wire:model="notas.{{ $index }}.numeroNota">
                                        @endforeach

                                        @foreach ($numeroNotas as $index => $nNotas)
                                            <input type="hidden" name="numeroNotas.[{{ $index }}]"
                                                wire:model='numeroNotas.{{ $index }}'>
                                        @endforeach

                                    </div>

                                    <div class="row" style="text-align: center">
                                        <div class="col">
                                            <a class="btn btn-dark" wire:click="addNote()"
                                                style="margin-top: 2%;">Importar mais Notas <i
                                                    class="fas fa-upload"></i></a>
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
                                            <h5>Transporte</h5>
                                            <p class="text-danger">Modal Rodoviário <i class="fa fa-car"></i></p>
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
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="form-group">
                                                <label for="">Motorista *</label>
                                                <select class="form-control" wire:model="motorista"
                                                    wire:change="addMotorista()">
                                                    <option value="">Selecionar</option>
                                                    @foreach ($motoristasDisponiveis as $motorista)
                                                        <option value="{{ $motorista->id . '/' . $motorista->nome }}">
                                                            {{ $motorista->nome }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <ul style="list-style: circle;">
                                                    @foreach ($motoristas as $index => $motorista)
                                                        <li>
                                                            <div class="row">
                                                                <div class="col">
                                                                    <b>{{ explode('/', $motorista)[1] }}</b>
                                                                </div>
                                                                <div class="col" style="text-align: center">
                                                                    <i class="fa fa-trash" title="Remover"
                                                                        wire:click="removeMotorista({{ $index }})"></i>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                            @foreach ($motoristas as $index => $mtr)
                                                <input type="hidden" required name="motoristas[{{ $index }}]"
                                                    wire:model="motoristas.{{ $index }}">
                                            @endforeach

                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-xs-6">
                                            <div class="form-group">
                                                <label for="">Veículo de reboque</label>
                                                <select class="form-control" wire:model="veiculoReboque"
                                                    wire:change="addReboque()">
                                                    <option value="">Selecionar</option>
                                                    @foreach ($veiculosReboqueDisponiveis as $veiculoR)
                                                        <option value="{{ $veiculoR->id . '/' . $veiculoR->placa }}">
                                                            {{ $veiculoR->placa }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <ul style="list-style: circle;">
                                                    @foreach ($reboques as $index => $rbq)
                                                        <li>
                                                            <div class="row">
                                                                <div class="col">
                                                                    <b>{{ explode('/', $rbq)[1] }}</b>
                                                                </div>
                                                                <div class="col" style="text-align: center">
                                                                    <i class="fa fa-trash" title="Remover"
                                                                        wire:click="removeReboque({{ $index }})"></i>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                            @foreach ($reboques as $index => $rbq)
                                                <input type="hidden" name="reboques[{{ $index }}]"
                                                    wire:model="reboques.{{ $index }}">
                                            @endforeach
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
                                        <option value="CT-e globalizado">CT-e globalizado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-6">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="">Nº documento</label>
                                        <input class="form-control" type="text" name="numero" min="0"
                                            wire:model="numero" required placeholder="Nº...">
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
                                            min="0" required placeholder="Série...">
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
                                        <div class="col-md-2 col-xs-4">
                                            <select class="form-control" name="localCarregamento"
                                                wire:model="localCarregamento" wire:change="buscarCidades(false)" required>
                                                @foreach ($ufs as $uf)
                                                    <option value="{{ $uf }}">{{ $uf }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5 col-xs-4">
                                            <select class="form-control" wire:model="carregamento" wire:change="carregamento()" required>
                                                @foreach ($cidadesCarregamento as $city)
                                                    <option value="{{ $city }}">{{ $city->cidade }}</option>
                                                @endforeach
                                            </select>

                                            <input class="form-control" type="hidden" name="codMunCarregamento" wire:model="codMunCarregamento" required placeholder="Cód. Município...">

                                            <input class="form-control" type="hidden" name="municipio" wire:model="municipio" required>
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
                                                readonly wire:model="localDescarregamento" required
                                                placeholder="Local descarregamento...">
                                        </div>
                                    </div><br>

                                    <div class="row">
                                        <div class="col-md-3 col-xs-4">
                                            <i class="fas fa-map-marker-alt text-primary"
                                                style="margin-right: 2%"></i><label for=""><b>
                                                    Percurso:</b></label>
                                        </div>
                                        <div class="col-md-9 col-xs-4">
                                            <div class="row">
                                                <div class="col-md-11 col-xs-6">
                                                    <input class="form-control" type="text"
                                                        value="{{ implode(' - ', $this->percursos) }}" readonly>
                                                    @foreach ($percursos as $index => $pcs)
                                                        <input type="hidden" name="percursos[{{ $index }}]"
                                                            wire:model="percursos.{{ $index }}" required>
                                                    @endforeach
                                                </div>

                                                <div class="col-md-1 col-xs-6">
                                                    <a title="Adicionar ou Remover Percurso" data-toggle="modal" wire:click="percursos()"
                                                        data-target="#modalPercurso">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </div>
                                            </div>

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
                                                min="0" wire:model="valorTotal" required
                                                placeholder="Valor total...">
                                        </div>
                                    </div><br>

                                    <div class="row">
                                        <div class="col-md-4 col-xs-4">
                                            <i class="fas fa-weight-hanging" style="margin-right: 2%"></i><label
                                                for=""><b> Peso total (Kg):</b></label>
                                        </div>
                                        <div class="col-md-4 col-xs-4">
                                            <input class="form-control" type="number" name="pesoTotal"
                                                min="0" wire:model="pesoTotal" required placeholder="Peso...">
                                        </div>
                                    </div><br>

                                    <div class="row">
                                        <div class="col-md-5 col-xs-5">
                                            <i class="fas fa-box-open" style="margin-right: 2%"></i><label
                                                for=""><b> Produto predominante:</b></label>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <input class="form-control" type="hidden" name="prodPred_id" wire:model="prodPred_id" required>
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
                    <a class="btn btn-info" data-toggle="modal" data-target="#modalMoreOptions">Mais Opções</a>
                    <a class="btn btn-secondary" href="{{ route('mdfe.index') }}">Voltar</a>
                    <button class="btn btn-success" type="submit" style="width: 25%">Concluir</button>
                </div>
            </div>
        </main>

        @component('components.modal', [
            'modalId' => 'modalMoreOptions',
            'modalTitle' => 'Mais Opções',
            'sizeModal' => 'modal-lg',
        ])
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="content">
                            <div class="container-fluid">
                                <div class="col-xs-12 col-sm-12" style="width: 100%">

                                    <div class="card card-primary card-outline card-tabs">

                                        <div class="card-header p-0 pt-1 border-bottom-0">
                                            <ul class="nav nav-tabs" id="tab" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link" id="prod-tab" data-toggle="pill" href="#prod"
                                                        role="tab" aria-controls="prod" aria-selected="false">Produto
                                                        Predominante</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="home-tab" data-toggle="pill"
                                                        href="#home" role="tab" aria-controls="home"
                                                        aria-selected="true">Observações</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="lacres-tab" data-toggle="pill"
                                                        href="#lacres" role="tab" aria-controls="lacres"
                                                        aria-selected="false">Lacres</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card-body">
                                            <div class="tab-content" id="tabContent">
                                                <div class="tab-pane fade show active" id="home" role="tabpanel"
                                                    aria-labelledby="home-tab">
                                                    <div class="row">
                                                        <div class="col">
                                                            <h4>Informações Adicionais</h4>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col">
                                                            <label for="">Informações de interesse ao
                                                                fisco</label>
                                                            <textarea class="form-control" name="info_fisco" wire:model="info_fisco" maxlength="255" cols="10"
                                                                rows="5" placeholder="Informações ao fisco..."></textarea>
                                                        </div>

                                                        <div class="col">
                                                            <label for="">Informações de interesse ao
                                                                contribuinte</label>
                                                            <textarea class="form-control" name="info_contribuinte" wire:model="info_contribuinte" maxlength="255"
                                                                cols="10" rows="5" placeholder="Informações ao contribuinte..."></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="lacres" role="tabpanel"
                                                    aria-labelledby="lacres-tab">
                                                    <div class="row">
                                                        <div class="col">
                                                            <h4>Lacres</h4>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col">
                                                            <label for="">Número</label>
                                                            <input class="form-control" type="text" name="numeroLacre"
                                                                wire:model="numeroLacre" placeholder="Número do lacre...">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="prod" role="tabpanel"
                                                    aria-labelledby="prod-tab">
                                                    <div class="row">
                                                        <div class="col">
                                                            <h4>Produto Predominante</h4>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col">
                                                            <label for="">Código GTIN</label>
                                                            <input class="form-control" type="text" name="codigo_gtin"
                                                                wire:model="codGTIN" placeholder="Código GTIN...">
                                                        </div>

                                                        <div class="col">
                                                            <label for="">Código NCM</label>
                                                            <input class="form-control" type="text" name="ncm"
                                                                wire:model="codNCM" placeholder="Ncm...">
                                                        </div>
                                                    </div>


                                                    <div class="row">
                                                        <div class="col">
                                                            <label for="">Latitude local de
                                                                Carregamento</label>
                                                            <input class="form-control" type="number"
                                                                name="lat_carregamento" required
                                                                wire:model="latCarregamento"
                                                                placeholder="Latitude do local de Carregamento...">
                                                        </div>

                                                        <div class="col">
                                                            <label for="">Longitude local de
                                                                Carregamento</label>
                                                            <input class="form-control" type="number"
                                                                name="lon_carregamento" required
                                                                wire:model="lonCarregamento"
                                                                placeholder="Longitude do local de Carregamento...">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col">
                                                            <label for="">Latitude e Longitude de Descarregamento</label>
                                                            <select class="form-control" wire:model="selectedLatLon" wire:change="descarregamento" required>
                                                                @foreach ($cidadesDescarregamentoLatLon as $city)
                                                                    <option value="{{ $city->lat . '@' . $city->lon }}" @if($city->lat == $latDescarregamento && $city->lon == $lonDescarregamento) selected @endif>{{ $city->ibge }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <input type="hidden" name="lat_descarregamento" wire:model="latDescarregamento" required>
                                                    <input type="hidden" name="lon_descarregamento" wire:model="lonDescarregamento" required>

                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcomponent
    </form>

    <div class="modal fade bd-add-modal-lg" tabindex="-1" role="dialog" id="meuModal" wire:ignore="true"
        aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
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
                                    <select class="form-control" wire:model="tipoDocumento" required>
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
                                    <select class="form-control" wire:model="localDescarregamento" id="meuSelect" required>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-5 col-xs-5">
                                <div class="form-group">
                                    <label for="">Cidade *</label>
                                    <select class="form-control" wire:model="cidade" id="cidade" required>
                                        <option value="">Selecionar</option>
                                        @foreach ($cidadesDescarregamento as $city)
                                            <option value="{{ $city->cidade . '@' . $city->ibge }}">{{ $city->cidade }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="">Valor total *</label>
                                    <input class="form-control" type="number" step="0.01" min="0"
                                        wire:model="valor" required placeholder="Valor...">
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
                                    <span class="text-danger" style="display: none;" id="chaveInvalida"></span>
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

                <div class="modal-footer" id="cancelar">
                    <a class="btn btn-secondary" data-dismiss="modal">Cancelar</a>
                </div>
            </div>
        </div>
    </div>

    @component('components.modal', [
        'modalId' => 'modalPercurso',
        'modalTitle' => 'Adicionar ou Remover Percurso',
        'sizeModal' => 'modal-md',
    ])
        <form wire:submit.prevent="addPercurso">
            <div class="row">
                <div class="col">
                    <label for="">Percurso</label>
                    <select class="form-control" name="percurso" wire:model="percurso" required>
                        <option value="">Selecionar</option>
                        @foreach ($ufs as $uf)
                            <option value="{{ $uf }}">{{ $uf }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <span class="text-danger" style="display: none;" id="percursoInvalido"></span>

            <div class="row" style="text-align: center; margin-top: 2%;">
                <div class="col">
                    <button class="btn"><i class="fa fa-plus"> adicionar</i></button>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col">
                <ul id="percursos" style="list-style: circle;">

                </ul>
            </div>
        </div>

        <div class="row" style="text-align: center">
            <div class="col">
                <button wire:click="removePercurso()" class="btn"><i class="fa fa-minus"> remover</i></button>
            </div>
        </div>
    @endcomponent

    @if (count($notas) >= 1)
        <div class="modal fade bd-edit-modal-lg" tabindex="-1" role="dialog" id="meuModalEdit" wire:ignore="true"
            aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">

                        <div class="col" style="text-align: center">
                            <div class="modal-title" style="text: center">
                                <h4>Editar documento</h4>
                            </div>
                        </div>

                    </div>
                    <div class="modal-body">

                        <form wire:submit.prevent="updateNote">

                            <div class="row">
                                <div class="col-md-3 col-xs-3">
                                    <div class="form-group">
                                        <label for="">Tipo de Documento *</label>
                                        <select class="form-control" wire:model="tipoDocumento" required>
                                            @foreach ($tiposDocumentos as $tipoDocumento)
                                                <option value="{{ $tipoDocumento }}">{{ $tipoDocumento }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4 col-xs-4">
                                    <div class="form-group">
                                        <label for="">Local de descarregamento *</label>
                                        <input class="form-control" type="text" wire:model="localDescarregamento"
                                            readonly>
                                    </div>
                                </div>

                                <div class="col-md-5 col-xs-5">
                                    <div class="form-group">
                                        <label for="">Cidade *</label>
                                        <select class="form-control" wire:model="cidade" required>
                                            @foreach (json_decode($cidadesDescarregamento) as $city)
                                                <option value="{{ $city->cidade . '@' . $city->ibge }}">
                                                    {{ $city->cidade }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="">Valor total *</label>
                                        <input class="form-control" type="number" step="0.01" min="0"
                                            wire:model="valor" required placeholder="Valor...">
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
                                        <span class="text-danger" style="display: none;"
                                            id="chaveInvalidaEdit"></span>
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
                        <button class="btn btn-secondary" wire:click="cancelEdit()">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>


<script>
    document.addEventListener('livewire:load', function() {
        Livewire.on('fecharModal', function() {
            $('#meuModal').modal('hide');
        });

        Livewire.on('abrirModal', function() {
            $('#meuModal').modal('show');
        });

        Livewire.on('abrirModalEdit', function() {
            $('#meuModalEdit').modal('show');
        });

        Livewire.on('fecharModalEdit', function() {
            $('#meuModalEdit').modal('hide');
        });

        Livewire.on('cidades', function(value) {
            var select = document.getElementById('cidade');
            select.innerHTML = '';
            var option1 = document.createElement('option');
            option1.value = '';
            option1.text = 'Selecionar';
            select.add(option1);
            value.forEach(element => {
                var option2 = document.createElement('option');
                option2.value = element.cidade + '@' + element.ibge;
                option2.text = element.cidade;
                select.add(option2);
            });
        });

        Livewire.on('percursos', function(value) {
            var percursos = document.getElementById('percursos');
            percursos.innerHTML = '';
            value.forEach(element => {
                var li = document.createElement('li');
                li.textContent = element;
                percursos.appendChild(li);
            });
        });

        Livewire.on('atualizarSelect', function(value) {
            var select = document.getElementById('meuSelect');
            select.innerHTML = '';
            select.readonly = true;
            var option = document.createElement('option');
            option.value = value;
            option.text = value;
            select.add(option);
        });

        Livewire.on('chaveJaExiste', function() {
            let errorBox = document.getElementById('chaveInvalida');
            let errorBox2 = document.getElementById('chaveInvalidaEdit');
            errorBox.innerHTML =
                "<i class='fas fa-exclamation-circle'></i> Chave já utilizada, tente com uma nova chave!";
            errorBox.style.display = 'block';
            errorBox2.innerHTML =
                "<i class='fas fa-exclamation-circle'></i> Chave já utilizada, tente com uma nova chave!";
            errorBox2.style.display = 'block';
            setTimeout(function() {
                errorBox.style.display = 'none';
                errorBox2.style.display = 'none';
            }, 3000);
        });

        Livewire.on('percursoInvalido', function() {
            let errorBox = document.getElementById('percursoInvalido');
            errorBox.innerHTML =
                "<i class='fas fa-exclamation-circle'></i> O Local de Carregamento e o Local de Descarregamento não podem estar inclusos no percurso!";
            errorBox.style.display = 'block';
            setTimeout(function() {
                errorBox.style.display = 'none';
            }, 3000);
        });

        Livewire.on('chaveInvalida', function() {
            let errorBox = document.getElementById('chaveInvalida');
            let errorBox2 = document.getElementById('chaveInvalidaEdit');
            errorBox.innerHTML =
                "<i class='fas fa-exclamation-circle'></i> Chave inválida, tente com uma chave válida!";
            errorBox.style.display = 'block';
            errorBox2.innerHTML =
                "<i class='fas fa-exclamation-circle'></i> Chave inválida, tente com uma chave válida!";
            errorBox2.style.display = 'block';
            setTimeout(function() {
                errorBox.style.display = 'none';
                errorBox2.style.display = 'none';
            }, 3000);
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
