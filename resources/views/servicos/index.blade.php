@extends('adminlte::page')

@section('title', 'Serviços')

@push('css')
<style>
    :root {
        --primary-color: #00033a;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --info-color: #17a2b8;
        --success-color: #28a745;
    }
    .card-main {
        background: var(--card-bg);
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px var(--shadow-color);
        padding: 30px;
    }
    .custom-btn { font-weight: 500; border-radius: 8px; }
    .custom-btn-info { background-color: var(--info-color) !important; border-color: var(--info-color) !important; color: #fff !important; }
    .custom-btn-success { background-color: var(--success-color) !important; border-color: var(--success-color) !important; color: #fff !important; }
    .action-buttons a, .action-buttons button { color: #6c757d; margin: 0 6px; font-size: 1.1rem; background: none; border: none; }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark">Serviços (catálogo NFCom)</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right">
            <button type="button" class="btn custom-btn custom-btn-info" data-toggle="modal" data-target="#createModal">
                <i class="fas fa-plus mr-1"></i> Novo Serviço
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
            'pageLength' => 10,
            'ordering' => true,
            'showFooter' => false,
        ])
            <thead class="table-light">
                <tr>
                    <th>Código</th>
                    <th class="text-left">Descrição</th>
                    <th>cClass</th>
                    <th>Unidade</th>
                    <th>Valor</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($servicos as $servico)
                    <tr>
                        <td>{{ $servico->codigo }}</td>
                        <td class="text-left">{{ $servico->descricao }}</td>
                        <td>{{ $servico->cClass }}</td>
                        <td>{{ $servico->uMed }}</td>
                        <td>R$ {{ number_format($servico->valor, 2, ',', '.') }}</td>
                        <td class="action-buttons text-right">
                            <button type="button" title="Editar" data-toggle="modal" data-target="#editModal{{ $servico->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" title="Excluir" data-toggle="modal" data-target="#deleteModal{{ $servico->id }}">
                                <i class="fas fa-trash text-danger"></i>
                            </button>
                        </td>
                    </tr>

                    @component('components.modal', [
                        'modalId' => 'editModal' . $servico->id,
                        'modalTitle' => 'Editar Serviço',
                        'sizeModal' => 'modal-lg',
                    ])
                        <form action="{{ route('servicos.update', [$servico->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @include('servicos._campos', ['servico' => $servico])
                            <div class="text-right mt-2">
                                <button type="submit" class="btn custom-btn custom-btn-success">Salvar</button>
                            </div>
                        </form>
                    @endcomponent

                    @component('components.modal', [
                        'modalId' => 'deleteModal' . $servico->id,
                        'modalTitle' => 'Excluir Serviço',
                        'sizeModal' => 'modal-sm',
                    ])
                        <form action="{{ route('servicos.destroy') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="servico_id" value="{{ $servico->id }}">
                            <p>Tem certeza que deseja excluir o serviço "{{ $servico->descricao }}"?</p>
                            <div class="text-right">
                                <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                            </div>
                        </form>
                    @endcomponent
                @endforeach
            </tbody>
        @endcomponent
    </div>
</div>

@component('components.modal', [
    'modalId' => 'createModal',
    'modalTitle' => 'Novo Serviço',
    'sizeModal' => 'modal-lg',
])
    <form action="{{ route('servicos.store') }}" method="POST">
        @csrf
        @include('servicos._campos')
        <div class="text-right mt-2">
            <button type="submit" class="btn custom-btn custom-btn-success">Salvar</button>
        </div>
    </form>
@endcomponent
@stop
