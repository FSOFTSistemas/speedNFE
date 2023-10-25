@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Categorias</h1>
@stop

@section('content')

    <div class="container">
        <a class="btn btn-info" href="{{route('cadastrar_categoria')}}">&nbsp; + Categoria &nbsp;</a>

        <table class="table table-striped">
            <thead>
                <th>DESCRIÇÃO</th>
                <th>STATUS</th>
                @if($empresa == 1)
                    <th>EMPRESA</th>
                @endif
                <th></th>
            </thead>
            <tbody>
                @foreach ($categorias as $categoria)
                    <tr>
                        <td>{{$categoria->descricao}}</td>
                        @if($categoria->status == 1)
                            <td>Ativa</td>
                        @else
                            <td>Inativa</td>
                        @endif
                        @if($empresa == 1)
                            <td>{{$categoria->fantasia}}</td>
                        @endif
                        <td>
                            <a class="btn btn-warning" href="{{route('desativarReativar_categoria', ['id' => $categoria->id])}}">
                                @if($categoria->status == 1)
                                    Desativar
                                @else
                                    Reativar
                                @endif
                            </a>
                        </td>

                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>

@endsection