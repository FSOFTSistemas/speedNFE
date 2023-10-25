@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Cadastro de Estoque</h1>
@stop

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="{{route('salvar_estoque')}}">
                            @csrf

                            <label>Empresa</label>
                            @if($empresa == 1)
                                <select onchange="javascript:liberarProdutos()" class="form-control" name="empresa" id="empresa">
                                    <option value="0">--Escolha uma empresa--</option>
                                    @foreach($empresas as $emp)
                                        <option value="{{$emp->id}}">{{$emp->fantasia}}</option>
                                    @endforeach
                                </select>
                            @endif

                            <label>Produto</label>
                            <select disabled class="form-control" name="produto" id="produto">
                                <option value="0">--escolha um produto--</option>
                                @foreach($produtos as $produto)
                                    <option id="{{$produto->empresa_id}}" value="{{$produto->id}}">{{$produto->produto}}</option>
                                @endforeach
                            </select>

                            <script>
                                function liberarProdutos(){
                                    var input, filter, ul, li, a, i, txtValue;
                                    input = document.getElementById('empresa');
                                    filter = input.options[input.selectedIndex].value;
                                    select = document.getElementById("produto");
                                    option = select.getElementsByTagName('option');

                                    // Loop through all list items, and hide those who don't match the search query
                                    for (i = 0; i < option.length; i++) {
                                        a = option[i].id;
                                        if (a == filter) {
                                            option[i].style.display = "";
                                        } else {
                                            option[i].style.display = "none";
                                        }
                                    }
                                    document.getElementById('produto').removeAttribute('disabled');
                                }
                            </script>

                            <label>Estoque</label>
                            <input class="form-control" name="estoque" id="estoque" type="number" step="0.01" />

                            <div class="text-center" style="padding-top: 2%">
                                <button class="btn btn-success">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-2"></div>
        </div>
    </div>

@endsection