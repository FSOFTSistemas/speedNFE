@extends('adminlte::page')

@section('title', 'Notas Emitidas')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3>Resumo de Notas NFCe</h3>
        </div>
    </div>
@stop

@section('content')
    <div class="row" style="margin-bottom: 2%">
        <div class="col">
            <a class="btn btn-primary" href="{{ route('cupom.create') }}">+ Emitir NFCe</a>
            <a class="btn btn-info text-light" href="{{ route('nfce.showUnuser') }}">Inutilizar Faixa</a>
        </div>
    </div>

    @component('components.dataTable', [
        'responsive' => [
            [
                'responsivePriority' => 1,
                'targets' => 0,
            ],
            [
                'responsivePriority' => 2,
                'targets' => 6,
            ],
            [
                'responsivePriority' => 3,
                'targets' => 5,
            ],
            [
                'responsivePriority' => 4,
                'targets' => 1,
            ],
            [
                'responsivePriority' => 5,
                'targets' => 2,
            ],
            [
                'responsivePriority' => 6,
                'targets' => 4,
            ],
            [
                'responsivePriority' => 7,
                'targets' => -1,
            ],
        ],
        'searching' => true,
        'lengthChange' => true,
    ])
        <thead class="table-primary">
            <tr>
                <th>Nº</th>
                <th>Data</th>
                <th>Série</th>
                <th>Chave</th>
                <th>Situação</th>
                <th>Cliente</th>
                <th>Cupom</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            @foreach ($cupoms as $cpm)
                <tr>
                    <td>{{ $cpm->nfce->nro ?? null }}</td>
                    <td>{{ $cpm->data }}</td>
                    <td>{{ $cpm->nfce->serie ?? null }}</td>
                    <td>{{ $cpm->nfce->chave ?? null }}</td>
                    <td>{{ $cpm->situacao }}</td>
                    <td>{{ $cpm->cliente->nome ?? 'CONSUMIDOR FINAL' }}</td>
                    <td>{{ $cpm->nroCupom }}</td>
                    <td>
                        <div class="row">
                            @if ($cpm->situacao == 'ATIVO')
                                @if (isset($cpm->nfce) && $cpm->nfce->situacao == 'Autorizado')
                                    <div class="col">
                                        <a title="Cancelar" onclick="openModalCancelNFCe({{ $cpm->id }})"
                                            class='text-red'><i class="far fa-trash-alt"></i></a>
                                    </div>
                                @else
                                    <div class="col">
                                        <a title="Cancelar" class='text-danger'
                                            onclick="openModalCancelCoupon({{ $cpm->id }})"><i
                                                class="far fa-trash-alt"></i></a>
                                    </div>

                                    <div class="col">
                                        <a title="Enviar" href='{{ route('nfce.send', [$cpm->id]) }}' class='text-success'><i
                                                class="fa fa-upload"></i></a>
                                    </div>
                                @endif
                            @endif

                            @if (!isset($cpm->nfce))
                                <div class="col">
                                    <a title="Visualizar" target="_blank" href='{{ route('cupom.showPreView', [$cpm->id]) }}'
                                        class='text-primary'><i class="far fa-eye"></i></a>
                                </div>
                            @else
                                <div class="col">
                                    <a title="Visualizar" target="_blank" href='{{ route('nfce.show', [$cpm->id]) }}'
                                        class='text-primary'><i class="far fa-eye"></i></a>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    @endcomponent

    @component('components.modal', [
        'modalId' => 'ModalCancelCoupon',
        'modalTitle' => 'Cancelar Cupom',
        'sizeModal' => 'modal-md',
    ])
        <p style="color: red; text-align: center">OBS: Você irá cancelar este cupom!</p>

        <div class="text-center">
            <form action="{{ route('cupom.destroy') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('DELETE')
                <input type="hidden" required class="form-control" id="couponId" name="couponId" value="">

                <div class="text-center mb-2">
                    <button type="submit" class="btn btn-warning">CONFIRMAR</button>
                </div>
            </form>

        </div>
    @endcomponent

    @component('components.modal', [
        'modalId' => 'ModalCancelNFCe',
        'modalTitle' => 'Cancelar NFCe',
        'sizeModal' => 'modal-md',
    ])
        <p style="color: red; text-align: center">OBS: Você irá cancelar esta NFCe!</p>

        <div class="text-center">
            <form class="g-3 needs-validation" novalidate action="{{ route('nfce.cancel') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('DELETE')
                <input type="hidden" required class="form-control" id="cpnId" name="cpnId" value="">

                <div class="form-floating mb-3">
                    <textarea class="form-control" placeholder=" " name="justificativa" id="justificativa" style="height: 120px;"
                        minlength="15" maxlength="1200" required></textarea>
                    <label for="justificativa">Justificativa</label>
                    <div class="form-text">
                        Apresente uma justificativa para cancelamento desta NFCe!
                    </div>
                </div>

                <div class="text-center mb-2">
                    <button type="submit" class="btn btn-warning">CONFIRMAR</button>
                </div>
            </form>

        </div>
    @endcomponent

    <script>
        function openModalCancelCoupon(couponId) {
            document.getElementById('couponId').value = couponId
            $('#ModalCancelCoupon').modal('show')
        }

        function openModalCancelNFCe(couponId) {
            document.getElementById('cpnId').value = couponId
            $('#ModalCancelNFCe').modal('show')
        }

        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')

            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop
