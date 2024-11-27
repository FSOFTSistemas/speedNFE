@extends('adminlte::page')

@section('title', 'Inutilizar Nota Fiscal')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3 class="m-0 text-dark">Inutilizar Faixa de Nº</h3>
        </div>
    </div>
@stop

@section('content')

    <div class="row" style="margin-bottom: 2%">
        <div class="col">
            <a class="btn btn-secondary" href="{{ $mode == "nfce" ? route('cupom.index') : route('notas.index') }}">
                Voltar
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="g-3 needs-validation" novalidate method="POST" @if ($mode == "nfce") action="{{ route('nfce.unuse') }}" @else action="/inutilizar" @endif>
                @csrf
                <input type="hidden" value="{{ $empresa }}" name="empresa_id">

                <div class="row">
                    <div class="col">
                        <div class="form-floating">
                            <input type="number" class="form-control" step="1" min="1" name="numI"
                                id="numI" placeholder=" " required>
                            <label for="numI">Número Inicial</label>
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-floating">
                            <input type="number" class="form-control" step="1" min="1" name="numF"
                                id="numF" placeholder=" " required>
                            <label for="numF">Número Final</label>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col">
                        <div class="form-floating">
                            <textarea class="form-control" placeholder=" " name="justificativa" id="justificativa" style="height: 150px;" minlength="15" maxlength="1200"
                                required></textarea>
                            <label for="justificativa">Justificativa</label>
                        </div>
                    </div>
                </div>

                <div class="pt-3 text-center">
                    <button class="btn btn-outline-success w-25" type="submit">Salvar</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
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
