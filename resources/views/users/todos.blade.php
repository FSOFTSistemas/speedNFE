@extends('adminlte::page')

@section('title', 'Usuários')

@section('content_header')
    <div class="row" style="text-align: center">
        <div class="col">
            <h1 class="m-0 text-dark">Usuários</h1>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <a href="{{ route('cadastrar_usuario') }}" class='btn btn-info'>&nbsp;+ Usuário&nbsp;</a>
        </div>
        <div class="col" style="text-align: end">
            <a href="{{ route('empresa.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
@stop

@section('content')
    <table class="table table-hover" id="users" style="width: 100%">
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
                    <td>
                        <div class="row">
                            <div class="col">
                                <a title="Editar" href="{{ route('editar_usuario', ['id' => $user->id]) }}"><i class="fa fa-edit text-info"></i></a>
                            </div>

                            <div class="col">
                                <a title="Deletar" data-toggle="modal" data-target="#modalExcluirUsuario" onclick="setaDadosModal({{ $user->id }})"><i class="fa fa-trash text-danger"></i></a>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @component('components.modal', [
        'modalId' => 'modalExcluirUsuario',
        'modalTitle' => 'Excluir Usuários',
        'sizeModal' => 'modal-md',
    ])
        <form action="{{ route('excluir_usuario') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('DELETE')
            <div class="row" style="text-align: center">
                <div class="col">
                    <input type="hidden" name="userId" id="userId" required>
                    <p class="text-danger"><b>OBS:</b> Isso irá deletar permanentemente o registro!</p>
                    <button class="btn btn-warning text-light" type="submit">Excluir</button>
                </div>
            </div>
        </form>
    @endcomponent

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
        function setaDadosModal(userId) {
            document.getElementById('userId').value = userId
        }

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
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json',
            },
        });
    </script>
@endsection
