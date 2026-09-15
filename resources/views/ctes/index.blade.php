@extends('adminlte::page')

@section('title', 'CTe')

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
            <h1 class="m-0 text-dark">Conhecimentos de Transporte (CTe)</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right">
            <a class="btn custom-btn custom-btn-info" href="{{ route('cte.create') }}">
                <i class="fas fa-upload mr-1"></i> Emitir CTe
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
                    <th class="text-left">Remetente</th>
                    <th class="text-left">Destinatário</th>
                    <th>Situação</th>
                    <th>Valor</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ctes as $cte)
                    <tr>
                        <td>{{ $cte->numero }}/{{ $cte->serie }}</td>
                        <td>{{ \Carbon\Carbon::parse($cte->data)->format('d/m/Y') }}</td>
                        <td class="text-left">{{ $cte->remetente->nome ?? '-' }}</td>
                        <td class="text-left">{{ $cte->destinatario->nome ?? '-' }}</td>
                        <td>
                            @if ($cte->situacao->value === 'Cancelado')
                                <span class="badge badge-danger">{{ $cte->situacao->value }}</span>
                            @elseif ($cte->situacao->value === 'Autorizado')
                                <span class="badge badge-success">{{ $cte->situacao->value }}</span>
                            @elseif ($cte->situacao->value === 'Rejeitado')
                                <span class="badge badge-danger">{{ $cte->situacao->value }}</span>
                            @else
                                <span class="badge badge-warning">{{ $cte->situacao->value }}</span>
                            @endif
                        </td>
                        <td>R$ {{ number_format($cte->vTPrest, 2, ',', '.') }}</td>
                        <td class="action-buttons text-right">
                            @if ($cte->situacao->value === 'Autorizado')
                                <a title="Visualizar (DACTE)" target="_blank" href="{{ route('cte.view', [$cte->id]) }}">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <a title="Baixar XML" href="{{ route('cte.downloadXml', [$cte->id]) }}">
                                    <i class="far fa-file-code"></i>
                                </a>
                                <button type="button" title="Cancelar" data-toggle="modal" data-target="#cancelModal{{ $cte->id }}">
                                    <i class="fas fa-ban text-danger"></i>
                                </button>
                            @endif
                            @if ($cte->situacao->value === 'Pendente' || $cte->situacao->value === 'Rejeitado')
                                <a title="Editar" href="{{ route('cte.edit', [$cte->id]) }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a title="Enviar" href="{{ route('cte.enviar', [$cte->id]) }}" onclick="return confirm('Enviar esse CTe para a SEFAZ?')">
                                    <i class="fas fa-paper-plane text-success"></i>
                                </a>
                                <button type="button" title="Excluir" data-toggle="modal" data-target="#deleteModal{{ $cte->id }}">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            @endif
                        </td>
                    </tr>

                    @if ($cte->situacao->value === 'Autorizado')
                        @component('components.modal', [
                            'modalId' => 'cancelModal' . $cte->id,
                            'modalTitle' => 'Cancelar CTe #' . $cte->numero,
                            'sizeModal' => 'modal-md',
                        ])
                            <form action="{{ route('cte.cancel') }}" method="POST">
                                @csrf
                                <input type="hidden" name="cte_id" value="{{ $cte->id }}">
                                <div class="form-group">
                                    <label for="justificativa{{ $cte->id }}">Justificativa (mínimo 15 caracteres)</label>
                                    <textarea class="form-control" id="justificativa{{ $cte->id }}" name="justificativa" minlength="15" required></textarea>
                                </div>
                                <div class="text-right mt-2">
                                    <button type="submit" class="btn btn-danger">Confirmar Cancelamento</button>
                                </div>
                            </form>
                        @endcomponent
                    @endif

                    @if ($cte->situacao->value === 'Pendente' || $cte->situacao->value === 'Rejeitado')
                        @component('components.modal', [
                            'modalId' => 'deleteModal' . $cte->id,
                            'modalTitle' => 'Excluir CTe #' . $cte->numero,
                            'sizeModal' => 'modal-sm',
                        ])
                            <form action="{{ route('cte.delete') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="cte_id" value="{{ $cte->id }}">
                                <p>Tem certeza que deseja excluir esse CTe?</p>
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
