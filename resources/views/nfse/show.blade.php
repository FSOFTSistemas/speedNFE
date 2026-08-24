@extends('adminlte::page')

@section('title', 'Visualizar NFS-e')

@section('content_header')
    <h1 class="m-0 text-dark">NFS-e #{{ $nfse->id }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Situação</strong>
                <p>
                    {{ $nfse->situacao }}
                    @if ($nfse->cStat)
                        <span class="text-muted">({{ $nfse->cStat }})</span>
                    @endif
                </p>
            </div>
            <div class="col-md-4">
                <strong>Tomador</strong>
                <p>{{ $nfse->cliente->nome ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Serviço</strong>
                <p>{{ $nfse->servico->descricao ?? '-' }}</p>
            </div>
        </div>
        @if ($nfse->chave || $nfse->nProtocolo || $nfse->xMotivo)
            <div class="row">
                <div class="col-md-4">
                    <strong>Chave de Acesso</strong>
                    <p>{{ $nfse->chave ?: '-' }}</p>
                </div>
                <div class="col-md-4">
                    <strong>Protocolo/Id DPS</strong>
                    <p>{{ $nfse->nProtocolo ?: '-' }}</p>
                </div>
                <div class="col-md-4">
                    <strong>Processamento</strong>
                    <p>{{ optional($nfse->data_processamento)->format('d/m/Y H:i') ?: '-' }}</p>
                </div>
            </div>
            @if ($nfse->xMotivo)
                <div class="alert alert-light border">
                    <strong>Mensagem SEFIN</strong>
                    <div>{{ $nfse->xMotivo }}</div>
                </div>
            @endif
        @endif
        <div class="row">
            <div class="col-md-3">
                <strong>Competência</strong>
                <p>{{ optional($nfse->data_competencia)->format('d/m/Y') ?: '-' }}</p>
            </div>
            <div class="col-md-3">
                <strong>cTribNac</strong>
                <p>{{ $nfse->cTribNac ?: '-' }}</p>
            </div>
            <div class="col-md-3">
                <strong>cNBS</strong>
                <p>{{ $nfse->cNBS ?: '-' }}</p>
            </div>
            <div class="col-md-3">
                <strong>cIndOp</strong>
                <p>{{ $nfse->cIndOp ?: '-' }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <strong>Valor Serviço</strong>
                <p>R$ {{ number_format($nfse->vServ, 2, ',', '.') }}</p>
            </div>
            <div class="col-md-3">
                <strong>Base ISSQN</strong>
                <p>R$ {{ number_format($nfse->vBC, 2, ',', '.') }}</p>
            </div>
            <div class="col-md-3">
                <strong>ISSQN</strong>
                <p>R$ {{ number_format($nfse->vISSQN, 2, ',', '.') }}</p>
            </div>
            <div class="col-md-3">
                <strong>Valor Líquido</strong>
                <p>R$ {{ number_format($nfse->vLiq, 2, ',', '.') }}</p>
            </div>
        </div>
        <div class="form-group">
            <strong>Discriminação</strong>
            <p>{{ $nfse->discriminacao }}</p>
        </div>
        @if ($nfse->informacoes_complementares)
            <div class="form-group">
                <strong>Informações Complementares</strong>
                <p>{{ $nfse->informacoes_complementares }}</p>
            </div>
        @endif
        <div class="text-right mt-3">
            <a href="{{ route('nfse.index') }}" class="btn btn-secondary">Voltar</a>
            @if ($nfse->xmlAutorizado || $nfse->xmlDps)
                <a href="{{ route('nfse.downloadXml', [$nfse->id]) }}" class="btn btn-outline-info">Baixar XML</a>
            @endif
            @if (in_array($nfse->situacao, ['Pendente', 'Rejeitado', 'Rascunho']))
                <a href="{{ route('nfse.edit', [$nfse->id]) }}" class="btn btn-primary">Editar</a>
            @endif
        </div>
    </div>
</div>
@stop
