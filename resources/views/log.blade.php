@extends('adminlte::page')

@section('title', 'Log de Transações')

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
        --success-color: #28a745;
        --info-color: #17a2b8;
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
    
    /* Estilos da Tabela */
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
        letter-spacing: 0.5px;
    }
    .table tbody tr:hover {
        background-color: #f1f1f1 !important;
    }
    .table td.text-left { text-align: left; }

    /* Estilo do botão de ação na tabela */
    .action-button {
        background: none;
        border: none;
        color: var(--text-light);
        font-size: 1.2rem;
        transition: color 0.3s ease;
    }
    .action-button:hover {
        color: var(--info-color) !important;
    }
    
    /* Estilos do Modal Melhorado */
    .modal-content { border-radius: 15px; border: none; }
    .modal-header { border-bottom: 1px solid var(--border-color); }
    .log-details-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 5px;
    }
    .log-details-table td {
        padding: 8px;
        border-bottom: 1px solid #f0f0f0;
    }
    .log-details-table td:first-child {
        font-weight: 600;
        color: var(--text-dark);
        width: 30%;
    }
    /* Destaque para dados alterados */
    .data-changed {
        background-color: #fffbe6 !important; /* Amarelo claro */
    }
    .data-changed td:first-child {
        font-weight: 700 !important;
    }
</style>
@endpush

@section('content_header')
    <h1 class="m-0 text-dark" style="font-weight: 600;">Log de Transações do Sistema</h1>
@stop

@section('content')
<div class="card card-main">
    <div class="card-body p-0">
        @component('components.dataTable', [
            'responsive' => true,
            'searching' => true,
            'lengthChange' => true,
            'pageLength' => 50,
            'ordering' => true,
            'showFooter' => false,
        ])
            <thead class="table-light">
                <tr>
                    <th>Id</th>
                    <th class="text-left">Tabela</th>
                    <th>Ação</th>
                    <th class="text-left">Usuário</th>
                    <th>Data</th>
                    <th>Detalhes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td>#{{ $log->id }}</td>
                        <td class="text-left">{{ $log->tabela_afetada }}</td>
                        <td>
                            @if ($log->acao == 'create') <span class="badge badge-success">CRIAÇÃO</span>
                            @elseif ($log->acao == 'update') <span class="badge badge-warning">ATUALIZAÇÃO</span>
                            @elseif ($log->acao == 'delete') <span class="badge badge-danger">EXCLUSÃO</span>
                            @else <span class="badge badge-secondary">{{ $log->acao }}</span>
                            @endif
                        </td>
                        <td class="text-left">{{ $log->usuario->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->updated_at)->format('d/m/Y H:i:s') }}</td>
                        <td>
                            <button type="button" class="action-button" data-toggle="modal" data-target="#logModal" 
                                    data-dados-anteriores="{{ json_encode($log->dados_anteriores) }}" 
                                    data-dados-atuais="{{ json_encode($log->dados_atuais) }}">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endcomponent
    </div>
</div>

<div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="logModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logModalLabel">Detalhes do Log de Transação</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Dados Anteriores (Antes da Ação)</h6>
                        <div class="table-responsive">
                            <table class="log-details-table" id="dadosAnterioresTable">
                                </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Dados Atuais (Depois da Ação)</h6>
                        <div class="table-responsive">
                            <table class="log-details-table" id="dadosAtuaisTable">
                                </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    $('#logModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var dadosAnterioresStr = button.data('dados-anteriores');
        var dadosAtuaisStr = button.data('dados-atuais');

        // Tenta fazer o parse dos dados. Se falhar, usa um objeto vazio.
        var dadosAnteriores = {};
        try {
            if (dadosAnterioresStr && dadosAnterioresStr !== 'null') {
                dadosAnteriores = JSON.parse(dadosAnterioresStr);
            }
        } catch(e) { console.error("Erro ao parsear dados_anteriores:", e); }

        var dadosAtuais = {};
        try {
            if (dadosAtuaisStr && dadosAtuaisStr !== 'null') {
                dadosAtuais = JSON.parse(dadosAtuaisStr);
            }
        } catch(e) { console.error("Erro ao parsear dados_atuais:", e); }
        
        var anterioresTable = $('#dadosAnterioresTable');
        var atuaisTable = $('#dadosAtuaisTable');
        
        anterioresTable.empty();
        atuaisTable.empty();

        // Combina todas as chaves de ambos os objetos para garantir que todos os campos sejam mostrados
        var allKeys = [...new Set([...Object.keys(dadosAnteriores), ...Object.keys(dadosAtuais)])];
        
        if (allKeys.length === 0) {
            atuaisTable.append('<tr><td colspan="2" class="text-muted">Nenhum dado detalhado registrado.</td></tr>');
            return;
        }

        allKeys.forEach(function(key) {
            var valorAnterior = dadosAnteriores[key] !== undefined ? dadosAnteriores[key] : '---';
            var valorAtual = dadosAtuais[key] !== undefined ? dadosAtuais[key] : '---';
            
            // Verifica se o valor mudou para aplicar o destaque
            var isChanged = String(valorAnterior) !== String(valorAtual);
            var rowClass = isChanged ? 'data-changed' : '';

            anterioresTable.append(`<tr class="${rowClass}"><td>${key}</td><td>${valorAnterior}</td></tr>`);
            atuaisTable.append(`<tr class="${rowClass}"><td>${key}</td><td>${valorAtual}</td></tr>`);
        });
    });
});
</script>
@stop