@extends('adminlte::page')

@section('title', 'Fluxo de Caixa')

@section('content_header')
@stop

@section('content')
    <div class="row" style="padding-top: 1%; text-align: right;">
        <div class="col">
            <!-- Botão de Novo Lançamento -->
            <button class="btn btn-primary" style="margin-bottom: 1%;" data-toggle="modal" data-target="#createModal">
                <i class="fas fa-plus"></i>&nbsp;Novo Lançamento
            </button>

            <!-- Botão CRUD Plano de Contas com link para a rota contas.index -->
            <a href="{{ route('contas.index') }}" class="btn btn-secondary" style="margin-bottom: 1%; margin-left: 10px;">
                <i class="fas fa-list-alt"></i>&nbsp;Plano de Contas
            </a>
        </div>
    </div>

    @component('components.dataTable', [
        'responsive' => [
            ['responsivePriority' => 1, 'targets' => 0],
            ['responsivePriority' => 2, 'targets' => 1],
            ['responsivePriority' => 3, 'targets' => 2],
            ['responsivePriority' => 4, 'targets' => 3],
            ['responsivePriority' => 5, 'targets' => 4],
            ['responsivePriority' => 6, 'targets' => 5],
            ['responsivePriority' => 7, 'targets' => -1],
        ],
        'searching' => true,
        'lengthChange' => true,
        'pageLength' => 25,
        'ordering' => false,
        'showFooter' => true,
        'sumColumnIndex' => 2,
    ])
        <thead class="table-primary" style="width: 100%">
            <tr>
                <th>Descrição</th>
                <th>Plano de Contas</th>
                <th>Valor</th>
                <th>Tipo</th>
                <th>Data</th>
                <th></th>

            </tr>
        </thead>
        <tbody>
            @foreach ($lancamentos as $lancamento)
                <tr>
                    <td>{{ $lancamento->descricao }}</td>
                    <td>{{ $lancamento->planoDeContas->descricao ?? 'Não informado' }}</td>
                    <td>{{ number_format($lancamento->valor, 2, ',', '.') }}</td>
                    <td>
                        @if ($lancamento->tipo == 'Entrada')
                            <span class="badge badge-success">Entrada</span>
                        @elseif ($lancamento->tipo == 'Saída')
                            <span class="badge badge-danger">Saída</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($lancamento->tipo) }}</span>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($lancamento->data)->format('d/m/Y') }}</td>
                    <td>
                        <div class="row">
                            <div class="col">
                                <a title="Editar" href="#" class="text-warning" data-toggle="modal"
                                    data-target="#modalEditFluxo{{ $lancamento->id }}">
                                    <i class="fa fa-edit"></i>
                                </a>
                            </div>
                            <div class="col">
                                <a title="Excluir" href="{{ route('fluxo-caixa.destroy', [$lancamento->id]) }}"
                                    class='text-danger' data-toggle="modal" data-target="#deleteModal{{ $lancamento->id }}"><i
                                        class="fa fa-trash"></i></a>
                            </div>
                        </div>
                    </td>
                </tr>
                <div class="modal fade" id="deleteModal{{ $lancamento->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-md modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4>Apagar este Lançamento?</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <p style="color: red;">OBS: Você irá excluir todas as informações sobre este lançamento!</p>
                                <form action="{{ route('fluxo-caixa.destroy', $lancamento->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">EXCLUIR</button>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>

                @include('fluxodecaixa.modals.edit')
            @endforeach
        </tbody>
    @endcomponent

    <!-- Modal de Criação -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Novo Lançamento</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('fluxo-caixa.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="plano_de_contas_id">Plano de Contas</label>
                            <select class="form-control" name="plano_de_contas_id" id="plano_de_contas_id" required>
                                <option value="">Selecione</option>
                                @foreach ($planosDeContas as $plano)
                                    <option value="{{ $plano->id }}">{{ $plano->descricao }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <input type="text" class="form-control" id="descricao" name="descricao" required>
                        </div>
                        <div class="form-group">
                            <label for="valor">Valor</label>
                            <input type="number" class="form-control" id="valor" name="valor" step="0.01"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="tipo">Tipo</label>
                            <select class="form-control" id="tipo" name="tipo">
                                <option value="Entrada">Entrada</option>
                                <option value="Saida">Saída</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="data">Data</label>
                            <input type="date" class="form-control" id="data" name="data" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Salvar</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
@stop
