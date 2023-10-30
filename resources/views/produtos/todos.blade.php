@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h1 class="m-0 text-dark">Produtos</h1>
        </div>
    </div>
@stop

@section('content')

    <div class="container">
        <div class="row">
            <div class="col">
                <a class="btn btn-info" style="margin-bottom: 2%"
                    href="{{ route('categoria.index') }}">&nbsp;Categorias&nbsp;</a>
                <a class="btn btn-info" style="margin-bottom: 2%" href="/produto/cadastro">&nbsp;+ Produto&nbsp;</a>
            </div>
        </div>

        <table class="table table-hover" id="produtos">
            <thead class="table-primary">
                <tr>
                    <th>CODIGO</th>
                    <th>PRODUTO</th>
                    <th>PRECO CUSTO</th>
                    <th>PRECO VENDA</th>
                    <th>CATEGORIA</th>
                    @if ($empresa == 1)
                        <th>EMPRESA</th>
                    @endif
                    {{-- <th>estoque atual</th> --}}
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach ($produtos as $produto)
                    <tr>
                        <td>{{ $produto->codigo }}</td>
                        <td>{{ $produto->produto }}</td>
                        <td>R$ {{ number_format($produto->precocusto, 2) }}</td>
                        <td>R$ {{ number_format($produto->precovenda, 2) }}</td>
                        <td>{{ $produto->descricao }}</td>
                        @if ($empresa == 1)
                            <td>{{ $produto->fantasia }}</td>
                        @endif
                        {{-- <td>estoque</td> --}}
                        <td><a class="text-warning" href="{{ route('editar_produto', ['id' => $produto->id]) }}"><i
                                    class="fa fa-edit"></i></a>
                        </td>
                        <td><a class="text-danger" onclick="setaDadosModal({{ $produto->id }})"><i data-toggle="modal"
                                    data-target=".bd-delete-modal-lg" class="fa fa-trash"></i></a></td>
                        <td><a class="text-primary" href="{{ route('ver_produto', [$produto->id]) }}"><i
                                    class="fa fa-eye"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade bd-delete-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">

                    <div class="col" style="text-align: center">
                        <div class="modal-title" style="text: center">
                            <h4>Apagar este Produto?</h4>
                        </div>
                    </div>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <p style="color: red; text-align: center">OBS: Você irá excluir todas as informações sobre este produto!
                    </p>

                    <div class="" style="text-align: center">

                        <form action="{{ route('excluir_produto') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('DELETE')
                            <div class="form-group">
                                <input type="hidden" step="0.01" class="form-control" id="idProduto" name="idProduto"
                                    value="">
                            </div>

                            <div class="text-center">
                                <button type="submit" style="width: 50%;" class="btn btn-danger">EXCLUIR</button>
                            </div>
                            <br>
                        </form>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script>
        function setaDadosModal(idProduto) {
            document.getElementById('idProduto').value = idProduto;
        }

        $(document).ready(function() {
            $('#produtos').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
                },
            });
        });
    </script>
@stop
