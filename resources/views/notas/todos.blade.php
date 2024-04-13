@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h1 class="m-0 text-dark">Notas Fiscais</h1>
        </div>
    </div>
@stop

@section('content')

    <div class="row">
        <div class="col">
            <form action="{{ route('zip') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-5 col-xs-12" style="margin-bottom: 3%">
                        <input class="form-control" type="month" name="periodo" required min="2022-01" max="2030-12"
                            value="{{ date_format(today(), 'Y-m') }}">
                    </div>
                    <div class="col-md-4 col-xs-12">
                        <button class="btn btn-info" type="submit">Download ZIP</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col">
            <div class="text-right">
                <a class="btn btn-info" href="{{ route('inutilizar.index') }}">Inutilizar Notas</a>
            </div>
        </div>
    </div>

    <br>
    <table class="table table-hover" id="xmls">
        <thead class="table-primary" style="text-align: center">
            <tr>
                <th>Número</th>
                <th>Chave</th>
                <th>Valor</th>
                <th>Estado</th>
                @if($empresa == 1)
                <th>Empresa</th>
                @endif
                <th></th>
                <th></th>
            </tr>
        </thead>

        <tbody style="text-align: center">
            @foreach ($notas as $nota)
                <tr>
                    <td>#{{ $nota->numero_nfe }}</td>
                    <td><a href="/venda/imprimir/{{ $nota->id }}" target="_blank">{{ $nota->chave }}</a></td>
                    <td>R$ {{ number_format($nota->total, 2) }}</td>
                    <td>{{ $nota->estado }}</td>
                    @if($empresa == 1)
                    <td>{{ $nota->fantasia }}</td>
                    @endif
                    <td>
                        <form action="{{ route('baixarXml') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="empresa" id="empresa" value="{{ $nota->fantasia }}">
                            <input type="hidden" name="chave" id="chave" value="{{ $nota->chave }}">
                            <input type="hidden" name="data" id="data" value="{{ $nota->data }}">
                            <input type="hidden" name="estado" id="estado" value="{{ $nota->estado }}">
                            <button class="btn btn-light" type="submit" title="Download XML"><i class="fa fa-file-code text-success"></i></button>
                        </form>
                    </td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>

    </table>

@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#xmls').DataTable({
                responsive: true,
                // pageLength: 5,
                // lengthMenu: [5, 10, 20],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
                },
            });
        });
    </script>
@stop
