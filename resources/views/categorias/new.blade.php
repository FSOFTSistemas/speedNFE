@extends('adminlte::page')

@section('title', 'Cadastrar Categoria')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h3 class="m-0 text-dark">Cadastro de Categoria</h3>
        </div>
    </div>
@stop

@section('content')
    <div class="text-right mb-3">
        <a href="{{ route('categoria.index') }}" class="btn btn-secondary">Voltar</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="row g-3 needs-validation" novalidate action="{{ route('salvar_categoria') }}" method="post">
                @csrf

                @if ($empresa == 1)
                    <div class="row mt-3">
                        <div class="col">
                            <div class="input-group has-validation mb-2">
                                <div class="form-floating">
                                    <select class="form-select" name="empresa" id="empresa" required>
                                        <option value="">Selecione uma Empresa</option>
                                        @foreach ($empresas as $emp)
                                            <option value="{{ $emp->id }}" @if (old('empresa')) selected @endif>{{ $emp->fantasia }}</option>
                                        @endforeach
                                    </select>
                                    <label for="empresa">Empresa</label>
                                    <div class="invalid-feedback">
                                        Informe uma empresa.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col">
                        <div class="input-group has-validation mb-2">
                            <div class="form-floating">
                                <textarea class="form-control" name="descricao" id="descricao" style="height: 150px" required>{{ old('descricao') }}</textarea>
                                <label for="descricao">Descrição</label>
                                <div class="invalid-feedback">
                                    Informe um tipo válido.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-outline-success w-25">Salvar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
        (() => {
            'use strict'

            const forms = document.querySelectorAll('.needs-validation')

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
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
