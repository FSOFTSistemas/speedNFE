@extends('adminlte::page')

@section('title', 'Pagamento')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Pagamento via PIX</h3>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
@stop

@section('content')
    @php
        // Valores vindos de routes/web.php (querystring) ou defina defaults
        $valor = $valor ?? null; // ex.: 49.90
        $descricao = $descricao ?? 'Pagamento de Assinatura';
        $expiracao = (int) ($expiracao ?? 3600);
    @endphp

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-secondary shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <div>
                        <strong><i class="fas fa-qrcode text-secondary"></i> PIX</strong>
                        <small class="text-muted ml-2">Escaneie o QR Code ou use o copia e cola</small>
                    </div>
                    <span id="statusPill" class="badge badge-secondary">AGUARDANDO</span>
                </div>

                <div class="card-body">
                    {{-- ALERTS --}}
                    <div id="alertBox" class="alert d-none" role="alert"></div>

                    {{-- RESUMO --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted">Descrição</div>
                                <div class="font-weight-bold" id="resDescricao">{{ $descricao }}</div>
                            </div>
                        </div>
                        <div class="col-md-4 mt-3 mt-md-0">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted">Valor</div>
                                <div class="h4 mb-0" id="resValor">
                                    @if ($valor)
                                        R$ {{ number_format((float) $valor, 2, ',', '.') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mt-3 mt-md-0">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted">Expira em</div>
                                <div>
                                    <span id="countdown">—</span>
                                </div>
                                <div class="progress mt-2" style="height: 10px;">
                                    <div id="progressBar" class="progress-bar bg-success" role="progressbar"
                                        style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CONTEÚDO PRINCIPAL --}}
                    <div class="row">
                        <div class="col-md-6 text-center mb-3 mb-md-0">
                            <div class="border rounded p-3">
                                <div class="mb-2"><strong>QR Code (PIX)</strong></div>
                                <img id="qrImg" src="" alt="QR Code PIX" style="max-width: 320px; display:none;"
                                    class="img-fluid" />
                                <div id="qrPlaceholder" class="text-muted"
                                    style="min-height: 340px; display:flex; align-items:center; justify-content:center;">
                                    <div class="text-center">
                                        <i class="fas fa-qrcode fa-3x mb-2"></i>
                                        <div>Gerando QR Code...</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 d-flex flex-column">
                            <div class="form-group">
                                <label for="txid">TXID</label>
                                <div class="input-group">
                                    <input id="txid" type="text" class="form-control" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="btnVerificar">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Verifique manualmente o status, se desejar.</small>
                            </div>

                            <div class="form-group">
                                <label for="copiaCola">Pix Copia e Cola</label>
                                <textarea id="copiaCola" class="form-control" rows="5" readonly placeholder=""></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button id="btnCopiar" class="btn btn-primary">
                                    <i class="fas fa-copy"></i> Copiar
                                </button>
                                <a id="btnAtualizar" href="{{ url()->current() }}" class="btn btn-outline-secondary ml-2">
                                    <i class="fas fa-redo"></i> Atualizar
                                </a>
                            </div>

                            <div class="mt-3">
                                <small class="text-muted">
                                    Assim que seu pagamento for identificado, confirmaremos automaticamente.
                                </small>
                            </div>
                        </div>
                    </div>

                </div> {{-- card-body --}}
            </div>
        </div>
    </div>

    {{-- Dados para JS --}}
    <script>
        window.__PIX_PAGE__ = {
            valor: {!! json_encode($valor) !!},
            descricao: {!! json_encode($descricao) !!},
            expiracao: {!! json_encode($expiracao) !!},
            rotas: {
                criar: {!! json_encode(route('pix.criar')) !!},
                consultarBase: {!! json_encode(url('/pix/cob')) !!} // + '/' + txid
                sucesso: {!! json_encode(route('pix.sucesso')) !!} // << ADICIONE ESTA LINHA
            },
            csrf: {!! json_encode(csrf_token()) !!}
        };
    </script>
@stop

@push('js')
    <script>
        (function() {
            const S = window.__PIX_PAGE__ || {};
            const $ = (sel) => document.querySelector(sel);
            const $$ = (sel) => Array.from(document.querySelectorAll(sel));

            const alertBox = $('#alertBox');
            const statusPill = $('#statusPill');
            const qrImg = $('#qrImg');
            const qrPh = $('#qrPlaceholder');
            const txidInput = $('#txid');
            const copiaCola = $('#copiaCola');
            const btnCopiar = $('#btnCopiar');
            const btnVerificar = $('#btnVerificar');
            const countdownEl = $('#countdown');
            const progressBar = $('#progressBar');
            const resDescricao = $('#resDescricao');
            const resValor = $('#resValor');

            let expiresAt = null; // ISO datetime
            let totalSeconds = Math.max(parseInt(S.expiracao || 3600, 10), 60);
            let remainSeconds = totalSeconds;
            let txid = null;
            let pollTimer = null;
            let tickTimer = null;
            let pago = false;

            function showAlert(type, msg) {
                alertBox.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning', 'alert-info');
                alertBox.classList.add('alert-' + type);
                alertBox.textContent = msg;
            }

            function hideAlert() {
                alertBox.classList.add('d-none');
                alertBox.classList.remove('alert-success', 'alert-danger', 'alert-warning', 'alert-info');
                alertBox.textContent = '';
            }

            function setStatusPill(text, kind) {
                statusPill.textContent = text;
                statusPill.className = 'badge badge-' + (kind || 'secondary');
            }

            function formatMMSS(sec) {
                const m = Math.floor(sec / 60);
                const s = sec % 60;
                return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            }

            function updateProgress() {
                if (!totalSeconds || totalSeconds <= 0) {
                    progressBar.style.width = '0%';
                    return;
                }
                const used = totalSeconds - remainSeconds;
                const pct = Math.max(0, Math.min(100, (used / totalSeconds) * 100));
                progressBar.style.width = pct.toFixed(2) + '%';
                // Muda cor conforme o tempo
                if (remainSeconds <= 30) {
                    progressBar.classList.remove('bg-success', 'bg-warning');
                    progressBar.classList.add('bg-danger');
                } else if (remainSeconds <= 120) {
                    progressBar.classList.remove('bg-success', 'bg-danger');
                    progressBar.classList.add('bg-warning');
                } else {
                    progressBar.classList.remove('bg-warning', 'bg-danger');
                    progressBar.classList.add('bg-success');
                }
            }

            function stopAllTimers() {
                if (pollTimer) clearInterval(pollTimer);
                if (tickTimer) clearInterval(tickTimer);
                pollTimer = null;
                tickTimer = null;
            }

            function startCountdown() {
                if (!expiresAt) return;

                // Se veio a data exata do backend, calcule o restante correto
                const end = new Date(expiresAt).getTime();
                const now = Date.now();
                remainSeconds = Math.max(0, Math.floor((end - now) / 1000));
                // Garantia: se por qualquer motivo vier maior que totalSeconds, limita
                remainSeconds = Math.min(remainSeconds, totalSeconds);

                countdownEl.textContent = formatMMSS(remainSeconds);
                updateProgress();

                tickTimer = setInterval(() => {
                    if (pago) {
                        clearInterval(tickTimer);
                        return;
                    }
                    remainSeconds = Math.max(0, remainSeconds - 1);
                    countdownEl.textContent = formatMMSS(remainSeconds);
                    updateProgress();
                    if (remainSeconds <= 0) {
                        clearInterval(tickTimer);
                        setStatusPill('EXPIRADO', 'secondary');
                        showAlert('warning', 'Tempo de pagamento expirado. Gere um novo PIX.');
                        desabilitarInteracoesPorExpiracao();
                        stopAllTimers();
                    }
                }, 1000);
            }

            function desabilitarInteracoesPorExpiracao() {
                btnCopiar.disabled = true;
                btnVerificar.disabled = true;
                copiaCola.readOnly = true;
            }

            async function criarPix() {
                if (!S.valor) {
                    showAlert('danger', 'Valor não informado. Volte e selecione o plano/assinatura.');
                    setStatusPill('ERRO', 'danger');
                    return;
                }
                hideAlert();
                setStatusPill('GERANDO', 'info');
                try {
                    const resp = await fetch(S.rotas.criar, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': S.csrf
                        },
                        body: JSON.stringify({
                            valor: S.valor,
                            descricao: S.descricao || 'Pagamento',
                            expiracao: S.expiracao || 3600
                        })
                    });
                    if (!resp.ok) {
                        const t = await resp.text();
                        throw new Error(t || 'Erro ao criar PIX.');
                    }
                    const data = await resp.json();

                    txid = data.txid || null;
                    expiresAt = data.expires_at || null;

                    // QR
                    if (data.qr_base64) {
                        qrImg.src = data.qr_base64;
                        qrImg.style.display = 'block';
                        qrPh.style.display = 'none';
                    } else {
                        qrImg.style.display = 'none';
                        qrPh.style.display = 'flex';
                    }

                    // Copia e cola
                    copiaCola.value = data.copia_e_cola || '';
                    txidInput.value = txid || '';

                    setStatusPill((data.status || 'ATIVA').toUpperCase(), 'warning');

                    // Contador
                    startCountdown();

                    // Polling a cada 3s
                    iniciarPolling();

                } catch (e) {
                    console.error(e);
                    setStatusPill('ERRO', 'danger');
                    showAlert('danger', e.message || 'Falha ao criar cobrança PIX.');
                }
            }

            function iniciarPolling() {
                pararPolling(); // segurança
                pollTimer = setInterval(async () => {
                    if (!txid) return;
                    try {
                        const resp = await fetch(`${S.rotas.consultarBase}/${encodeURIComponent(txid)}`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (!resp.ok) throw new Error('Erro ao consultar cobrança.');
                        const data = await resp.json();

                        const st = (data.status || '').toUpperCase();
                        const foiPago = !!data.pago;

                        if (data.expires_at && !pago) {
                            // Atualiza relógio baseado no backend (mais preciso)
                            expiresAt = data.expires_at;
                        }

                        if (foiPago) {
                            pago = true;
                            setStatusPill('PAGO', 'success');
                            showAlert('success', 'Pagamento identificado! Obrigado.');
                            stopAllTimers();

                            // monta os parâmetros para exibir na tela de sucesso
                            const v = (S.valor ? Number(S.valor).toFixed(2) : null);
                            const qs = new URLSearchParams({
                                txid: txid || '',
                                valor: v || '',
                                descricao: S.descricao || 'Pagamento'
                            }).toString();

                            // redireciona
                            window.location.href = `${S.rotas.sucesso}?${qs}`;
                            
                        } else if (st === 'REMOVIDA_PELO_USUARIO_RECEBEDOR' || st === 'REMOVIDA_PELO_PSP' ||
                            st === 'REMOVIDA') {
                            setStatusPill('REMOVIDA', 'secondary');
                            showAlert('warning', 'Cobrança foi removida.');
                            stopAllTimers();
                        } else if (remainSeconds <= 0) {
                            // expirado já é tratado no countdown
                            stopAllTimers();
                        } else {
                            setStatusPill(st || 'ATIVA', 'warning');
                        }
                    } catch (e) {
                        console.error(e);
                        // Não derruba a página; apenas informa uma vez
                    }
                }, 3000);
            }

            function pararPolling() {
                if (pollTimer) clearInterval(pollTimer);
                pollTimer = null;
            }

            // Copiar
            btnCopiar?.addEventListener('click', async () => {
                try {
                    if (!copiaCola.value) return;
                    await navigator.clipboard.writeText(copiaCola.value);
                    showAlert('success', 'Código PIX copiado para a área de transferência.');
                    setTimeout(hideAlert, 2500);
                } catch {
                    showAlert('warning',
                        'Não foi possível copiar automaticamente. Selecione e copie manualmente.');
                }
            });

            // Verificar manualmente
            btnVerificar?.addEventListener('click', async () => {
                if (!txidInput.value) return;
                try {
                    const resp = await fetch(
                        `${S.rotas.consultarBase}/${encodeURIComponent(txidInput.value)}`);
                    if (!resp.ok) throw new Error('Erro ao consultar.');
                    const data = await resp.json();
                    const st = (data.status || '').toUpperCase();
                    setStatusPill(st, (data.pago ? 'success' : 'warning'));
                    if (data.pago) {
                        pago = true;
                        showAlert('success', 'Pagamento identificado!');
                        stopAllTimers();
                    }
                } catch (e) {
                    showAlert('warning', e.message || 'Falha ao consultar.');
                }
            });

            // Preenche resumo (se quiser mudar dinamicamente)
            if (!S.valor) {
                resValor.innerHTML = '<span class="text-muted">—</span>';
            }

            // Auto-criação do PIX ao carregar
            document.addEventListener('DOMContentLoaded', () => {
                criarPix();
            });

        })();
    </script>
@endpush
