<form action="/kesepakatanbersama/{{ $kb->no_kb }}/update" method="post" id="frmKesepakatanBersama">
    @csrf
    <div class="row">

        <div class="col-12">
            <x-inputtext label="Tanggal Kesepaatan Bersama" field="tanggal" value="{{ $kb->tgl_kb }}" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="tahun" id="tahun_kb" class="form-control">
                    <option value="">Tahun Pemutihan</option>
                    @for($thn = 2019; $thn<=date('Y'); $thn++) <option {{ $kb->tahun == $thn ? 'selected' : '' }} value="{{ $thn }}">{{ $thn }}</option>
                        @endfor
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit"><i class="feather icon-send mr-1"></i> Buat Kesepakatan Bersama</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {
        $("#frmKesepakatanBersama").submit(function(e) {
            var tanggal = $("#frmKesepakatanBersama").find("#tanggal").val();
            var tahun = $("#tahun_kb").val();
            if (tanggal == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi!'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmKesepakatanBersama").find("#tanggal").focus();
                });
                return false;
            } else if (tahun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tahun Pemutihan Harus Diisi!'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tahun_kb").focus();
                });
                return false;
            }
        });
    });

</script>
