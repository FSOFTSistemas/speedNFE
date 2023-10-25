@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Notas Fiscais</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">
        <form method="POST" action="/inutilizar">
            @csrf
            <input type="hidden" value="{{$empresa}}" name="empresa_id">
            <div class="row">
                <div class="col-6">
                    <label>Série</label><br>
                    <input class="form-control" type="number" step="1" min="1" name="serie">
                </div>
                <div class="col-6">
                    <label>Número</label><br>
                    <input class="form-control" type="number" step="1" min="1" name="numI">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>Justificativa</label><br>
                    {{-- <input type="textarea" name="justificativa"> --}}
                    <textarea name="justificativa" class="form-control" width="100%" rows="5"></textarea>
                </div>
            </div>
            <div style="padding-top: 2%" class="text-center">
                <button class="btn btn-success" type="submit">Salvar</button>
            </div>
        </form>
    </div>
</div>

@endsection