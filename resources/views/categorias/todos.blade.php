@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h1 class="m-0 text-dark">Categorias</h1>
        </div>
    </div>
@stop

@section('content')

    <div class="container">
        <a class="btn btn-info" style="margin-bottom: 2%" href="{{ route('cadastrar_categoria') }}">&nbsp; + Categoria
            &nbsp;</a>

        <table class="table table-hover" id="categorias">
            <thead class="table-primary">
                <th>DESCRIÇÃO</th>
                <th>STATUS</th>
                @if ($empresa == 1)
                    <th>EMPRESA</th>
                @endif
                <th></th>
            </thead>
            <tbody>
                @foreach ($categorias as $categoria)
                    <tr>
                        <td>{{ $categoria->descricao }}</td>
                        @if ($categoria->status == 1)
                            <td>Ativa</td>
                        @else
                            <td>Inativa</td>
                        @endif
                        @if ($empresa == 1)
                            <td>{{ $categoria->fantasia }}</td>
                        @endif
                        <td>
                            <a class="btn btn-warning"
                                href="{{ route('desativarReativar_categoria', ['id' => $categoria->id]) }}">
                                @if ($categoria->status == 1)
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

@section('js')
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#categorias').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
                },
            });
        });
    </script>
@stop
