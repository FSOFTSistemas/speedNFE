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
                                    <h6 title="{{ isset($signature->due->expires_in) ? 'Expira em breve' : 'Expirado' }}"><i
                                            class="fas fa-exclamation-circle text-pink"></i>
                                        {{ $signature->due->expired_on ?? $signature->due->expires_in }}</h6>
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
                            <a class="btn btn-outline-primary border-secondary" data-toggle="modal" data-target="#renewSignatureModal">Renovar agora</a>
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
@endsection
