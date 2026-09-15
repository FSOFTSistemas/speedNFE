@extends('adminlte::page')

@section('title', 'Pagamento realizado')

@section('content_header')
    <div class="d-flex align-items-center justify-content-center flex-column text-center">
        <h3 class="mb-0">Pagamento realizado com sucesso</h3>
        <small class="text-muted">Obrigado! Sua operação foi confirmada.</small>
    </div>
@stop

@section('content')
    @php
        $txid = $txid ?? null;
        $valor = $valor ?? null;
        $descricao = $descricao ?? 'Pagamento';
    @endphp

    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-7 col-md-9">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5 text-center">

                    {{-- Check animado --}}
                    <div class="d-flex justify-content-center mb-4">
                        <div style="width:110px;height:110px;position:relative;">
                            <svg viewBox="0 0 120 120" style="width:110px;height:110px;">
                                <defs>
                                    <linearGradient id="g" x1="0%" y1="0%" x2="100%"
                                        y2="100%">
                                        <stop offset="0%" stop-color="#34d399"></stop>
                                        <stop offset="100%" stop-color="#10b981"></stop>
                                    </linearGradient>
                                </defs>
                                <circle cx="60" cy="60" r="52" fill="none" stroke="url(#g)"
                                    stroke-width="10" stroke-linecap="round" opacity="0.2" />
                                <circle cx="60" cy="60" r="52" fill="none" stroke="url(#g)"
                                    stroke-width="10" stroke-linecap="round" stroke-dasharray="326" stroke-dashoffset="326">
                                    <animate attributeName="stroke-dashoffset" from="326" to="0" dur="0.8s"
                                        fill="freeze" />
                                </circle>
                                <path d="M38 64 L54 78 L84 42" fill="none" stroke="#10b981" stroke-width="10"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="80"
                                    stroke-dashoffset="80">
                                    <animate attributeName="stroke-dashoffset" from="80" to="0" begin="0.5s"
                                        dur="0.5s" fill="freeze" />
                                </path>
                            </svg>
                        </div>
                    </div>

                    <h4 class="mb-2">Tudo certo! 🎉</h4>
                    <p class="text-muted mb-4">
                        Seu pagamento foi confirmado e já está registrado no sistema.
                    </p>

                    <div class="row text-left g-3">
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small mb-1">Descrição</div>
                                <div class="font-weight-bold">{{ $descricao }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small mb-1">Valor</div>
                                <div class="h5 mb-0">
                                    @if ($valor)
                                        R$ {{ number_format((float) $valor, 2, ',', '.') }}
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small mb-1">Status</div>
                                <span class="badge badge-success">PAGO</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <div class="text-muted small mb-1">TXID</div>
                                <div class="d-flex align-items-center">
                                    <code class="mr-2" style="white-space:nowrap;overflow:auto;display:block;">
                                        {{ $txid ?? '—' }}
                                    </code>
                                    @if ($txid)
                                        <button class="btn btn-sm btn-outline-secondary ml-auto" id="copyTxid">
                                            <i class="fas fa-copy"></i> Copiar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex flex-wrap justify-content-center">
                        <a href="{{ url('/home') }}" class="btn btn-success mx-1 my-1">
                            <i class="fas fa-home"></i> Ir para o início
                        </a>
                        <button id="btnEnviarComprovante" class="btn btn-primary mx-1 my-1">
                            <i class="fas fa-envelope"></i> Receber Comprovante
                        </button>
                        <a href="{{ url('/faturas') }}" class="btn btn-outline-secondary mx-1 my-1">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>

                </div>
            </div>

            <div class="text-center text-muted small mt-3">
                Dúvidas? Fale com o suporte.
            </div>
        </div>
    </div>
@stop

@push('js')
<script>
    // Injeta os dados da página (do PHP) para o JS
    const pageData = {
        txid: @json($txid ?? ''),
        valor: @json($valor ?? '0.00'),
        descricao: @json($descricao ?? 'Pagamento')
    };

    // Pega o CSRF token do <head> para as requisições AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Lógica para o botão "Copiar TXID" (escondido)
    document.getElementById('copyTxid')?.addEventListener('click', async () => {
        try {
            const text = pageData.txid;
            if (!text) return;
            await navigator.clipboard.writeText(text);
            
            const btn = document.getElementById('copyTxid');
            const oldHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Copiado!';
            btn.disabled = true;
            setTimeout(()=> {
                btn.innerHTML = oldHtml;
                btn.disabled = false;
            }, 2000);
        } catch (e) {
            console.error('Falha ao copiar TXID', e);
        }
    });

    // Lógica para o botão "Receber Comprovante" (SweetAlert)
    document.getElementById('btnEnviarComprovante')?.addEventListener('click', async () => {
        
        const { value: email } = await Swal.fire({
            title: 'Receber Comprovante',
            // inputLabel: 'Digite seu endereço de e-mail', // <--- REMOVIDO (v8)
            text: 'Digite seu endereço de e-mail:',       // <--- ADICIONADO (alternativa)
            input: 'email',
            inputPlaceholder: 'seu.email@exemplo.com',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Enviar',
            showLoaderOnConfirm: true,
            preConfirm: (email) => {
                if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    Swal.showValidationMessage('Por favor, digite um e-mail válido.');
                    return false;
                }
                return email;
            },
            allowOutsideClick: () => !Swal.isLoading()
        });

        if (email) {
            try {
                // O SweetAlert v8 não tem um 'Swal.showLoading()' separado assim
                // O 'showLoaderOnConfirm: true' já faz isso.

                const resp = await fetch(@json(route('pix.enviarConfirmacao')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        email: email,
                        txid: pageData.txid,
                        valor: pageData.valor,
                        descricao: pageData.descricao
                    })
                });

                if (!resp.ok) {
                    const err = await resp.json();
                    throw new Error(err.message || 'Falha ao enviar.');
                }

                // Sucesso
                await Swal.fire({
                    type: 'success', 
                    title: 'Enviado!',
                    text: `O comprovante foi enviado para ${email}.`
                });

            } catch (error) {
                // Erro
                Swal.fire({
                    type: 'error', 
                    title: 'Oops...',
                    text: error.message || 'Não foi possível enviar o e-mail.'
                });
            }
        }
    });
</script>
@endpush