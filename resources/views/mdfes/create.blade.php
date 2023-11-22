@extends('adminlte::page')

@section('title', 'Emitir MDFe')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3>Emitir MDFe</h3>
        </div>
    </div>
@stop

@section('content')

    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="row" style="text-align: center">
                    <div class="col">
                        <h2>Informações da MDFe</h2>
                    </div>
                </div>
            </div>

            <div class="card-body">

                {{-- <form action="" method="POST" enctype="multipart/form-data">

                    <div class="row">
                        <div class="col">
                            <div class="form-group">

                            </div>
                        </div>
                    </div>

                </form> --}}

            </div>
        </div>
    </div>

    <div class="modal fade bd-delete-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="meuModal"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">

                    <div class="col" style="text-align: center">
                        <div class="modal-title" style="text: center">
                            <h4>Apagar esta Nota ?</h4>
                        </div>
                    </div>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <p style="color: red; text-align: center">OBS: Você irá excluir todas as informações sobre esta nota!
                    </p>

                    <div class="" style="text-align: center">

                        <form action="{{ route('veiculos.delete') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('DELETE')
                            <div class="form-group">
                                <input type="hidden" step="0.01" class="form-control" id="mdfeId" name="mdfeId"
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

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        // $(document).ready(function() {
        //     $("#meuModal").show();
        // });

        // $(".fechar-modal").click(function() {
        //     $("#.bd-delete-modal-lg").hide();
        // });
    </script>
@stop
