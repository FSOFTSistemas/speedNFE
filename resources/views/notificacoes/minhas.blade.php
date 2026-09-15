@extends('adminlte::page')

@section('title', 'Minhas Notificações')

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <div class="page-eyebrow">Central de Notificações</div>
            <h1 class="m-0 text-dark" style="font-weight: 700;">Minhas Notificações</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right">
            <form action="{{ route('notificacoes.marcarTodasLidas') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn custom-btn custom-btn-secondary">
                    <i class="fas fa-check-double mr-1"></i> Marcar todas como lidas
                </button>
            </form>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-main">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse ($leituras as $leitura)
                    @php
                        $notificacao = $leitura->notificacao;
                        $badge = match ($notificacao->tipo ?? 'info') {
                            'urgente' => 'badge-danger',
                            'aviso' => 'badge-warning',
                            default => 'badge-info',
                        };
                    @endphp
                    <div class="list-group-item {{ $leitura->lida_em ? '' : 'bg-light' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge {{ $badge }} text-uppercase mr-2">{{ $notificacao->tipo ?? 'info' }}</span>
                                <span class="font-weight-bold">{{ $notificacao->titulo }}</span>
                                @unless ($leitura->lida_em)
                                    <span class="badge badge-primary ml-2">Não lida</span>
                                @endunless
                                <div class="text-muted text-sm mt-1">{{ $notificacao->created_at->format('d/m/Y H:i') }}</div>
                                <p class="mb-0 mt-2">{{ $notificacao->mensagem }}</p>
                            </div>
                            @unless ($leitura->lida_em)
                                <form action="{{ route('notificacoes.marcarLida', $leitura->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary text-nowrap">
                                        <i class="fas fa-check mr-1"></i> Marcar como lida
                                    </button>
                                </form>
                            @endunless
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-center text-muted py-5">
                        Você ainda não recebeu nenhuma notificação.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $leituras->links() }}
    </div>
@stop
