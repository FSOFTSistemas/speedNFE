<div>

    <div class="max-w-3xl mx-auto mb-2">
        <div class="bg-white rounded-lg p-5">

            <div class="grid sm:grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div wire:ignore>
                    <select class="form-control" id="select2">
                        <option value="">Select Option</option>
                        @foreach($items as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    You have selected: <strong>{{ $selected }}</strong>
                </div>
            </div>


        </div>
    </div>

</div>

{{-- @push('scripts')

    <script>
        $(document).ready(function () {
            $('#select2').select2();
            $('#select2').on('change', function (e) {
                var data = $('#select2').select2("val");
            @this.set('selected', data);
            });
        });
    </script>

@endpush --}}