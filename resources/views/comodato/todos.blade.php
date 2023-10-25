@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <a class="btn btn-primary" href="/comodato/new">Cadastrar novo produto em comodato</a>
            </div>
            <div class="col">
                <input type="text" class="form-control" placeholder="Filtrar" id="gfg" name="gfg">
            </div>
        </div>
        <br>
        <table class="table table-striped">
            <tr>
                <th>USUARIO</th>
                <th>CLIENTE</th>
                <th>PRODUTO</th>
                <th>DATA</th>
                <th>QUANTIDADE</th>
                <th>STATUS</th>
                <th></th>
                <th></th>
            </tr>
            @php($total = 0)
            <tbody id="geeks">
            @foreach($comodatos as $comodato)
                <tr>
                    <td>{{$comodato->name}}</td>
                    <td>{{$comodato->nome}}</td>
                    <td>{{$comodato->descricao}}</td>
                    <td>{{date('d/m/Y', strtotime($comodato->data))}}</td>
                    <td>{{number_format($comodato->qauntidade,2)}}</td>
                    @if($recebimento->status == 0)
                        <td>Pendente</td>
                        <td><a class="btn btn-success" href="">Receber</a></td>
                    @elseif($recebimento->status)
                        <td><a class="btn btn-success" href="">Ver</a></td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $("#gfg").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#geeks tr").filter(function() {
                    $(this).toggle($(this).text()
                    .toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
@endsection