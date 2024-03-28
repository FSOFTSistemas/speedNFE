@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h5 class="m-0 text-dark">Produtos</h5>
        </div>
    </div>
@stop

@section('content')

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
                    <th>CÓDIGO</th>
                    <th>PRODUTO</th>
                    <th>PREÇO CUSTO</th>
                    <th>PREÇO VENDA</th>
                    <th>CATEGORIA</th>
                    @if ($empresa == 1)
                        <th>EMPRESA</th>
                    @endif
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
                        <td>
                            <div class="row">
                                <div class="col">
                                    <a class="text-warning" title="Editar"
                                        href="{{ route('editar_produto', ['id' => $produto->id]) }}"><i
                                            class="fa fa-edit"></i></a>
                                </div>

                                <div class="col">
                                    <a class="text-danger" title="Excluir" onclick="setaDadosModal({{ $produto->id }})"><i
                                            data-toggle="modal" data-target=".bd-delete-modal-lg"
                                            class="fa fa-trash"></i></a>
                                </div>

                                <div class="col">
                                    <a class="text-primary" title="Visualizar"
                                        href="{{ route('ver_produto', [$produto->id]) }}"><i class="fa fa-eye"></i></a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>


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

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <link
        href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.1/b-3.0.0/b-colvis-3.0.0/b-html5-3.0.0/b-print-3.0.0/cr-2.0.0/date-1.5.2/r-3.0.0/sr-1.4.0/datatables.min.css"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
@stop

@section('js')

    <script src="https://code.jquery.com/jquery-3.7.0.js" integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM="
        crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script
        src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.1/b-3.0.0/b-colvis-3.0.0/b-html5-3.0.0/b-print-3.0.0/cr-2.0.0/date-1.5.2/r-3.0.0/sr-1.4.0/datatables.min.js">
    </script>
    <script>
        function setaDadosModal(idProduto) {
            document.getElementById('idProduto').value = idProduto;
        }

        $(document).ready(function() {
            $('#produtos').DataTable({
                responsive: true,

                columnDefs: [{
                        responsivePriority: 1,
                        targets: 0
                    },
                    {
                        responsivePriority: 2,
                        targets: -1
                    },
                    // {
                    //     responsivePriority: 3,
                    //     targets: 2
                    // },

                    // {
                    //     responsivePriority: 4,
                    //     targets: 6
                    // },

                ],

                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
                },
            });
        });
    </script>
@stop
