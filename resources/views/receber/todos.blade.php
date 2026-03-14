@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Contas a Receber</h1>
@stop

@section('content')

    <div class="containter">
        <div style="padding-bottom: 2%">
            <a href="/receber/cadastro" class="btn btn-info">&nbsp;+ Contas a Receber&nbsp;</a>
        </div>
        <table class="table table-striped">
            <tr>
                <th>ID</th>
                <th>CLIENTE</th>
                <th>VALOR ORIGINAL</th>
                <th>VALOR PAGO</th>
                <th>VALOR ATUAL</th>
                <th>VENCIMENTO</th>
                <th>SITUACAO</th>
                <th></th>
                <th></th>
            </tr>
            @php($total = 0)
            <tbody id="geeks">
            @foreach($recebimentos as $recebimento)
                @php($total = $total + $recebimento->valor_atual)
                <tr>
                    <td><strong>#{{$recebimento->id}}</strong></td>
                    <td>{{$recebimento->nome}}</td>
                    <td>R$ {{number_format($recebimento->valor_original,2)}}</td>
                    <td>R$ {{number_format($recebimento->valor_pago,2)}}</td>
                    <td>R$ {{number_format($recebimento->valor_atual,2)}}</td>
                    <td>{{date('d/m/Y', strtotime($recebimento->vencimento))}}</td>
                    @if($recebimento->status == 0)
                        <td>Pendente</td>
                        <td><a class="btn btn-success" href="{{ route('editar_recebimento', ['id'=>$recebimento->id]) }}">Receber</a></td>
                        <td></td>
                        @if($recebimento->valor_pago > 0)
                            {{-- <td><a class="btn btn-warning" href="{{ //route('comprovante', ['id'=>$recebimento->id]) }}" target="_blank" title="">Comprovante</a></td> --}}
                        @else
                            <td></td>
                        @endif
                    @elseif($recebimento->status)
                        <td>Pago</td>
                        <td></td>
                        <td></td>
                        {{-- <td><a class="btn btn-warning" href="{{ //route('comprovante', ['id'=>$recebimento->id]) }}" target="_blank" title="">Comprovante</a></td> --}}
                    @endif
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr class="table-active">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><strong>Total: R$ {{number_format($total, 2)}}</strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

@endsection