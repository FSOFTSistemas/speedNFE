@extends('adminlte::page')

@section('title', 'XMLS')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3>XMLS de NFCe</h3>
        </div>
    </div>
@stop

@section('content')
    <div class="row" style="margin-bottom: 2%">
        <div class="col">
            <a class="btn btn-info text-light" href="{{ route('nfce.showUnuser') }}">Inutilizar Faixa</a>
            <a class="btn btn-secondary" data-toggle="modal" data-target="#SendXmlModal"><i
                    class="far fa-share-square text-light"></i> Enviar para contador</a>
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
                'targets' => 1,
            ],
            [
                'responsivePriority' => 3,
                'targets' => 2,
            ],
            [
                'responsivePriority' => 4,
                'targets' => 3,
            ],
            [
                'responsivePriority' => 5,
                'targets' => 4,
            ],
            [
                'responsivePriority' => 6,
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
                <th>Chave</th>
                <th>Cliente</th>
                <th>Cupom</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            @foreach ($nfces as $nfce)
                <tr>
                    <td>{{ $nfce->nro }}</td>
                    <td>{{ $nfce->data }}</td>
                    <td><a class="text-decoration-none" title="Visualizar" target="_blank"
                            href="{{ route('nfce.show', [$nfce->cupom->id]) }}">{{ $nfce->chave }}</a></td>
                    <td>{{ $nfce->cupom->cliente->nome ?? 'CONSUMIDOR FINAL' }}</td>
                    <td>{{ $nfce->cupom->nroCupom }}</td>
                    <td>
                        <div class="row">
                            <div class="col">
                                <a title="Download" href='{{ route('nfce.downloadXml', [$nfce->id]) }}' class='text-primary'><i
                                        class="far fa-file-code text-success"></i></a>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    @endcomponent

    @component('components.modal', [
        'modalId' => 'SendXmlModal',
        'modalTitle' => 'Enviar XMLS para o contador',
        'sizeModal' => 'modal-md',
    ])
        <div class="text-center">
            <form class="g-3 needs-validation" novalidate action="{{ route('nfce.sendXmlsToAccountant') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col">
                        <div class="input-group">
                            <div class="form-floating mb-3">
                                <input class="form-control" type="month" id="month" name="month" placeholder=" "
                                    required>
                                <label for="month">Mês</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">

                        <div class="input-group">
                            <div class="form-floating mb-3">
                                <input class="form-control" type="email" id="accountant" name="accountant" placeholder=" " value="{{ $accountant }}"
                                    required>
                                <label for="accountant">Contador</label>
                                <div class="form-text">
                                    Selecione o mês e o contador ao qual você deseja enviar os xmls!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-2">
                    <button type="submit" class="btn btn-outline-success">CONFIRMAR</button>
                </div>
            </form>

        </div>
    @endcomponent
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
        <script>
            (() => {
                'use strict'

                const forms = document.querySelectorAll('.needs-validation')

                Array.from(forms).forEach(form => {
                    form.addEventListener('submit', event => {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
            })()
        </script>
@endsection
