<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<form method="POST" action="/pengajuanizin/storekoreksipresensi" id="frmKoreksipresensi" enctype="multipart/form-data">
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
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Presensi" field="tgl_presensi" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_jadwal" id="kode_jadwal" class="form-control">
                    <option value="">Pilih Jadwal</option>
                    @foreach ($jadwal as $d)
                    <option value="{{ $d->kode_jadwal }}">{{ $d->nama_jadwal }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="kode_jadwal_old" id="kode_jadwal_old">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <x-inputtext label="Jam Masuk" field="jam_masuk" icon="feather icon-clock" />
            <input type="hidden" id="jam_masuk_old" name="jam_masuk_old" />
        </div>
        <div class="col-6">
            <x-inputtext label="Jam Pulang" field="jam_pulang" icon="feather icon-clock" />
            <input type="hidden" id="jam_pulang_old" name="jam_pulang_old" />
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

        $("#tgl_presensi").change(function(e) {
            e.preventDefault();
            getpresensihariini();
        });

        $("#nik").change(function(e) {
            getpresensihariini();
        });

        function getpresensihariini() {
            var tgl_presensi = $("#tgl_presensi").val();
            var nik = $("#nik").val();
            var hariini = "{{ date('Y-m-d') }}";

            var start = new Date(hariini);
            var end = new Date(tgl_presensi);
            $.ajax({
                url: '/pengajuanizin/getpresensihariini'
                , type: 'POST'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tgl_presensi: tgl_presensi
                    , nik: nik
                }
                , cache: false
                , success: function(respond) {
                    var data = respond.split("|");
                    $("#jam_masuk").val(data[0]);
                    $("#jam_masuk_old").val(data[0]);
                    $("#jam_pulang").val(data[1]);
                    $("#jam_pulang_old").val(data[1]);
                    $("#kode_jadwal_old").val(data[2]);
                    var $select = $('#kode_jadwal').selectize();
                    var control = $select[0].selectize;
                    control.setValue(data[2]);

                }
            });
            //alert(start);
            // if (end.getTime() < start.getTime()) {
            //     $.ajax({
            //         url: '/pengajuanizin/getpresensihariini'
            //         , type: 'POST'
            //         , data: {
            //             _token: "{{ csrf_token() }}"
            //             , tgl_presensi: tgl_presensi
            //             , nik: nik
            //         }
            //         , cache: false
            //         , success: function(respond) {
            //             var data = respond.split("|");
            //             $("#jam_masuk").val(data[0]);
            //             $("#jam_masuk_old").val(data[0]);
            //             $("#jam_pulang").val(data[1]);
            //             $("#jam_pulang_old").val(data[1]);
            //             $("#kode_jadwal_old").val(data[2]);
            //             var $select = $('#kode_jadwal').selectize();
            //             var control = $select[0].selectize;
            //             control.setValue(data[2]);

            //         }
            //     });
            // } else {
            //     if (tgl_presensi != "") {
            //         swal({
            //             title: 'Oops'
            //             , text: 'Data Presensi yang dapat diubah, Hanya Data Persensi Sebelum Tanggal Hari ini !'
            //             , icon: 'warning'
            //             , showConfirmButton: false
            //         }).then(function() {
            //             $("#jam_masuk").val("");
            //             $("#jam_masuk_old").val("");
            //             $("#jam_pulang").val("");
            //             $("#jam_pulang_old").val("");
            //             $("#kode_jadwal_old").val("");
            //             var $select = $('#kode_jadwal').selectize();
            //             var control = $select[0].selectize;
            //             control.setValue("");
            //         });
            //         return false;
            //     }

            // }
        }
        $('#jam_masuk,#jam_pulang').mask('00:00');


        $("#nik,#kode_jadwal").selectize();

        $("#frmKoreksipresensi").submit(function(e) {
            var nik = $("#nik").val();
            var tgl_presensi = $("#tgl_presensi").val();
            var kode_jadwal = $("#kode_jadwal").val();
            var jam_masuk = $("#jam_masuk").val();
            var jam_pulang = $("#jam_pulang").val();
            var keterangan = $("#keterangan").val();

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
            } else if (tgl_presensi == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_presensi").focus();
                });
                return false;
            } else if (kode_jadwal == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jadwal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_jadwal").focus();
                });
                return false;
            } else if (jam_masuk == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jam Masuk Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jam_masuk").focus();
                });
                return false;
            } else if (jam_pulang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jam Pulang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jam_pulang").focus();
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
