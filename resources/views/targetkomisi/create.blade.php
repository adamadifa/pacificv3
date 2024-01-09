<input type="hidden" value="{{ $kode_target }}" id="kode_target">
<div class="row">
    <div class="col-12">
        <div class="form-group">
            @if ($cabangaktif == 'PCF')
                <select name="kode_cabang" id="kode_cabang" class="form-control">
                    <option value="">Pilih Cabang</option>
                    @foreach ($cabang as $d)
                        <option value="{{ $d->kode_cabang }}">{{ strtoupper($d->nama_cabang) }}</option>
                    @endforeach
                </select>
            @else
                <input type="hidden" name="kode_cabang" id="kode_cabang" class="form-control"
                    value="{{ $cabangaktif }}">
            @endif
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <thead class="thead-dark" style="text-align:center;">
                <tr>
                    <th rowspan="2" style="vertical-align:center !important">ID Sales</th>
                    <th rowspan="2">Nama Sales</th>
                    <th colspan="13">Target Quantity</th>
                </tr>
                <tr>
                    <th>AB</th>
                    <th>AR</th>
                    <th>AS</th>
                    <th>BB</th>
                    {{-- <th>CG</th>
                    <th>CGG</th> --}}
                    <th>DEP</th>
                    {{-- <th>DS</th> --}}
                    <th>SP</th>
                    {{-- <th>CG5</th> --}}
                    <th>SC</th>
                    <th>SP8</th>
                    <th>SP500</th>
                    <th>BR20</th>
                </tr>
            </thead>
            <tbody id="loadlisttarget">

            </tbody>
        </table>
    </div>
</div>

<script>
    $(function() {
        function loadlisttarget() {
            var kode_target = $("#kode_target").val();
            var kode_cabang = $("#kode_cabang").val();
            $.ajax({
                type: 'POST',
                url: '/targetkomisi/getlisttarget',
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_target: kode_target,
                    kode_cabang: kode_cabang
                },
                cache: false,
                success: function(respond) {
                    $("#loadlisttarget").html(respond);
                }
            });
        }
        loadlisttarget();
        $("#kode_cabang").change(function() {
            loadlisttarget();
        });

    });
</script>
