@forelse ($leituras as $leitura)
    @php
        $notificacao = $leitura->notificacao;
        $icone = match ($notificacao->tipo ?? 'info') {
            'urgente' => 'fa-exclamation-circle text-danger',
            'aviso' => 'fa-exclamation-triangle text-warning',
            default => 'fa-info-circle text-info',
        };
    @endphp
    <a href="{{ route('notificacoes.minhas') }}" class="dropdown-item">
        <i class="fas {{ $icone }} mr-2"></i>
        <span class="font-weight-bold">{{ $notificacao->titulo ?? 'Notificação' }}</span>
        <span class="float-right text-muted text-sm">{{ optional($leitura->created_at)->diffForHumans() }}</span>
        <div class="dropdown-divider"></div>
    </a>
@empty
    <span class="dropdown-item text-muted text-center">Nenhuma notificação nova</span>
@endforelse
