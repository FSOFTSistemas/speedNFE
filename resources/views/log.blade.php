@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Log</h1>
@stop

@section('content')

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
            'targets' => 5, 
        ]

    ],
    'searching' => true,
    'lengthChange' => true,
    'pageLength' => 50,
    'ordering' => true,
    'showFooter' => false,
])
    <thead class="table-primary" style="width: 100%">
        <tr>
            <th>Id</th>
            <th>Tabela</th>
            <th>Ação</th>
            <th>Usuário</th>
            <th>Data</th>
            <th>View</th>
        </tr>
    </thead>
    <tbody style="width: 100%">
        @foreach ($logs as $log)
            <tr>
                <td>#{{ $log->id }}</td>
                <td>{{ $log->tabela_afetada }}</td>
                <td>{{ $log->acao }}</td>
                <td>{{ $log->usuario->name }}</td>
                <td>{{ \Carbon\Carbon::parse($log->updated_at)->format('d/m/Y H:i:s') }}</td>
                <td>
                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#logModal" 
                        data-dados-anteriores="{{ json_encode($log->dados_anteriores) }}" 
                        data-dados-atuais="{{ json_encode($log->dados_atuais) }}">
                        Ver Dados
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
  
@endcomponent

<!-- Modal -->
<div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="logModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logModalLabel">Detalhes do Log de Transação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Dados Anteriores -->
                <h6><strong>Dados Anteriores</strong></h6>
                <ul id="dadosAnterioresList"></ul>

                <!-- Dados Atuais -->
                <h6><strong>Dados Atuais</strong></h6>
                <ul id="dadosAtuaisList"></ul>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    #dadosAnterioresList li, #dadosAtuaisList li {
    margin-bottom: 5px;
}

#dadosAnterioresList li strong, #dadosAtuaisList li strong {
    color: #007bff;
}
</style>
@stop

@section('js')
<!-- jQuery (necessário para o modal funcionar com Bootstrap) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$('#logModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); 
    var dadosAnteriores = button.data('dados-anteriores'); 
    var dadosAtuais = button.data('dados-atuais'); 

    var dadosAnterioresObj = isValidJson(dadosAnteriores) ? JSON.parse(dadosAnteriores) : null;
    var dadosAtuaisObj = isValidJson(dadosAtuais) ? JSON.parse(dadosAtuais) : null;

    var anterioresList = $('#dadosAnterioresList');
    anterioresList.empty();
    
    if (dadosAnterioresObj != 'null'){
       objdados =  JSON.parse(dadosAnterioresObj);
        
       for (var key in objdados) {
            if (objdados.hasOwnProperty(key)) {  
                //console.log(key + ': ' + objdados[key]);
                anterioresList.append('<li><strong>' + key + ':</strong> ' + objdados[key] + '</li>');
            }
        }
    }

    var atuaisList = $('#dadosAtuaisList');
    atuaisList.empty(); 

    if (dadosAtuaisObj != 'null'){
       objdados =  JSON.parse(dadosAtuaisObj);
        
       for (var key in objdados) {
            if (objdados.hasOwnProperty(key)) {  
                //console.log(key + ': ' + objdados[key]);
                atuaisList.append('<li><strong>' + key + ':</strong> ' + objdados[key] + '</li>');
            }
        }
    }
});

// Função para verificar se o dado é uma string JSON válida
function isValidJson(str) {
    try {
        JSON.parse(str);
        return true;  // Se o JSON for válido, retorna true
    } catch (e) {
        return false;  // Se houver erro ao fazer o parse, retorna false
    }
}
</script>
@stop