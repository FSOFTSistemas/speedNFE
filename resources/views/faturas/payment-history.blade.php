@extends('adminlte::page')

@section('title', 'Histórico de pagamentos')

@section('content_header')
    <div class="text-center text-dark">
        <h3>Histórico de pagamentos</h3>
    </div>
@stop

@section('content')
    <section>
        <div class="card">
            <div class="card-content">
                <div class="card-body">
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
                        'pageLength' => 50,
                        'ordering' => true,
                        'showFooter' => false,
                    ])
                        <thead class="table-primary">
                            <tr>
                                <th>ID do pagamento</th>
                                <th>ID da fatura</th>
                                <th>Serviço</th>
                                <th>Pago em</th>
                                <th>Valor</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>534534</td>
                                <td>2543524</td>
                                <td>Parcela do speed</td>
                                <td>2024-08-14</td>
                                <td>R$64.99</td>
                            </tr>
                        </tbody>
                    @endcomponent
                </div>
            </div>
        </div>
    </section>
@endsection
