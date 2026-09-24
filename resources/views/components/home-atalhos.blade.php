{{-- Atalhos rápidos da home, exibidos conforme o cargo do usuário (mesmas listas de access.permission das rotas) --}}
@php
    $cargo = auth()->user()->cargo;

    $atalhos = [
        ['url' => url('/vendas/nova'), 'icon' => 'fas fa-file-invoice', 'cor' => 'primary', 'titulo' => 'Emitir NFe', 'descricao' => 'Nova nota fiscal eletrônica', 'cargos' => ['master', 'admin', 'client-advanced1', 'client-advanced2', 'client-NFe']],
        ['url' => route('cupom.create'), 'icon' => 'fas fa-cash-register', 'cor' => 'info', 'titulo' => 'Nova venda PDV', 'descricao' => 'Cupom / NFC-e no balcão', 'cargos' => ['master', 'admin', 'client-NFCe', 'client-advanced2']],
        ['url' => route('mdfe.create'), 'icon' => 'fas fa-truck', 'cor' => 'success', 'titulo' => 'Emitir MDF-e', 'descricao' => 'Manifesto de carga', 'cargos' => ['master', 'admin', 'client-MDFe', 'client-advanced1', 'client-advanced3']],
        ['url' => route('cte.create'), 'icon' => 'fas fa-shipping-fast', 'cor' => 'warning', 'titulo' => 'Emitir CT-e', 'descricao' => 'Conhecimento de transporte', 'cargos' => ['master', 'admin', 'client-CTe', 'client-advanced3']],
        ['url' => route('nfcom.create'), 'icon' => 'fas fa-broadcast-tower', 'cor' => 'info', 'titulo' => 'Emitir NFCom', 'descricao' => 'Nota de comunicação', 'cargos' => ['master', 'admin', 'client-NFCom']],
        ['url' => route('nfse.create'), 'icon' => 'fas fa-concierge-bell', 'cor' => 'primary', 'titulo' => 'Emitir NFS-e', 'descricao' => 'Nota de serviço nacional', 'cargos' => ['master', 'admin', 'client-NFSe']],
        ['url' => route('produto.new'), 'icon' => 'fas fa-box-open', 'cor' => 'warning', 'titulo' => 'Novo produto', 'descricao' => 'Cadastrar no catálogo', 'cargos' => ['master', 'admin', 'client-advanced1', 'client-advanced2', 'client-NFe', 'client-NFCe']],
        ['url' => route('cliente.create'), 'icon' => 'fas fa-user-plus', 'cor' => 'success', 'titulo' => 'Novo cliente', 'descricao' => 'Cadastrar destinatário', 'cargos' => ['master', 'admin', 'client-advanced1', 'client-advanced2', 'client-NFe', 'client-NFCe']],
    ];

    $atalhos = array_values(array_filter($atalhos, fn ($atalho) => in_array($cargo, $atalho['cargos'], true)));
@endphp

@if (count($atalhos) > 0)
    <div class="quick-grid">
        @foreach ($atalhos as $atalho)
            <a href="{{ $atalho['url'] }}" class="quick-card">
                <span class="stat-icon bg-{{ $atalho['cor'] }}-soft"><i class="{{ $atalho['icon'] }}"></i></span>
                <span class="quick-card-text">
                    <strong>{{ $atalho['titulo'] }}</strong>
                    <small>{{ $atalho['descricao'] }}</small>
                </span>
                <i class="fas fa-chevron-right quick-card-arrow"></i>
            </a>
        @endforeach
    </div>
@endif
