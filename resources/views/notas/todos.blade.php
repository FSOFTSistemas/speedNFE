@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Notas Fiscais</h1>
@stop

@section('content')

<div class="row">
    <div class="col">
        <form action="/zip" method="POST">
            @csrf
            <div class="row">
                <div class="col-3">
                    <input class="form-control" type="month" name="periodo" min="2022-01" max="2030-12" value="{{date_format(today(), 'Y-m')}}">
                </div>
                <div class="col-3">
                    <button class="btn btn-info" type="submit">Download ZIP</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col">
        <div class="text-right">
            <a class="btn btn-info" href="/inutilizar">Inutilizar Notas</a>
        </div>
    </div>
</div>

<br>
<table class="table table-striped">
    <thead>
        <th>Chave</th>
        <th>Valor</th>
        <th>Estado</th>
        <th></th>
        <th></th>
    </thead>

    <tbody>
        @foreach($notas as $nota)
        <tr>
            <td><a href="/venda/imprimir/{{$nota->id}}" target="_blank">{{$nota->chave}}</a></td>
            <td>{{$nota->total}}</td>
            <td>{{$nota->estado}}</td>
            <td><a href="/notas/xml/{{$nota->chave}}" class="btn btn-outline-success">XML</a></td>
            <td></td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <th>{{$notas->links()}}</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
    </tfoot>
</table>



@endsection