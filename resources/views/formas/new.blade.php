@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Formas de Pagamento</h1>
@stop

@section('content')

    <div class="container">
        <div class='row'>
            <div class="col">
                <div class="card">
                    <form method='post' action="{{route('salvar_forma')}}">
                        @csrf
                        <div class='card-body'>
                            <label>Descrição</label>
                            <input class='form-control' type="text" name='descricao' id='descricao' />
                            <br>
                            @if($empresa == 1)
                                <select class='form-control' name="empresa" id="empresa">
                                    <option>-- Selecione a empresa --</option>
                                    @foreach($empresas as $emp)
                                        <option value="{{$emp->id}}">{{$emp->fantasia}}</option>
                                    @endforeach
                                </select>
                            @endif
                            <br>
                            <button class='btn btn-success' type='submit'>Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class='col'>
            </div>
        </div>
    </div>

@endsection