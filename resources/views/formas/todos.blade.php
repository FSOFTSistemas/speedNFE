@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Empresas</h1>
@stop

@section('content')
    <div class="container">
        @if($empresa == 1)
            <a href='/forma/cadastro' class="btn btn-info">&nbsp;+ Forma de Pagamento&nbsp;</a>
        @endif
        <table class="table table-striped">
            <thead>
                <th>DESCRICAO</th>
            </thead>

            <tbody>
                @foreach($formas as $forma)
                    <tr>
                        <td>{{$forma->descricao}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection