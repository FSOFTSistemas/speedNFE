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
            <form action="/zip" method="POST">
                @csrf
                <div class="row">
                    <div class="col-3">
                        <input class="form-control" type="month" name="periodo" min="2022-01" max="2030-12"
                            value="{{ date_format(today(), 'Y-m') }}">
                    </div>
                    <div class="col-3">
                        <button class="btn btn-info" type="submit">Download ZIP</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col">
            <div class="text-right">
                <a class="btn btn-info" href="/inutilizar">Inutilizar Notas</a>
            </div>
        </div>
    </div>

    <br>
    <table class="table table-hover" id="xmls">
        <thead class="table-primary" style="text-align: center">
            <th>Chave</th>
            <th>Valor</th>
            <th>Estado</th>
            <th></th>
            <th></th>
        </thead>

        <tbody style="text-align: center">
            @foreach ($notas as $nota)
                <tr>
                    <td><a href="/venda/imprimir/{{ $nota->id }}" target="_blank">{{ $nota->chave }}</a></td>
                    <td>R$ {{ number_format($nota->total, 2) }}</td>
                    <td>{{ $nota->estado }}</td>
                    <td><a title="XML" href="/notas/xml/{{ $nota->chave }}" class="text-success"><i class="fa fa-file-code"></i></a></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>

        {{-- <tfoot>
            <th>{{ $notas->links() }}</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tfoot> --}}
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
