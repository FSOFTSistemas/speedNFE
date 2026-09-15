@extends('adminlte::page')

@section('title', 'Central de Notificações')

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <div class="page-eyebrow">Master</div>
            <h1 class="m-0 text-dark" style="font-weight: 700;">Central de Notificações</h1>
            <div class="page-subtitle">Envie avisos para os usuários das empresas clientes</div>
        </div>
        <div class="col-lg-6 text-center text-lg-right">
            <button class="btn custom-btn custom-btn-success" data-toggle="modal" data-target="#novaNotificacaoModal">
                <i class="fas fa-paper-plane mr-1"></i> Nova Notificação
            </button>
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
                'pageLength' => 15,
                'ordering' => true,
                'showFooter' => false,
            ])
                <thead class="table-light">
                    <tr>
                        <th>Data</th>
                        <th class="text-left">Título</th>
                        <th>Tipo</th>
                        <th class="text-left">Destino</th>
                        <th>Lidas</th>
                        <th class="text-left">Criado por</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($notificacoes as $notificacao)
                        @php
                            $badge = match ($notificacao->tipo) {
                                'urgente' => 'badge-danger',
                                'aviso' => 'badge-warning',
                                default => 'badge-info',
                            };
                        @endphp
                        <tr>
                            <td>{{ $notificacao->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-left">{{ $notificacao->titulo }}</td>
                            <td><span class="badge {{ $badge }} text-uppercase">{{ $notificacao->tipo }}</span></td>
                            <td class="text-left">{{ $notificacao->empresa->fantasia ?? 'Todas as empresas' }}</td>
                            <td>{{ $notificacao->lidas_count }}/{{ $notificacao->leituras_count }}</td>
                            <td class="text-left">{{ $notificacao->criador->name ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            @endcomponent
        </div>
    </div>

    <div class="modal fade" id="novaNotificacaoModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Notificação</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('notificacoes.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-control" name="titulo" maxlength="255" required value="{{ old('titulo') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Mensagem</label>
                            <textarea class="form-control" name="mensagem" rows="4" required>{{ old('mensagem') }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-control" name="tipo" required>
                                <option value="info" @selected(old('tipo') == 'info')>Informação</option>
                                <option value="aviso" @selected(old('tipo') == 'aviso')>Aviso</option>
                                <option value="urgente" @selected(old('tipo') == 'urgente')>Urgente</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Destino</label>
                            <select class="form-control" name="destino" id="destino" required onchange="document.getElementById('empresaDestinoWrapper').classList.toggle('d-none', this.value !== 'empresa')">
                                <option value="todas" @selected(old('destino', 'todas') == 'todas')>Todas as empresas clientes</option>
                                <option value="empresa" @selected(old('destino') == 'empresa')>Uma empresa específica</option>
                            </select>
                        </div>
                        <div class="form-group mb-3 {{ old('destino') == 'empresa' ? '' : 'd-none' }}" id="empresaDestinoWrapper">
                            <label class="form-label">Empresa</label>
                            <select class="form-control" name="empresa_id">
                                <option value="">Selecione</option>
                                @foreach ($empresas as $empresa)
                                    <option value="{{ $empresa->id }}" @selected(old('empresa_id') == $empresa->id)>{{ $empresa->fantasia }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn custom-btn custom-btn-success">
                            <i class="fas fa-paper-plane mr-1"></i> Enviar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
