@extends('layouts.app')
@section('content')

@if(isset($mensagem))
    <div class="alert alert-success" role="alert">
    {{$mensagem}}
    </div>
@endif

    <div class="container">
        <div class="row">
            <div class='col'></div>
            <div class='col'></div>
        </div>  

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>USUARIO</th>
                    <th>SALDO</th>
                    <th>ULTIMA ABERTURA</th>
                    <th>ULTIMO FECHAMENTO</th>
                    <th></th>

                </tr>
            </thead>
            <tbody>
                @foreach($caixas as $caixa)
                    <tr>
                        <td>{{$caixa->name}}</td> 
                        <td>R$ {{number_format($caixa->saldo)}}</td> 
                        <td>{{date('d/m/Y', strtotime($caixa->abertura))}}</td> 
                        <td>{{date('d/m/Y', strtotime($caixa->fechamento))}}</td> 
                        @if($caixa->status == 1)
                            <td><a class="btn btn-danger" href="{{ route('fechar_caixa', ['id' => $caixa->id]) }}" title="Fechar caixa {{ $caixa->name }}">Fechar Caixa</a></td> 
                        @else
                            <td><a class="btn btn-warning" href="{{ route('editar_caixa', ['id' => $caixa->id]) }}" title="Abrir caixa {{ $caixa->name }}">Abrir Caixa</a></td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>



    </div>
@endsection 
