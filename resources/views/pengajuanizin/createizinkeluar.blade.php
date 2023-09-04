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
    <input type="hidden" name="jenis_izin" value="KL">
    <div class="row" id="tanggal_form">
        <div class="col-12">
            <x-inputtext label="Tanggal" value="{{ date('Y-m-d') }}" field="dari" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row" id="jam_keluar_form">
        <div class="col-12">
            <x-inputtext label="Jam Keluar (HH:MM)" field="jam_keluar" icon="feather icon-clock" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="keperluan" class="form-control" id="keperluan">
                    <option value="">Keperluan</option>
                    <option value="P">Pribadi</option>
                    <option value="K">Kantor</option>
                </select>
            </div>
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
        $('#jam_keluar').mask('00:00');
        $("#frmPengajuanizin").submit(function() {
            var nik = $("#nik").val();
            var dari = $("#frmPengajuanizin").find("#dari").val();
            var keterangan = $("#keterangan").val();
            var jam_keluar = $("#jam_keluar").val();
            var keperluan = $("#keperluan").val();
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
            } else if (jam_keluar == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jam Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jam_keluar").focus();
                });

                return false;
            } else if (keperluan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keperluan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#keperluan").focus();
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
