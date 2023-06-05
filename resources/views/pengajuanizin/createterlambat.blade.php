<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<form method="POST" action="/pengajuanizin/store" id="frmPengajuanizin" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="nik" id="nik" class="form-control">
                    <option value="">Pilih Karyawan</option>
                    @foreach ($karyawan as $d)
                    <option value="{{ $d->nik }}">{{ $d->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <input type="hidden" name="status" value="i">
    <input type="hidden" name="jenis_izin" value="TL">
    <div class="row" id="tanggal_form">
        <div class="col-12">
            <x-inputtext label="Tanggal" field="dari" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row" id="jam_terlambat_form">
        <div class="col-12">
            <x-inputtext label="Jam Terlambat (HH:mm)" field="jam_terlambat" icon="feather icon-clock" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext field="keterangan" label="Keterangan" icon="feather icon-file-text" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary w-100"><i class="feather icon-send mr-1"></i>Kirim</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script>
    $(function() {
        $("#nik").selectize();
        $('#jam_terlambat').mask('00:00');
        $("#frmPengajuanizin").submit(function() {
            var nik = $("#nik").val();
            var dari = $("#frmPengajuanizin").find("#dari").val();
            var keterangan = $("#keterangan").val();
            var jam_terlambat = $("#jam_terlambat").val();
            if (nik == "") {
                swal({
                    title: 'Oops'
                    , text: 'Nik Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nik").focus();
                });
                return false;
            } else if (dari == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmPengajuanizin").find("#dari").focus();
                });
                return false;
            } else if (jam_terlambat == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jam Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jam_terlambat").focus();
                });

                return false;
            } else if (keterangan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keterangan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#keterangan").focus();
                });

                return false;
            }
        });
    });

</script>
