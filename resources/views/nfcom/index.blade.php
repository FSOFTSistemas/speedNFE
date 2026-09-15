@extends('adminlte::page')

@section('title', 'NFCom')

@push('css')
<style>
    :root {
        --primary-color: #00033a;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --info-color: #17a2b8;
        --success-color: #28a745;
    }
    .card-main {
        background: var(--card-bg);
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px var(--shadow-color);
        padding: 30px;
    }
    .custom-btn { font-weight: 500; border-radius: 8px; }
    .custom-btn-info { background-color: var(--info-color) !important; border-color: var(--info-color) !important; color: #fff !important; }
    .action-buttons a, .action-buttons button { color: #6c757d; margin: 0 6px; font-size: 1.1rem; background: none; border: none; }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark">Notas Fiscais de Comunicação (NFCom)</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right">
            <a class="btn custom-btn custom-btn-info" href="{{ route('nfcom.create') }}">
                <i class="fas fa-upload mr-1"></i> Emitir NFCom
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="card card-main">
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
                    <th>Data</th>
                    <th class="text-left">Assinante</th>
                    <th>Situação</th>
                    <th>Valor</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($nfcoms as $nfcom)
                    <tr>
                        <td>{{ $nfcom->nro }}/{{ $nfcom->serie }}</td>
                        <td>{{ \Carbon\Carbon::parse($nfcom->data)->format('d/m/Y') }}</td>
                        <td class="text-left">{{ $nfcom->cliente->nome ?? '-' }}</td>
                        <td>
                            @if ($nfcom->situacao->value === 'Cancelado')
                                <span class="badge badge-danger">{{ $nfcom->situacao->value }}</span>
                            @elseif ($nfcom->situacao->value === 'Autorizado')
                                <span class="badge badge-success">{{ $nfcom->situacao->value }}</span>
                            @elseif ($nfcom->situacao->value === 'Rejeitado')
                                <span class="badge badge-danger">{{ $nfcom->situacao->value }}</span>
                            @else
                                <span class="badge badge-warning">{{ $nfcom->situacao->value }}</span>
                            @endif
                        </td>
                        <td>R$ {{ number_format($nfcom->vNF, 2, ',', '.') }}</td>
                        <td class="action-buttons text-right">
                            <a title="Visualizar (PDF)" target="_blank" href="{{ route('nfcom.view', [$nfcom->id]) }}">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            @if ($nfcom->situacao->value === 'Autorizado')
                                <a title="Baixar XML" href="{{ route('nfcom.downloadXml', [$nfcom->id]) }}">
                                    <i class="far fa-file-code"></i>
                                </a>
                                <button type="button" title="Cancelar" data-toggle="modal" data-target="#cancelModal{{ $nfcom->id }}">
                                    <i class="fas fa-ban text-danger"></i>
                                </button>
                            @endif
                            @if ($nfcom->situacao->value === 'Pendente' || $nfcom->situacao->value === 'Rejeitado')
                                <a title="Editar" href="{{ route('nfcom.edit', [$nfcom->id]) }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a title="Enviar" href="{{ route('nfcom.enviar', [$nfcom->id]) }}" onclick="return confirm('Enviar essa NFCom para a SEFAZ?')">
                                    <i class="fas fa-paper-plane text-success"></i>
                                </a>
                                <button type="button" title="Excluir" data-toggle="modal" data-target="#deleteModal{{ $nfcom->id }}">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            @endif
                        </td>
                    </tr>

                    @if ($nfcom->situacao->value === 'Autorizado')
                        @component('components.modal', [
                            'modalId' => 'cancelModal' . $nfcom->id,
                            'modalTitle' => 'Cancelar NFCom #' . $nfcom->nro,
                            'sizeModal' => 'modal-md',
                        ])
                            <form action="{{ route('nfcom.cancel') }}" method="POST">
                                @csrf
                                <input type="hidden" name="nfcom_id" value="{{ $nfcom->id }}">
                                <div class="form-group">
                                    <label for="justificativa{{ $nfcom->id }}">Justificativa (mínimo 15 caracteres)</label>
                                    <textarea class="form-control" id="justificativa{{ $nfcom->id }}" name="justificativa" minlength="15" required></textarea>
                                </div>
                                <div class="text-right mt-2">
                                    <button type="submit" class="btn btn-danger">Confirmar Cancelamento</button>
                                </div>
                            </form>
                        @endcomponent
                    @endif

                    @if ($nfcom->situacao->value === 'Pendente' || $nfcom->situacao->value === 'Rejeitado')
                        @component('components.modal', [
                            'modalId' => 'deleteModal' . $nfcom->id,
                            'modalTitle' => 'Excluir NFCom #' . $nfcom->nro,
                            'sizeModal' => 'modal-sm',
                        ])
                            <form action="{{ route('nfcom.delete') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="nfcom_id" value="{{ $nfcom->id }}">
                                <p>Tem certeza que deseja excluir essa NFCom?</p>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                                </div>
                            </form>
                        @endcomponent
                    @endif
                @endforeach
            </tbody>
        @endcomponent
    </div>
</div>
@stop
