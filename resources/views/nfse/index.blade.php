@extends('adminlte::page')

@section('title', 'NFS-e')

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark">Notas Fiscais de Serviço (NFS-e)</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right">
            <a class="btn btn-info" href="{{ route('nfse.create') }}">
                <i class="fas fa-plus mr-1"></i> Emitir NFS-e
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-body p-0">
        @component('components.dataTable', [
            'responsive' => true,
            'searching' => true,
            'lengthChange' => true,
            'pageLength' => 10,
            'ordering' => true,
            'showFooter' => false,
        ])
            <thead class="table-light">
                <tr>
                    <th>Nº/Série</th>
                    <th>DPS</th>
                    <th>Competência</th>
                    <th class="text-left">Tomador</th>
                    <th>Serviço</th>
                    <th>Situação</th>
                    <th>Valor</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($nfses as $nfse)
                    @php($transmissaoInconclusiva = $nfse->situacao === 'Pendente' && $nfse->cStat === 'PENDENTE')
                    <tr>
                        <td>{{ $nfse->nro ?: '-' }}/{{ $nfse->serie ?: '-' }}</td>
                        <td>{{ $nfse->nDPS ?: '-' }}/{{ $nfse->serieDPS ?: '-' }}</td>
                        <td>{{ optional($nfse->data_competencia)->format('d/m/Y') ?: '-' }}</td>
                        <td class="text-left">{{ $nfse->cliente->nome ?? '-' }}</td>
                        <td>{{ $nfse->cTribNac ?: '-' }}</td>
                        <td>
                            @if ($nfse->situacao === 'Autorizado')
                                <span class="badge badge-success">{{ $nfse->situacao }}</span>
                            @elseif ($nfse->situacao === 'Cancelado' || $nfse->situacao === 'Rejeitado')
                                <span class="badge badge-danger">{{ $nfse->situacao }}</span>
                            @else
                                <span class="badge badge-warning">{{ $nfse->situacao }}</span>
                            @endif
                            @if ($nfse->xMotivo)
                                <div class="small text-muted text-left mt-1" title="{{ $nfse->xMotivo }}">
                                    {{ \Illuminate\Support\Str::limit($nfse->xMotivo, 80) }}
                                </div>
                            @endif
                        </td>
                        <td>R$ {{ number_format($nfse->vLiq, 2, ',', '.') }}</td>
                        <td class="text-right">
                            <a title="Visualizar" href="{{ route('nfse.view', [$nfse->id]) }}" class="mx-1">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if ($nfse->xmlAutorizado || $nfse->xmlDps)
                                <a title="Baixar XML" href="{{ route('nfse.downloadXml', [$nfse->id]) }}" class="mx-1">
                                    <i class="far fa-file-code"></i>
                                </a>
                            @endif
                            @if ($nfse->chave && in_array($nfse->situacao, ['Autorizado', 'Cancelado']))
                                <a title="Baixar DANFSe" href="{{ route('nfse.downloadDanfse', [$nfse->id]) }}" class="mx-1">
                                    <i class="far fa-file-pdf"></i>
                                </a>
                            @endif
                            @if (in_array($nfse->situacao, ['Pendente', 'Rejeitado', 'Rascunho']) && ! $transmissaoInconclusiva)
                                <a title="Editar" href="{{ route('nfse.edit', [$nfse->id]) }}" class="mx-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('nfse.enviar', [$nfse->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Enviar essa NFS-e?')">
                                    @csrf
                                    <button type="submit" title="Enviar" class="btn btn-link p-0 mx-1">
                                        <i class="fas fa-paper-plane text-success"></i>
                                    </button>
                                </form>
                                <button type="button" title="Excluir" class="btn btn-link p-0 mx-1" data-toggle="modal" data-target="#deleteModal{{ $nfse->id }}">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            @endif
                            @if ($transmissaoInconclusiva)
                                <form action="{{ route('nfse.reconciliar', [$nfse->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" title="Sincronizar com a SEFIN" class="btn btn-link p-0 mx-1">
                                        <i class="fas fa-sync-alt text-warning"></i>
                                    </button>
                                </form>
                            @endif
                            @if ($nfse->situacao === 'Autorizado')
                                <button type="button" title="Cancelar" class="btn btn-link p-0 mx-1" data-toggle="modal" data-target="#cancelModal{{ $nfse->id }}">
                                    <i class="fas fa-ban text-danger"></i>
                                </button>
                            @endif
                        </td>
                    </tr>

                    @component('components.modal', [
                        'modalId' => 'deleteModal' . $nfse->id,
                        'modalTitle' => 'Excluir NFS-e',
                        'sizeModal' => 'modal-sm',
                    ])
                        <form action="{{ route('nfse.delete') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="nfse_id" value="{{ $nfse->id }}">
                            <p>Tem certeza que deseja excluir essa NFS-e?</p>
                            <div class="text-right">
                                <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                            </div>
                        </form>
                    @endcomponent

                    @component('components.modal', [
                        'modalId' => 'cancelModal' . $nfse->id,
                        'modalTitle' => 'Cancelar NFS-e',
                        'sizeModal' => 'modal-md',
                    ])
                        <form action="{{ route('nfse.cancel') }}" method="POST">
                            @csrf
                            <input type="hidden" name="nfse_id" value="{{ $nfse->id }}">
                            <div class="form-group">
                                <label>Motivo</label>
                                <select class="form-control" name="codigo_motivo" required>
                                    <option value="1">1 - Erro na emissão</option>
                                    <option value="2">2 - Serviço não prestado</option>
                                    <option value="9">9 - Outros</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Justificativa (mínimo 15 caracteres)</label>
                                <textarea class="form-control" name="justificativa" minlength="15" maxlength="255" required></textarea>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-danger">Confirmar Cancelamento</button>
                            </div>
                        </form>
                    @endcomponent
                @endforeach
            </tbody>
        @endcomponent
    </div>
</div>
@stop
