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

    <input type="hidden" name="status" value="s">
    <div class="row" id="tanggal_form">
        <div class="col-6">
            <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker />
        </div>
        <div class="col-6">
            <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker />
        </div>
    </div>

    <div class="row" id="jml_hari_frm">
        <div class="col-12">
            <x-inputtext field="jmlhari" label="Jumlah Hari" icon="feather icon-file-text" />
        </div>
    </div>
    <div class="row" id="fileUpload1">
        <div class="col-12">
            <div class="form-group">
                <input type="file" name="sid" class="form-control">
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

        function loadjumlahhari() {
            var dari = $("#frmPengajuanizin").find("#dari").val();
            var sampai = $("#frmPengajuanizin").find("#sampai").val();
            var date1 = new Date(dari);
            var date2 = new Date(sampai);

            // To calculate the time difference of two dates
            var Difference_In_Time = date2.getTime() - date1.getTime();

            // To calculate the no. of days between two dates
            var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);

            //To display the final no. of days (result)
            var result = Difference_In_Days + 1;
            if (dari == "" || sampai == "") {
                var hasil = 0;
            } else {
                var hasil = result;
            }
            $("#jmlhari").val(hasil);
        }


        $("#frmPengajuanizin").find("#sampai,#dari").change(function(e) {
            loadjumlahhari();
        });



        $("#frmPengajuanizin").submit(function() {
            var nik = $("#nik").val();
            var dari = $("#frmPengajuanizin").find("#dari").val();
            var sampai = $("#frmPengajuanizin").find("#sampai").val();
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
            } else if (dari == "" || sampai == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmPengajuanizin").find("#dari").focus();
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
