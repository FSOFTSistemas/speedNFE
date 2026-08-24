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
    .servico-form-section {
        border: 1px solid var(--border-color);
        border-radius: 6px;
        margin-bottom: 16px;
        padding: 16px 16px 4px;
    }
    .servico-form-title {
        color: #477b57;
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 14px;
        text-transform: uppercase;
    }
    .servico-form-tabs {
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 16px;
    }
    .servico-form-tabs .nav-link {
        color: #5f6b76;
        font-weight: 600;
        padding: 10px 16px;
    }
    .servico-form-tabs .nav-link.active {
        color: var(--primary-color);
        border-color: var(--border-color);
        border-bottom-color: #fff;
    }
    .servico-form-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef var(--border-color);
    }
    .servico-form-tab-content {
        min-height: 300px;
    }
    @media (max-width: 575.98px) {
        .servico-form-tabs .nav-item {
            flex: 1 1 100%;
        }
        .servico-form-tabs .nav-link {
            border-radius: 0;
        }
    }
    .modal .select2-container {
        width: 100% !important;
    }
    .modal .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da;
        border-radius: .25rem;
        height: calc(2.25rem + 2px);
    }
    .modal .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: calc(2.25rem + 2px);
        padding-left: .75rem;
    }
    .modal .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px);
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark">Serviços</h1>
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
                    <th>Classificação</th>
                    <th>Tributação nacional</th>
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
                        <td>{{ $servico->cTribNac ?: '-' }}</td>
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
                        'sizeModal' => 'modal-xl',
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
    'sizeModal' => 'modal-xl',
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

@push('js')
<script>
    $(function () {
        const dominioUrl = @json(route('servicos.dominios-nfse', ['tipo' => '__TIPO__']));

        // A tabela carrega componentes Bootstrap de versões diferentes do AdminLTE.
        // A troca manual mantém as abas funcionando independentemente do plugin ativo.
        $(document).on('click', '.servico-form-tabs .nav-link', function (event) {
            event.preventDefault();

            const $link = $(this);
            const $tabs = $link.closest('.servico-form-tabs');
            const target = $link.attr('href');
            const $content = $tabs.next('.servico-form-tab-content');

            if (!target || !$content.length) return;

            $tabs.find('.nav-link')
                .removeClass('active')
                .attr('aria-selected', 'false');
            $content.find('.tab-pane')
                .removeClass('show active');

            $link
                .addClass('active')
                .attr('aria-selected', 'true');
            $content.find(target).addClass('show active');
        });

        const initServicoSelect2 = ($scope) => {
            if (!$.fn.select2) return;

            $scope.find('.servico-select2').each(function () {
                const $select = $(this);
                if ($select.data('select2')) return;

                const $modal = $select.closest('.modal');
                const domain = $select.data('domain');
                const config = {
                    placeholder: $select.data('placeholder') || 'Selecione',
                    allowClear: !this.required,
                    width: '100%',
                    dropdownParent: $modal.length ? $modal : $(document.body),
                    language: {
                        noResults: () => 'Nenhum resultado encontrado',
                        searching: () => 'Buscando...',
                        inputTooShort: () => 'Digite para pesquisar',
                    },
                };

                if (domain) {
                    config.ajax = {
                        url: dominioUrl.replace('__TIPO__', domain),
                        dataType: 'json',
                        delay: 250,
                        data: (params) => ({
                            q: params.term || '',
                            page: params.page || 1,
                        }),
                        processResults: (data) => data,
                        cache: true,
                    };
                }

                $select.select2(config);
            });
        };

        $('.modal').on('shown.bs.modal', function () {
            initServicoSelect2($(this));
        });
    });
</script>
@endpush
