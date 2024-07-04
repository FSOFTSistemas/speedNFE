@extends('adminlte::page')

@section('title', 'Atualizar Usuário')

@section('content_header')
    <div class="text-center">
        <h3 class="m-0">Atualizar Usuário</h3>
    </div>
@stop

@section('content')
    <div class="row text-right">
        <div class="col">
            <a class="btn btn-secondary mb-3" href="{{ route('index_usuario') }}">Voltar</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="row g-3 needs-validation" novalidate action="{{ route('usuario.update', [$user->id]) }}"
                method='POST'>
                @csrf
                @method('PUT')

                <div class="row mt-4">
                    <div class="col-md-7 col-12">
                        <div class="input-group has-validation mb-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="name" id="name"
                                    value="{{ $user->name }}" />
                                <label>Nome</label>
                                <div class="invalid-feedback">
                                    Informe um nome.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-6">
                        <div class="input-group has-validation mb-2">
                            <div class="form-floating">
                                <select required class="form-select" name="cargo" id="cargo">
                                    <option value="">Selecione uma Permissão</option>
                                    <option value="master" @if ($user->cargo == 'master') selected @endif>master
                                    </option>
                                    <option value="admin" @if ($user->cargo == 'admin') selected @endif>admin
                                    </option>
                                    <option value="client-NFe" @if ($user->cargo == 'client-NFe') selected @endif>
                                        Apenas NFe</option>
                                    <option value="client-NFCe" @if ($user->cargo == 'client-NFCe') selected @endif>
                                        Apenas NFCe</option>
                                    <option value="client-MDFe" @if ($user->cargo == 'client-MDFe') selected @endif>
                                        Apenas MDFe</option>
                                    <option value="client-CTe" @if ($user->cargo == 'client-CTe') selected @endif>
                                        Apenas CTe</option>
                                    <option value="client-advanced1" @if ($user->cargo == 'client-advanced1') selected @endif>
                                        NFe e MDFe</option>
                                    <option value="client-advanced2" @if ($user->cargo == 'client-advanced2') selected @endif>
                                        NFe e NFCe</option>
                                    <option value="client-advanced3" @if ($user->cargo == 'client-advanced3') selected @endif>
                                        CTe e MDFe</option>
                                </select>
                                <label>Permissões</label>
                                <div class="invalid-feedback">
                                    Informe uma permissão.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        @if (auth()->user()->can('master'))
                            <div class="input-group has-validation mb-2">
                                <div class="form-floating">
                                    <select class="form-select" name="empresa" id="empresa" required>
                                        <option value="{{ $user->empresa_id }}">{{ $user->empresa->fantasia }}</option>
                                        @foreach ($empresas as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->fantasia }}</option>
                                        @endforeach
                                    </select>
                                    <label>Empresa</label>
                                    <div class="invalid-feedback">
                                        Informe uma empresa.
                                    </div>
                                </div>
                            </div>
                        @else
                            <input hidden name="empresa" id="empresa" value="{{ $user->empresa_id }}" />
                        @endif
                    </div>
                </div>

                <div class="text-center mt-2">
                    <button type="submit" class="btn btn-outline-success w-25">Salvar</button>
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
