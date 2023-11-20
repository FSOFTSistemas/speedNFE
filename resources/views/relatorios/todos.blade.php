@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h1>Relatórios</h1>
        </div>
    </div>
@stop

@section('content')

    <div class="box">
        <div class="row">
            <div class="col-md-6 col-xs-12">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <h3>NFe's</h3>
                        </div>
                        <form action="{{ route('relatorio') }}" method="POST" target="_blank">
                            @csrf

                            @if ($empresa == 1)
                                <label>Empresa</label>
                                <select name="empresa" class="form-control">
                                    <option value="%">-- Todas --</option>
                                    @foreach ($empresas as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->fantasia }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="empresa" value="{{ $empresa }}">
                            @endif

                            <label>Período</label>
                            <div class="row">
                                <div class="col-6">
                                    <input class="form-control" type="month" required name="inicio" min="2022-01" max="2030-12"
                                        value="{{ date_format(today(), 'Y-m') }}">
                                </div>
                                <div class="col-6">
                                    <input class="form-control" type="month" required name="fim" min="2022-01" max="2030-12"
                                        value="{{ date_format(today(), 'Y-m') }}">
                                </div>
                            </div>

                            <label>Status</label>
                            <select class="form-control" name="status">
                                <option value="%">-- Todas --</option>
                                <option value="Aprovado">Autorizadas</option>
                                <option value="Cancelado">Canceladas</option>
                            </select>

                            <div class="text-center" style="padding-top: 6%">
                                <button type="submit" class="btn btn-success" value="nfe" name="tipoR">Gerar
                                    PDF</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
