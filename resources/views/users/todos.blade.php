@extends('adminlte::page')

@section('title', 'Usuários')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h1 class="m-0 text-dark">Usuários</h1>
        </div>
    </div>
@stop

@section('content')

    <div>
        @if ($logged->cargo == 'admin')
            <a href="{{ route('cadastrar_usuario') }}" class='btn btn-info'>&nbsp;+ Usuário&nbsp;</a>
        @else
            <a disabled href="{{ route('cadastrar_usuario') }}" class='btn btn-info'>&nbsp;+ Usuário&nbsp;</a>
        @endif
    </div>
    <table class="table table-hover" id="users">
        <thead class="table-primary">
            <th>ID</th>
            <th>LOGIN</th>
            <th>CARGO</th>
            <th>EMPRESA</th>
            <th></th>
        </thead>

        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>#{{ $user->id }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->cargo }}</td>
                    <td>{{ $user->fantasia }}</td>
                    @if ($logged->cargo == 'admin')
                        <td><a class="btn btn-warning" href="{{ route('editar_usuario', ['id' => $user->id]) }}">Editar</a>
                            <a class="btn btn-danger"
                                href="route{{ route('excluir_usuario', ['id' => $user->id]) }}">Excluir</a>
                        </td>
                    @else
                        <td><a disabled class="btn btn-warning"
                                href="{{ route('editar_usuario', ['id' => $user->id]) }}">Editar</a>
                            <a disabled class="btn btn-danger"
                                href="{{ route('excluir_usuario', ['id' => $user->id]) }}">Excluir</a>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection

@section('css')
    <link
        href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.0.3/cr-2.0.0/fh-4.0.1/kt-2.12.0/r-3.0.1/sc-2.4.1/sp-2.3.0/datatables.min.css"
        rel="stylesheet">
@endsection

@section('js')
    <script
        src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.0.3/cr-2.0.0/fh-4.0.1/kt-2.12.0/r-3.0.1/sc-2.4.1/sp-2.3.0/datatables.min.js">
    </script>

    <script>
        $('#users').DataTable({
            responsive: true,
            columnDefs: [{
                    responsivePriority: 1,
                    targets: 1
                },
                {
                    responsivePriority: 2,
                    targets: 2
                },
                {
                    responsivePriority: 3,
                    targets: -1
                },
                {
                    responsivePriority: 4,
                    targets: 0
                },
                {
                    responsivePriority: 5,
                    targets: 3
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
            },
        });
    </script>
@endsection
