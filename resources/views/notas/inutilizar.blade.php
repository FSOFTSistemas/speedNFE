@extends('adminlte::page')

@section('title', 'Inutilizar Nota Fiscal')

@section('content_header')
<div class="row" style="text-align: center">
    <div class="col">
        <h3 class="m-0 text-dark">Inutilizar Nota Fiscal</h3>
    </div>
</div>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <div class="row" style="text-align: center">
            <div class="col">
                <h2>Justificativa de Inutilização</h2>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="/inutilizar">
            @csrf
            <input type="hidden" value="{{$empresa}}" name="empresa_id">
            <div class="row">
                <div class="col-6">
                    <label>Série</label><br>
                    <input class="form-control" type="number" step="1" min="1" name="serie" required placeholder="Série...">
                </div>
                <div class="col-6">
                    <label>Número</label><br>
                    <input class="form-control" type="number" step="1" min="1" name="numI" required placeholder="Nº...">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>Justificativa</label><br>
                    {{-- <input type="textarea" name="justificativa"> --}}
                    <textarea name="justificativa" class="form-control" width="100%" rows="5" required placeholder="Justificativa..."></textarea>
                </div>
            </div>
            <div style="padding-top: 2%" class="text-center">
                <button class="btn btn-success" style="width: 25%" type="submit">Salvar</button>
            </div>
        </form>
    </div>
</div>

@endsection