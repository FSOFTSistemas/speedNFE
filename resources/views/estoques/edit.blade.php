@extends('adminlte::page')

@section('title', 'Editar Estoque')

@section('content_header')
    <div class="text-center text-dark">
        <h3>Edição de Estoque</h3>
    </div>
@stop

@section('content')
    <div class="row text-right mb-3">
        <div class="col">
            <a class="btn btn-secondary" href="#">Voltar</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col">
                    <h4>{{ $estoque->produto->produto }}</h4>
                </div>
            </div>

            <form class="g-3 needs-validation" novalidate action="" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" name="estoque" id="estoque" value="{{ $estoque->estoque_atual }}" placeholder=" " required>
                            <label for="estoque">Estoque Atual</label>
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" name="estoque_anterior" id="estoque_anterior" value="{{ $estoque->estoque_anterior }}" placeholder=" "
                                required>
                            <label for="estoque_anterior">Estoque Anterior</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" name="entradas" id="entradas" value="{{ $estoque->entradas }}" placeholder=" " required>
                            <label for="entradas">Entradas</label>
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" name="saidas" id="saidas" value="{{ $estoque->saidas }}" placeholder=" " required>
                            <label for="saidas">Saidas</label>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button class="btn btn-outline-success w-25" type="submit">Salvar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script>
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')

            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
@endsection
