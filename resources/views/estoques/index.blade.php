@extends('adminlte::page')

@section('title', 'Estoque')

@section('content_header')
    <div class="text-center text-dark">
        <h3>Estoque</h3>
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
        ],
        'searching' => true,
        'lengthChange' => true,
    ])
        <thead class="table-primary">
            <th>ID</th>
            <th>PRODUTO</th>
            <th>ESTOQUE</th>
            <th></th>
        </thead>

        <tbody>
            @foreach ($estoques as $estoque)
                <tr>
                    <td><b>#{{ $estoque->id }}</b></td>
                    <td>{{ $estoque->produto }}</td>
                    <td>{{ $estoque->estoque }}</td>
                    <td>
                        <a title="Editar" href="{{ route('estoque.edit', [$estoque->id]) }}" class="text-teal"><i
                            class="far fa-edit"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    @endcomponent

@endsection
