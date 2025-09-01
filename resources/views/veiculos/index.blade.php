@extends('adminlte::page')

@section('title', 'Veículos')

@push('css')
<style>
    /* Estilos do Padrão Visual Definido */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    :root {
        --primary-color: #00033a;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --text-dark: #343a40;
        --text-light: #6c757d;
        --action-delete: #dc3545;
        --action-edit: #ffc107;
        --success-color: #28a745;
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
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .custom-btn-primary { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; color: #fff !important; }
    .custom-btn-danger { background-color: var(--action-delete) !important; border-color: var(--action-delete) !important; color: #fff !important; }

    .header-buttons .btn { display: block; margin-bottom: 8px; }
    .header-buttons .btn:last-child { margin-bottom: 0; }
    @media (min-width: 992px) {
        .header-buttons .btn { display: inline-block; margin-bottom: 0; margin-left: 8px; }
    }
    
    .table thead th, .table tbody td {
        background-color: transparent !important;
        vertical-align: middle;
        text-align: center;
    }
    .table thead th {
        color: var(--text-dark) !important;
        font-weight: 600;
        border-bottom: 2px solid var(--border-color) !important;
        text-transform: uppercase;
    }
    .table tbody tr:hover { background-color: #f1f1f1 !important; }
    .table td.text-left { text-align: left; }

    .action-buttons {
        white-space: nowrap;
        text-align: right;
    }
    .action-buttons a {
        color: var(--text-light);
        margin: 0 8px;
        font-size: 1.2rem;
        transition: color 0.3s ease;
    }
    .action-buttons a:hover.text-warning { color: var(--action-edit) !important; }
    .action-buttons a:hover.text-danger { color: var(--action-delete) !important; }

    .modal-content { border-radius: 15px; border: none; }
    .modal-header { border-bottom: 1px solid var(--border-color); }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-3 mb-lg-0">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Veículos</h1>
        </div>
        <div class="col-lg-6 text-center text-lg-right header-buttons">
            <a class="btn custom-btn custom-btn-primary" href="{{ route('veiculos.create') }}">
                <i class="fas fa-plus mr-1"></i> Novo Veículo
            </a>
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
                    <th>Id</th>
                    <th>Placa</th>
                    <th>CPF/CNPJ</th>
                    @if ($empresa == 1)
                        <th class="text-left">Empresa</th>
                    @endif
                    <th>Propriedade</th>
                    <th>Tara(Kg)</th>
                    <th>Capacidade(M³)</th>
                    <th>Tipo</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($veiculos as $veiculo)
                    <tr>
                        <td>#{{ $veiculo->id }}</td>
                        <td>{{ $veiculo->placa }}</td>
                        <td>{{ $veiculo->cpf_cnpj }}</td>
                        @if ($empresa == 1)
                            <td class="text-left">{{ $veiculo->fantasia }}</td>
                        @endif
                        <td>{{ $veiculo->tipo_propriedade }}</td>
                        <td>{{ number_format($veiculo->tara, 1, ',', '.') }}</td>
                        <td>{{ number_format($veiculo->capacidade_m3, 1, ',', '.') }}</td>
                        <td>{{ $veiculo->tipo_veiculo }}</td>
                        <td class="action-buttons">
                            <a title="Editar" href='{{ route('veiculos.edit', [$veiculo->id]) }}' class='text-warning'><i class="fa fa-edit"></i></a>
                            <a title="Excluir" href="#" onclick="setaDadosModal({{ $veiculo->id }}, '{{ $veiculo->placa }}')" class='text-danger' data-toggle="modal" data-target="#deleteModal"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endcomponent
    </div>
</div>

<!-- Modal de Exclusão Genérico -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Apagar este Veículo?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p>Tem certeza que deseja excluir o veículo de placa <br> <strong id="veiculoPlaca" class="text-danger"></strong>?</p>
                <p class="text-danger mt-3"><b>Atenção:</b> Esta ação é irreversível!</p>
                <form id="deleteForm" action="{{ route('veiculos.delete') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="veiculoID" name="veiculoID">
                </form>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn custom-btn custom-btn-danger" onclick="document.getElementById('deleteForm').submit();">Sim, Excluir</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
        function setaDadosModal(veiculoID, veiculoPlaca) {
            // Seta o ID no input hidden
            document.getElementById('veiculoID').value = veiculoID;

            // Seta a placa do veículo no corpo do modal para confirmação
            document.getElementById('veiculoPlaca').textContent = veiculoPlaca;
        }
    </script>
@stop