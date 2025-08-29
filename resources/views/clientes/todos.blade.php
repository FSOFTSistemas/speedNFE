@extends('adminlte::page')

@section('title', 'Clientes')

@push('css')
<style>
    /* Importa a fonte Poppins para consistência */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    /* Variáveis de cor */
    :root {
        --primary-color: #00033a; /* Tom de azul marinho ajustado */
        --accent-blue: #3498db;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --border-color: #dee2e6;
        --text-dark: #343a40;
        --text-light: #6c757d;
        --action-edit: #28a745;
        --action-delete: #dc3545;
        --action-view: #17a2b8;
    }

    /* Estilo base da página */
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
    
    .custom-btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: #fff;
        font-weight: 500;
        border-radius: 8px;
        padding: 12px 20px; /* Aumenta o padding vertical para um botão mais robusto */
        transition: all 0.3s ease;
    }
    .custom-btn-primary:hover {
        background-color: #00045e; /* Tom mais claro para o hover */
        border-color: #00045e;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        color:#fff;
    }
    
    /* Estilos da Tabela */
    .table thead th {
        background-color: #f8f9fa;
        color: var(--text-dark);
        font-weight: 600;
        border-bottom: 2px solid var(--border-color);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Estilos dos Botões de Ação */
    .action-buttons a {
        color: var(--text-light);
        margin: 0 8px;
        font-size: 1.1rem;
        transition: color 0.3s ease;
    }
    .action-buttons a.text-edit:hover { color: var(--action-edit); }
    .action-buttons a.text-delete:hover { color: var(--action-delete); }
    .action-buttons a.text-view:hover { color: var(--action-view); }
    
    /* Modal de Exclusão */
    .modal-header {
        border-bottom: 1px solid var(--border-color);
    }
    .modal-content {
        border-radius: 15px;
        border: none;
    }
    .modal-footer {
        border-top: 1px solid var(--border-color);
    }
    .btn-delete-confirm {
        background-color: var(--action-delete);
        color: #fff;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col-12 text-center">
            <h1 class="m-0 text-dark" style="font-weight: 600;">Clientes</h1>
        </div>
    </div>
@stop

@section('content')
    {{-- Botão de novo cliente centralizado e mais largo --}}
    <div class="row mb-4">
        <div class="col-md-6 mx-auto text-center">
            <a class="btn custom-btn-primary btn-block" href="{{ route('cliente.create') }}">
                <i class="fas fa-plus mr-1"></i> Novo Cliente
            </a>
        </div>
    </div>

    <div class="card card-main">
        <div class="card-body p-0">
            <div class="table-responsive">
                {{-- A classe 'text-nowrap' foi removida para permitir quebra de linha em telas menores --}}
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            {{-- Coluna visível apenas em telas médias ou maiores --}}
                            <th class="d-none d-md-table-cell">CPF/CNPJ</th>
                            <th class="text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clientes as $cliente)
                            @if ($cliente->situacao == 0)
                                <tr>
                                    <td>{{ $cliente->nome }}</td>
                                    {{-- Coluna visível apenas em telas médias ou maiores --}}
                                    <td class="d-none d-md-table-cell">{{ $cliente->cpf_cnpj }}</td>
                                    <td class="text-right action-buttons">
                                        <a title="Visualizar" href='{{ route('cliente.view', ['id' => $cliente->id]) }}'
                                            class='text-view'><i class="far fa-eye"></i></a>
                                        <a title="Editar" href='{{ route('editar_cliente', ['id' => $cliente->id]) }}'
                                            class='text-edit'><i class="far fa-edit"></i></a>
                                        <a title="Excluir" href="#" onclick="setaDadosModal({{ $cliente->id }})" class='text-delete' data-toggle="modal"
                                            data-target="#deleteModal"><i class="far fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Nenhum cliente cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p>Tem certeza que deseja apagar este cliente?<br>
                    <strong class="text-danger">Todas as informações relacionadas a ele serão perdidas.</strong></p>
                    <form action="{{ route('excluir_cliente') }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="idCliente" name="idCliente">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger btn-delete-confirm" onclick="document.getElementById('deleteForm').submit();">Excluir</button>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        // Seta o ID do cliente no input hidden do modal
        function setaDadosModal(idCliente) {
            document.getElementById('idCliente').value = idCliente;
        }
    </script>
@endpush

