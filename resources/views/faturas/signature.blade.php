@extends('adminlte::page')

@section('title', 'Assinatura')

@section('content_header')
    <div class="text-center text-dark">
        <h3>Assinatura</h3>
    </div>
@stop

@section('content')
    @php
        // Define parâmetros padrões
        $expiracao = 3600; // 1 hora

        // 1. Flag para controlar o botão "Pagar"
        $primeiroPagamentoPendenteEncontrado = false;

        // 2. Ordena a coleção de pagamentos
        //    Regra: Atrasado (1), Pendente (2), Recebido (3), Outros (4)
        $sortedPayments = collect($payments ?? [])->sortBy(function ($pay) {
            $st = strtoupper(trim($pay->status ?? ''));
            if ($st === 'ATRASADO') {
                return 1;
            }
            if ($st === 'PENDENTE') {
                return 2;
            }
            if ($st === 'RECEBIDO') {
                return 3;
            }
            return 4; // Outros status vêm por último
        });

        $fmtDate = function ($d) {
            if (empty($d) || $d === '-') {
                return '-';
            }
            try {
                return \Carbon\Carbon::parse($d)->format('d-m-Y');
            } catch (\Throwable $e) {
                // Fallbacks comuns
                if (preg_match('/^\d{4}-\d{2}-\d{2}/', $d)) {
                    return \Carbon\Carbon::createFromFormat('Y-m-d', substr($d, 0, 10))->format('d-m-Y');
                }
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $d)) {
                    [$dd, $mm, $yyyy] = explode('/', $d);
                    return "{$dd}-{$mm}-{$yyyy}";
                }
                return $d; // último recurso: mostra como veio
            }
        };

    @endphp

    <section>
        <div class="card">
            <div class="card-content">
                <div class="card-body">

                    {{-- Histórico de Pagamentos --}}
                    <div class="card border-secondary">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-receipt text-secondary"></i> Histórico de Pagamentos</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead>
                                        <tr>
                                            {{-- Menos colunas no mobile para destacar o botão de ação --}}
                                            <th class="d-none d-md-table-cell">Descrição</th>
                                            <th style="white-space: nowrap;">Vencimento</th>
                                            <th class="d-none d-md-table-cell" style="white-space: nowrap;">Recebimento</th>
                                            <th style="white-space: nowrap;">Valor</th>
                                            <th>Status</th>
                                            {{-- TXID removido --}}
                                            <th class="text-right sticky-actions-head" style="white-space: nowrap;">Ações
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sortedPayments as $pay)
                                            @php($st = strtoupper(trim($pay->status ?? '')))
                                            <tr>
                                                {{-- Descrição: oculto no mobile --}}
                                                <td class="d-none d-md-table-cell">
                                                    {{ $pay->descricao ?? '-' }}
                                                </td>

                                                {{-- Vencimento: sempre visível --}}
                                                <td style="white-space: nowrap;">
                                                    {{ $fmtDate($pay->data_vencimento ?? null) }}
                                                    <div class="d-md-none text-muted small">
                                                        {{ $pay->descricao ?? '-' }}
                                                    </div>
                                                </td>

                                                <td class="d-none d-md-table-cell" style="white-space: nowrap;">
                                                    {{ $fmtDate($pay->data_recebimento ?? null) }}
                                                </td>

                                                {{-- Valor: sempre visível --}}
                                                <td style="white-space: nowrap;">
                                                    R${{ number_format($pay->valor ?? 0, 2, ',', '.') }}
                                                </td>

                                                {{-- Status: sempre visível --}}
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $st === 'RECEBIDO'
                                                            ? 'success'
                                                            : ($st === 'PENDENTE'
                                                                ? 'warning'
                                                                : ($st === 'ATRASADO'
                                                                    ? 'danger'
                                                                    : 'secondary')) }}">
                                                        {{ $st ?: 'N/D' }}
                                                    </span>
                                                </td>

                                                {{-- Ações: sticky no mobile para ficar visível --}}
                                                <td class="text-nowrap text-right sticky-actions-cell">
                                                    {{-- Se tiver TXID, mostra botão "Ver" (sem exibir a coluna TXID) --}}
                                                    @if (!empty($pay->txid))
                                                        <a class="btn btn-sm btn-outline-primary mb-1"
                                                            href="{{ url('/pix/cob/' . $pay->txid) }}" target="_blank">
                                                            <i class="fas fa-eye"></i> Ver
                                                        </a>
                                                    @endif

                                                    {{-- Botão "Pagar" apenas para o primeiro PENDENTE/ATRASADO --}}
                                                    @if (in_array($st, ['PENDENTE', 'ATRASADO']) && !$primeiroPagamentoPendenteEncontrado)
                                                        <form method="POST" action="{{ route('pix.pagamento') }}"
                                                            class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="customer_cnpj_cpf"
                                                                value="{{ $customerCpfCnpj ?? '' }}">
                                                            <input type="hidden" name="installment_id"
                                                                value="{{ $pay->id ?? '' }}">
                                                            <input type="hidden" name="valor"
                                                                value="{{ number_format($pay->valor ?? 0, 2, '.', '') }}">
                                                            <input type="hidden" name="descricao"
                                                                value="{{ $pay->descricao ?? 'Pagamento de Mensalidade' }}">
                                                            <input type="hidden" name="expiracao"
                                                                value="{{ 300 }}">
                                                            <button type="submit" class="btn btn-sm btn-success mb-1">
                                                                <i class="fas fa-qrcode"></i> Pagar
                                                            </button>
                                                        </form>
                                                        @php($primeiroPagamentoPendenteEncontrado = true)
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    Nenhum pagamento encontrado.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- /Histórico --}}

                </div>
            </div>
        </div>
    </section>
@endsection

@push('css')
    <style>
        /* Deixa a coluna de Ações sempre à vista em telas pequenas */
        @media (max-width: 767.98px) {

            .sticky-actions-head,
            .sticky-actions-cell {
                position: sticky;
                right: 0;
                background: #fff;
                /* garante contraste sobre listras */
                z-index: 2;
            }

            .sticky-actions-head {
                box-shadow: -6px 0 8px rgba(0, 0, 0, .05);
            }

            .sticky-actions-cell {
                box-shadow: -6px 0 8px rgba(0, 0, 0, .03);
            }

            /* Melhora a tocabilidade no mobile */
            .table .btn {
                padding: .4rem .6rem;
                font-size: .875rem;
                line-height: 1.2;
            }
        }
    </style>
@endpush
