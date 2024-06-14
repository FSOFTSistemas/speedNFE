@php
    $uniqueId = 'myTable_' . uniqid();
@endphp

<table class="table table-hover" id="{{ $uniqueId }}" style="width: 100%">
    {{ $slot }}
</table>

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link
        href="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-2.0.3/af-2.7.0/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/cr-2.0.0/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.1/sc-2.4.1/sb-1.7.0/sp-2.3.0/datatables.min.css"
        rel="stylesheet">
@endsection

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script
    src="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-2.0.3/af-2.7.0/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/cr-2.0.0/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.1/sc-2.4.1/sb-1.7.0/sp-2.3.0/datatables.min.js">
</script>

<script>
    $('#{{ $uniqueId }}').DataTable({
        lengthChange: {{ Js::from($lengthChange) }},
        searching: {{ Js::from($searching) }},
        responsive: true,
        columnDefs: {{ Js::from($responsive) }},
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json',
        },
    });
    console.log('Ola seu gay' + {{ $uniqueId }})
</script>
