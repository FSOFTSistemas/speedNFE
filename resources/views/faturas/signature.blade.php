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
                                            <th>Descrição</th>
                                            <th style="white-space: nowrap;">Data Vencimento</th>
                                            <th style="white-space: nowrap;">Data Recebimento</th>
                                            <th style="white-space: nowrap;">Valor</th>
                                            <th>Status</th>
                                            <th>TXID</th>
                                            <th style="white-space: nowrap;">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($payments ?? [] as $pay)
                                            <tr>
                                                <td>{{ $pay->descricao ?? '-' }}</td>
                                                <td>{{ $pay->data_vencimento ?? '-' }}</td>
                                                <td>{{ $pay->data_recebimento ?? '-' }}</td>
                                                <td>R${{ number_format($pay->valor ?? 0, 2, ',', '.') }}</td>
                                                <td>
                                                    @php($st = strtoupper(trim($pay->status ?? '')))
                                                    <span class="badge badge-{{ 
                                                        $st === 'RECEBIDO' ? 'success' : (
                                                        $st === 'PENDENTE' ? 'warning' : (
                                                        $st === 'ATRASADO' ? 'danger' : 'secondary')) }}">
                                                        {{ $st ?: 'N/D' }}
                                                    </span>
                                                </td>
                                                <td style="max-width: 220px; overflow:hidden; text-overflow:ellipsis;">
                                                    {{ $pay->txid ?? '-' }}
                                                </td>
                                                <td class="text-nowrap">

                                                    {{-- Se tiver TXID, mostra botão "Ver" --}}
                                                    @if (!empty($pay->txid))
                                                        <a class="btn btn-xs btn-outline-primary"
                                                            href="{{ url('/pix/cob/' . $pay->txid) }}"
                                                            target="_blank">
                                                            <i class="fas fa-eye"></i> Ver
                                                        </a>
                                                    @endif

                                                    {{-- Se estiver PENDENTE ou ATRASADO, mostra botão "Pagar" --}}
                                                    @if (in_array($st, ['PENDENTE', 'ATRASADO']))
                                                        <a class="btn btn-xs btn-success ml-1"
                                                            href="{{ route('pix.pagamento', [
                                                                'valor' => number_format($pay->valor ?? 0, 2, '.', ''),
                                                                'descricao' => $pay->descricao ?? 'Pagamento de Mensalidade',
                                                                'expiracao' => 3600,
                                                            ]) }}">
                                                            <i class="fas fa-qrcode"></i> Pagar
                                                        </a>
                                                    @endif

                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
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
