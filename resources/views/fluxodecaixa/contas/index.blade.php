@extends('adminlte::page')

@section('title', 'Plano de Contas')

@section('content_header')
    <div class="row text-center">
        <div class="col">
            <h3 class="m-0 text-dark">Plano de Contas</h3>
        </div>
    </div>
@stop

@section('content')
    <div class="row mb-2">
        <div class="col">
            <button class="btn btn-primary mb-1" data-toggle="modal" data-target="#modalCreate">&nbsp;+ Nova Conta&nbsp;</button>
        </div>
    </div>

    @component('components.dataTable', [
        'responsive' => [
            ['responsivePriority' => 1, 'targets' => 0],
            ['responsivePriority' => 2, 'targets' => 1],
            ['responsivePriority' => 3, 'targets' => 2],
            ['responsivePriority' => 4, 'targets' => 3],
            ['responsivePriority' => 5, 'targets' => 4],
            ['responsivePriority' => 6, 'targets' => -1],
        ],
        'searching' => true,
        'lengthChange' => true,
        'pageLength' => 10,
        'ordering' => true,
        'showFooter' => false,
    ])
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Descrição</th>
                <th>Tipo</th>
                <th>Conta Pai</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($contas as $conta)
                <tr>
                    <td>{{ $conta->id }}</td>
                    <td>{{ $conta->codigo }}</td>
                    <td>{{ $conta->descricao }}</td>
                    <td>{{ $conta->tipo }}</td>
                    <td>{{ $conta->contaPai->descricao ?? '-' }}</td>
                    <td>
                        <div class="row">
                            <div class="col">
                                <a class="text-warning" title="Editar"
                                    onclick="setaDadosModalEdit({{ $conta->id }}, '{{ $conta->codigo }}', '{{ $conta->descricao }}', '{{ $conta->tipo }}', {{ $conta->conta_pai_id ?? 'null' }})"
                                    data-toggle="modal" data-target="#modalEdit">
                                    <i class="far fa-edit"></i>
                                </a>
                            </div>

                            <div class="col">
                                <a class="text-danger" title="Excluir"
                                    onclick="setaDadosModalDelete({{ $conta->id }})"
                                    data-toggle="modal" data-target="#modalDelete">
                                    <i class="far fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @include('fluxodecaixa.contas.modals.edit')
                @include('fluxodecaixa.contas.modals.delete')
            @endforeach
        </tbody>
    @endcomponent

    <!-- Modais -->
    @include('fluxodecaixa.contas.modals.create')

@endsection

@section('js')
    <script>
        // Função para preencher os campos do modal de edição
        function setaDadosModalEdit(id, codigo, descricao, tipo, contaPai) {
            $('#edit-id').val(id);
            $('#edit-codigo').val(codigo);
            $('#edit-descricao').val(descricao);
            $('#edit-tipo').val(tipo);
            $('#edit-conta-pai').val(contaPai);
        }

        // Função para preencher os dados do modal de exclusão
        function setaDadosModalDelete(id) {
            $('#delete-id').val(id);
        }
    </script>
@stop