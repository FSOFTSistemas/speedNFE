@extends('adminlte::page')

@section('title', 'Assinatura')

@section('content_header')
    <div class="text-center text-dark">
        <h3>Assinatura</h3>
    </div>
@stop

@section('content')
    <section>
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    @if (isset($signature->due))
                        <div class="row">
                            <div class="col">
                                <h6>
                                    <b>Licença para plataforma - Speed NFe</b>
                                </h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <h6>Mensal</h6>
                            </div>
                        </div>

                        <div class="row pt-3">
                            <div class="col">
                                <h6><i class="fas fa-history text-teal"></i> Renovação automática</h6>
                                <div class="row">
                                    <div class="col">
                                        <h6>ativada</h6>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <h6>Data de expiração</h6>
                                <div class="row">
                                    <div class="col">
                                        <h6
                                            title="{{ isset($signature->due->expires_in) ? 'Expira em breve' : 'Expirado' }}">
                                            <i class="fas fa-exclamation-circle text-pink"></i>
                                            {{ $signature->due->expired_on ?? $signature->due->expires_in }}
                                        </h6>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <h6>Preço de renovação</h6>
                                <div class="row">
                                    <div class="col">
                                        <h6>R${{ number_format($signature->value, 2, '.', '.') }}</h6>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <a class="btn btn-outline-primary border-secondary" data-toggle="modal"
                                    data-target="#renewSignatureModal">Renovar agora</a>
                            </div>
                        </div>

                        @component('components.modal', [
                            'modalId' => 'renewSignatureModal',
                            'modalTitle' => 'Renovar Assinatura',
                            'sizeModal' => 'modal-lg',
                        ])
                        @endcomponent
                    @else
                        <div class="text-center">
                            <h5>Seu perfil é isento de assinaturas dentro de nossa plataforma! <br> Aproveite gratuitamente
                                as nossas funcionalidades</h5>
                        </div>
                    @endif

                <hr>

                <div class="row mb-3">
                    <div class="col d-flex gap-2">
                        <a class="btn btn-success" data-toggle="modal" data-target="#newPixPaymentModal">
                            <i class="fas fa-qrcode"></i> Novo Pagamento (PIX)
                        </a>
                        <a class="btn btn-outline-secondary" href="{{ url()->current() }}">
                            <i class="fas fa-sync"></i> Atualizar
                        </a>
                    </div>
                </div>

                <div class="card border-secondary">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-receipt text-secondary"></i> Histórico de Pagamentos</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="white-space: nowrap;">Data</th>
                                        <th>Descrição</th>
                                        <th style="white-space: nowrap;">Valor</th>
                                        <th>Status</th>
                                        <th>TXID</th>
                                        <th style="white-space: nowrap;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments ?? [] as $pay)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($pay->created_at)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $pay->descricao ?? '-' }}</td>
                                            <td>R${{ number_format($pay->valor ?? 0, 2, ',', '.') }}</td>
                                            <td>
                                                @php($st = strtoupper($pay->status ?? ''))
                                                <span class="badge badge-{{ $st === 'LIQUIDADO' || $st === 'PAGO' ? 'success' : ($st === 'ATIVA' ? 'warning' : 'secondary') }}">
                                                    {{ $st ?: 'N/D' }}
                                                </span>
                                            </td>
                                            <td style="max-width: 220px; overflow:hidden; text-overflow:ellipsis;">{{ $pay->txid ?? '-' }}</td>
                                            <td>
                                                @if(!empty($pay->txid))
                                                    <a class="btn btn-xs btn-outline-primary" href="{{ url('/pix/cob/' . $pay->txid) }}" target="_blank">
                                                        <i class="fas fa-eye"></i> ver
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">Nenhum pagamento encontrado.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                </div>
            </div>
        </div>
    </section>

    @component('components.modal', [
        'modalId' => 'renewSignatureModal',
        'modalTitle' => 'Renovar Assinatura',
        'sizeModal' => 'modal-lg',
    ])
    @endcomponent


    <!-- Modal: Novo Pagamento (PIX) -->
    <div class="modal fade" id="newPixPaymentModal" tabindex="-1" role="dialog" aria-labelledby="newPixPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newPixPaymentModalLabel">Gerar Pagamento PIX</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pixValor">Valor (R$)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="pixValor" placeholder="0,00" value="{{ number_format($signature->value ?? 0, 2, '.', '') }}">
                            </div>
                            <div class="form-group">
                                <label for="pixDescricao">Descrição</label>
                                <input type="text" class="form-control" id="pixDescricao" placeholder="Ex.: Renovação Speed NFe">
                            </div>
                            <div class="form-group">
                                <label for="pixExpiracao">Expiração (segundos)</label>
                                <input type="number" class="form-control" id="pixExpiracao" value="3600" min="60">
                            </div>
                            <button id="btnGerarPix" class="btn btn-primary">
                                <i class="fas fa-bolt"></i> Gerar PIX
                            </button>
                            <small class="form-text text-muted mt-2">
                                O pagamento será confirmado automaticamente pelo webhook quando o PIX for liquidado.
                            </small>
                            <div id="pixAlert" class="alert alert-danger mt-3 d-none"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center mb-2">
                                <strong>QR Code (PIX)</strong>
                            </div>
                            <div class="d-flex justify-content-center">
                                <img id="pixQrImg" src="" alt="QR PIX" style="max-width: 280px; display:none;" />
                            </div>
                            <div class="form-group mt-3">
                                <label for="pixCopiaCola">Pix Copia e Cola</label>
                                <textarea id="pixCopiaCola" class="form-control" rows="4" readonly placeholder="Gere o PIX para exibir o código"></textarea>
                            </div>
                            <div class="form-group">
                                <label>TXID</label>
                                <input id="pixTxid" class="form-control" type="text" readonly>
                            </div>
                            <div class="form-group mb-0">
                                <label>Status</label>
                                <input id="pixStatus" class="form-control" type="text" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        (function() {
            function $(sel) { return document.querySelector(sel); }
            const btn = document.getElementById('btnGerarPix');
            if (btn) {
                btn.addEventListener('click', async function () {
                    const alertBox = document.getElementById('pixAlert');
                    const v = parseFloat((document.getElementById('pixValor').value || '0').toString().replace(',', '.'));
                    const d = document.getElementById('pixDescricao').value || 'Pagamento';
                    const e = parseInt(document.getElementById('pixExpiracao').value || '3600', 10);

                    alertBox.classList.add('d-none');
                    alertBox.textContent = '';

                    try {
                        const resp = await fetch('{{ url('/pix/cob') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ valor: v, descricao: d, expiracao: e })
                        });

                        if (!resp.ok) {
                            const t = await resp.text();
                            throw new Error(t || 'Erro ao gerar PIX');
                        }

                        const data = await resp.json();
                        // Preenche campos
                        const img = document.getElementById('pixQrImg');
                        const copia = document.getElementById('pixCopiaCola');
                        const txid = document.getElementById('pixTxid');
                        const st = document.getElementById('pixStatus');

                        img.src = data.qr_base64 || '';
                        img.style.display = data.qr_base64 ? 'block' : 'none';
                        copia.value = data.copia_e_cola || '';
                        txid.value = data.txid || '';
                        st.value = data.status || '';
                    } catch (err) {
                        alertBox.textContent = (err && err.message) ? err.message : 'Falha ao gerar PIX';
                        alertBox.classList.remove('d-none');
                    }
                });
            }
        })();
    </script>
    @endpush

@endsection
