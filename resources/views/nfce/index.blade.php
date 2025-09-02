@extends('adminlte::page')

@section('title', 'Resumo de Notas NFCe')

@push('css')
    <style>
        /* Estilos importados para consistência */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #00033a;
            --card-bg: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.08);
            --border-color: #dee2e6;
            --text-dark: #343a40;
            --text-light: #6c757d;
            --action-view: #007bff;
            --action-delete: #dc3545;
            --action-send: #28a745;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        .card-main {
            background: var(--card-bg);
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px var(--shadow-color);
            padding: 30px;
        }

        .custom-btn {
            font-weight: 500;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .custom-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .custom-btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #fff !important;
        }

        .custom-btn-info {
            background-color: var(--info-color) !important;
            border-color: var(--info-color) !important;
            color: #fff !important;
        }

        .custom-btn-warning {
            background-color: var(--warning-color) !important;
            border-color: var(--warning-color) !important;
            color: #000 !important;
        }

        .custom-btn-success {
            background-color: var(--success-color) !important;
            border-color: var(--success-color) !important;
            color: #fff !important;
        }

        .custom-btn-danger {
            background-color: var(--action-delete) !important;
            border-color: var(--action-delete) !important;
            color: #fff !important;
        }

        /* Otimização dos botões do cabeçalho para mobile */
        .header-buttons .btn {
            display: block;
            margin-bottom: 8px;
        }

        .header-buttons .btn:last-child {
            margin-bottom: 0;
        }

        @media (min-width: 992px) {
            .header-buttons .btn {
                display: inline-block;
                margin-bottom: 0;
                margin-left: 8px;
            }
        }

        /* Estilos da Tabela */
        .table thead th,
        .table tbody td {
            background-color: transparent !important;
            vertical-align: middle;
            text-align: center;
        }

        .table thead th {
            color: var(--text-dark) !important;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color) !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1 !important;
        }

        .table td.customer-name {
            text-align: left;
        }

        /* Ações */
        .action-buttons {
            white-space: nowrap;
        }

        .action-buttons a {
            color: var(--text-light);
            margin: 0 8px;
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .action-buttons a:hover.text-danger {
            color: var(--action-delete) !important;
        }

        .action-buttons a:hover.text-success {
            color: var(--action-send) !important;
        }

        .action-buttons a:hover.text-primary {
            color: var(--action-view) !important;
        }

        /* Modal */
        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            border-bottom: 1px solid #f0f0f0;
        }
    </style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Resumo de Notas NFCe</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            <a class="btn custom-btn custom-btn-primary" href="{{ route('cupom.create') }}"><i class="fas fa-plus mr-1"></i>
                Emitir NFCe</a>
            <a class="btn custom-btn custom-btn-info" href="{{ route('nfce.showUnuser') }}"><i class="fas fa-ban mr-1"></i>
                Inutilizar Faixa</a>
            <a class="btn custom-btn custom-btn-warning" data-toggle="modal" data-target="#ModalEnviaLoteCupom"><i
                    class="fas fa-paper-plane mr-1"></i> Enviar Lote</a>
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
                'pageLength' => 100,
                'ordering' => true,
                'showFooter' => true,
                'sumColumnIndex' => 4,
            ])
                <thead class="table-light">
                    <tr>
                        <th>Nº</th>
                        <th>Cupom</th>
                        <th style="text-align: left;">Cliente</th>
                        <th>Data</th>
                        <th>Valor</th>
                        <th>Situação</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($cupoms as $cpm)
                        <tr>
                            <td>{{ $cpm->nfce->nro ?? 'N/A' }}</td>
                            <td>{{ $cpm->nroCupom }}</td>
                            <td class="customer-name">{{ $cpm->cliente->nome ?? 'CONSUMIDOR FINAL' }}</td>
                            <td>{{ date('d/m/Y', strtotime($cpm->data)) }}</td>
                            <td>R$ {{ number_format($cpm->subtotal, 2, ',', '.') }}</td>
                            <td>
                                @if ($cpm->situacao == 'CANCELADO')
                                    <span class="badge badge-danger">{{ $cpm->situacao }}</span>
                                @elseif ($cpm->situacao == 'ATIVO')
                                    <span class="badge badge-success">{{ $cpm->situacao }}</span>
                                @else
                                    <span class="badge badge-warning">{{ $cpm->situacao }}</span>
                                @endif
                            </td>
                            <td class="action-buttons">
                                @if (!isset($cpm->nfce))
                                    <a title="Visualizar" target="_blank" href='{{ route('cupom.showPreView', [$cpm->id]) }}'
                                        class='text-primary'><i class="far fa-eye"></i></a>
                                @else
                                    <a title="Visualizar" target="_blank" href='{{ route('nfce.show', [$cpm->id]) }}'
                                        class='text-primary'><i class="far fa-eye"></i></a>
                                @endif

                                @if ($cpm->situacao != 'CANCELADO')
                                    @if (isset($cpm->nfce) && $cpm->nfce->situacao == 'Autorizado')
                                        <a title="Cancelar NFCe" href="#"
                                            onclick="openModalCancelNFCe({{ $cpm->id }})" class='text-danger'><i
                                                class="far fa-trash-alt"></i></a>
                                    @else
                                        <a title="Cancelar Cupom" href="#"
                                            onclick="openModalCancelCoupon({{ $cpm->id }})" class='text-danger'><i
                                                class="far fa-trash-alt"></i></a>
                                        <a title="Enviar" onclick="loadPage(this)" href='{{ route('nfce.send', [$cpm->id]) }}'
                                            class='text-success btn-send-nfce'><i class="fa fa-upload"></i></a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @endcomponent
        </div>
    </div>

    {{-- Modals --}}
    @component('components.modal', [
        'modalId' => 'ModalCancelCoupon',
        'modalTitle' => 'Cancelar Cupom',
        'sizeModal' => 'modal-md',
    ])
        <p class="text-center text-danger">Atenção: Você irá cancelar este cupom!</p>
        <form action="{{ route('cupom.destroy') }}" method="POST" class="text-center">
            @csrf
            @method('DELETE')
            <input type="hidden" required id="couponId" name="couponId">
            <button type="submit" class="btn custom-btn custom-btn-danger mt-3">Confirmar Cancelamento</button>
        </form>
    @endcomponent

    @component('components.modal', [
        'modalId' => 'ModalCancelNFCe',
        'modalTitle' => 'Cancelar NFCe',
        'sizeModal' => 'modal-md',
    ])
        <p class="text-center text-danger">Atenção: Você irá cancelar esta NFCe!</p>
        <form class="needs-validation" novalidate action="{{ route('nfce.cancel') }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" required id="cpnId" name="cpnId">
            <div class="form-group">
                <label for="justificativa">Justificativa (mínimo 15 caracteres)</label>
                <textarea class="form-control" name="justificativa" id="justificativa" rows="4" minlength="15" required></textarea>
                <div class="invalid-feedback">A justificativa é obrigatória e deve ter no mínimo 15 caracteres.</div>
            </div>
            <div class="text-center mt-3">
                <button type="submit" class="btn custom-btn custom-btn-danger">Confirmar Cancelamento</button>
            </div>
        </form>
    @endcomponent

    @component('components.modal', [
        'modalId' => 'ModalEnviaLoteCupom',
        'modalTitle' => 'Enviar Lote de NFCe',
        'sizeModal' => 'modal-md',
    ])
        <form class="needs-validation" novalidate action="{{ route('nfce.sendLot') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="day">Selecione a data</label>
                <input class="form-control" type="date" id="day" name="day" required>
                <small class="form-text text-muted">Serão enviados todos os cupons pendentes da data selecionada.</small>
                <div class="invalid-feedback">Por favor, selecione uma data.</div>
            </div>
            <div class="text-center mt-3">
                <button type="submit" class="btn custom-btn custom-btn-success">Confirmar Envio do Lote</button>
            </div>
        </form>
    @endcomponent

    <div class="modal fade" id="ModalPreLoader" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Aguarde...</h5>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3">Processando sua solicitação.</p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function openModalCancelCoupon(couponId) {
            document.getElementById('couponId').value = couponId;
            $('#ModalCancelCoupon').modal('show');
        }

        function openModalCancelNFCe(couponId) {
            document.getElementById('cpnId').value = couponId;
            $('#ModalCancelNFCe').modal('show');
        }

        function loadPage(clickedElement) {
            $('#ModalPreLoader').modal({
                keyboard: false,
                backdrop: 'static'
            });

            // 2. Desabilita TODOS os botões de envio na página.
            // Ele procura por todos os links com a classe 'btn-send-nfce' que adicionamos.
            var allSendButtons = document.querySelectorAll('.btn-send-nfce');
            allSendButtons.forEach(function(button) {
                button.style.pointerEvents = 'none'; // Impede que o link seja clicado novamente.
                button.style.opacity = '0.6'; // Deixa o link com aparência de desabilitado.
            });

            // 3. Troca o ícone do botão específico que foi clicado para um spinner.
            // O 'clickedElement' é o parâmetro '(this)' que passamos no onclick.
            if (clickedElement) {
                clickedElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
        }

        (() => {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })();
    </script>
@endsection
