@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="text-center">
        <h3 class="m-0 text-black" width="100%"><b>Relatórios</b></h3>
    </div>
@stop

@section('content')

    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <h3>NFe's</h3>
                    </div>
                    <form action="{{route('relatorio')}}" method="POST">
                        @csrf

                        @if($empresa == 1)
                            <label>Empresa</label>
                            <select name="empresa" class="form-control">
                                <option value="%">--todas--</option>
                                @foreach($empresas as $emp)
                                    <option value="{{$emp->id}}">{{$emp->fantasia}}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="hidden" name="empresa" value="{{$empresa}}">
                        @endif


                        <label>Período</label>
                        <div class="row">
                            <div class="col-6">
                                <input class="form-control" type="month" name="inicio" min="2022-01" max="2030-12" value="{{date_format(today(), 'Y-m')}}">
                            </div>
                            <div class="col-6">
                                <input class="form-control" type="month" name="fim" min="2022-01" max="2030-12" value="{{date_format(today(), 'Y-m')}}">
                            </div>
                        </div>

                        <label>Status</label>
                        <select class="form-control" name="status">
                            <option value="%">--todas--</option>
                            <option value="Aprovado">Autorizadas</option>
                            <option value="Cancelado">Canceladas</option>
                        </select>

                        <div class="text-center" style="padding-top: 6%">
                            <button type="submit" class="btn btn-success" value="nfe" name="tipoR">Gerar PDF</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-6">

        </div>
    </div>

@endsection