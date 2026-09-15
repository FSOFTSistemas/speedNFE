@extends('layouts.app')
@section('content')

    <div class="container">

        <div>
            @php( $total = 0)
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>DATA DE EMISSAO</th>    
                        <th>DESCRICAO</th>
                        <th>VALOR</th>
                    </tr>
                </thead>
                <tbody>
                    
                        @foreach($lancamentos as $lancamento)
                            @php( $total = $total + $lancamento->valor)
                           <tr> <td>{{$lancamento->created_at}}</td>
                            <td>{{$lancamento->descricao}}</td>
                            <td>{{$lancamento->valor}}</td></tr>
                        @endforeach
                    
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th>Total: </th>
                        <th>R$ {{number_format($total, 2)}}</th>
                    </tr>
                </tfoot>
            </table>
        
        </div>

        <form action="{{ route('close_caixa', ['id' => $caixa->id ]) }}", method="post">
            @csrf
            <input class="form-control" type="number" step="0.01" name="saldo" id="saldo" value="{{$total}}">
            <button type="submit" class="btn btn-success">Salvar</button>
        </form>
    </div>

@endsection

