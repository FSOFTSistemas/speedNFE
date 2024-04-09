@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')

@stop

@section('content')

<div class="container" style="padding-top: 20px">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Relatórios</div>

                <div class="card-body">
                    <form id="relatorioForm">
                        <div class="form-group">
                            <label for="tipo_relatorio">Selecione o Tipo de Relatório:</label>
                            <select class="form-control" id="tipo_relatorio" name="tipo_relatorio">
                                <option value="nfe">Selecione Um tipo de Relatório</option>
                                <option value="tipoR1">Relatório tipo 1</option>
                                <option value="tipoR2">Relatório tipo 2</option>
                                <option value="tipoR3"> Relatorio tipo 3</option>

                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select class="form-control" id="status" name="status">
                                <option value="%">Todos</option>
                                <option value="Aprovado">Aprovado</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inicio">Data Início:</label>
                            <input type="date" class="form-control" id="inicio" name="inicio">
                        </div>
                        <div class="form-group">
                            <label for="fim">Data Fim:</label>
                            <input type="date" class="form-control" id="fim" name="fim">
                        </div>
                        <button type="submit" class="btn btn-primary">Gerar Relatório</button>
                    </form>

                </div>
            </div>
            <table class="table table-hover" id="produtos">
                <thead class="table-primary">
                    <tr>
                        <th>ID Pedido</th>
                        <th>Estado</th>
                        <th>Data</th>
                        <th>Valor Total</th>
                    </tr>
                </thead>
                <tbody id="tbody-pedidos">
                    <!-- Aqui serão inseridos os dados do relatório -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#relatorioForm').submit(function(event) {
            event.preventDefault(); // Evita o envio do formulário padrão
            var tipoRelatorio = $('#tipo_relatorio').val();
            var status = $('#status').val();
            var inicio = $('#inicio').val();
            var fim = $('#fim').val();
            var empresa = $('#empresa').val();

            // Enviar solicitação AJAX para o endpoint do relatório
            $.ajax({
                url: '{{ route("relatorio") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    tipoR: tipoRelatorio,
                    status: status,
                    inicio: inicio,
                    fim: fim,
                    empresa: empresa
                },
                success: function(response) {
                    // Limpar os dados da tabela
                    $('#tbody-pedidos').empty();
                    // Atualizar a tabela com os dados do relatório
                    response.forEach(function(pedido) {
                        $('#tbody-pedidos').append('<tr>' +
                            '<td>' + pedido.id + '</td>' +
                            '<td>' + pedido.estado + '</td>' +
                            '<td>' + pedido.data + '</td>' +
                            '<td>' + pedido.valor_total + '</td>' +
                            '</tr>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    // Tratar erro, exibir mensagem de erro, etc.
                }
            });
        });
    });
</script>
@endpush
