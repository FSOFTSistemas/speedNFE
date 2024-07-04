@extends('adminlte::page')

@section('title', 'Usuários')

@section('content_header')
    <div class="row text-center">
        <div class="col">
            <h3 class="m-0">Usuários</h3>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <a href="{{ route('usuario.create') }}" class='btn btn-primary'>&nbsp;+ Usuário&nbsp;</a>
        </div>
        <div class="col text-right">
            <a href="{{ route('empresa.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
@stop

@section('content')

    @component('components.dataTable', [
        'responsive' => [
            [
                'responsivePriority' => 1,
                'targets' => 0,
            ],
            [
                'responsivePriority' => 2,
                'targets' => 1,
            ],
            [
                'responsivePriority' => 3,
                'targets' => 2,
            ],
            [
                'responsivePriority' => 4,
                'targets' => 3,
            ],
            [
                'responsivePriority' => 5,
                'targets' => -1,
            ],
        ],
        'searching' => true,
        'lengthChange' => true,
        'pageLength' => 10,
        'ordering' => true,
    ])
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
                    <td><b>#{{ $user->id }}</b></td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->cargo }}</td>
                    <td>{{ $user->fantasia }}</td>
                    <td>
                        <div class="row">
                            <div class="col">
                                <a title="Editar" href="{{ route('editar_usuario', ['id' => $user->id]) }}"><i
                                        class="far fa-edit text-teal"></i></a>
                            </div>

                            <div class="col">
                                <a title="Deletar" data-toggle="modal" data-target="#modalExcluirUsuario"
                                    onclick="setaDadosModal({{ $user->id }})"><i class="far fa-trash-alt text-danger"></i></a>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    @endcomponent

    @component('components.modal', [
        'modalId' => 'modalExcluirUsuario',
        'modalTitle' => 'Excluir Usuário',
        'sizeModal' => 'modal-md',
    ])
        <form action="{{ route('excluir_usuario') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('DELETE')

            <div class="row" style="text-align: center">
                <div class="col">
                    <input type="hidden" name="userId" id="userId" required>
                    <p class="text-danger"><b>OBS:</b> Isso irá deletar permanentemente o registro!</p>
                    <button class="btn btn-outline-warning" type="submit">Excluir</button>
                </div>
            </div>
        </form>
    @endcomponent

@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
        function setaDadosModal(userId) {
            document.getElementById('userId').value = userId
        }
    </script>
@endsection
