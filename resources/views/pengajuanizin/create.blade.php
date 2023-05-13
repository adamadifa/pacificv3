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

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="status" id="status" class="form-control">
                    <option value="">Permohonan</option>
                    <option value="i">Izin</option>
                    <option value="s">Sakit</option>
                    <option value="c">Cuti</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row" id="jenis_izin_form">
        <div class="col-12">
            <div class="form-group">
                <select name="jenis_izin" id="jenis_izin" class="form-control">
                    <option value="">Jenis Izin</option>
                    <option value="TM">Tidak Masuk Kantor</option>
                    <option value="PL">Pulang</option>
                    <option value="KL">Keluar Kantor</option>
                    <option value="TL">Terlambat</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row" id="jenis_cuti_form">
        <div class="col-12">
            <div class="form-group">
                <select name="jenis_cuti" id="jenis_cuti" class="form-control">
                    <option value="">Jenis Cuti</option>
                    @foreach ($mastercuti as $d)
                    <option value="{{ $d->kode_cuti }}">{{ $d->nama_cuti }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row" id="jam_pulang_form">
        <div class="col-12">
            <x-inputtext label="Jam Pulang" field="jam_pulang" icon="feather icon-clock" />
        </div>
    </div>
    <div class="row" id="jam_keluar_form">
        <div class="col-12">
            <x-inputtext label="Jam Keluar" field="jam_keluar" icon="feather icon-clock" />
        </div>
    </div>
    <div class="row" id="jam_terlambat_form">
        <div class="col-12">
            <x-inputtext label="Jam Terlambat" field="jam_terlambat" icon="feather icon-clock" />
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker />
        </div>
        <div class="col-6">
            <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row" id="fileUpload1">
        <div class="col-12">
            <div class="form-group">
                <input type="file" name="sid" class="form-control">
            </div>
        </div>
    </div>
    <div class="row" id="jml_hari_frm">
        <div class="col-12">
            <x-inputtext field="jmlhari" label="Jumlah Hari" icon="feather icon-file-text" />
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

        // $('#jam_pulang').datetimepicker({
        //     format: 'hh:mm'
        //     , autoclose: false
        // });

        $("#jam_pulang,#jam_keluar,#jam_terlambat").datetimepicker({
            format: 'HH:mm'
        });


        $("#nik").selectize();

        function hidesid() {
            $("#fileUpload1").hide();
        }

        function showsid() {
            $("#fileUpload1").show();
        }


        function hidejenisizin() {
            $("#jenis_izin_form").hide();
        }

        function showjenisizin() {
            $("#jenis_izin_form").show();
        }

        function hidejeniscuti() {
            $("#jenis_cuti_form").hide();
        }

        function showjeniscuti() {
            $("#jenis_cuti_form").show();
        }

        function hidejampulang() {
            $("#jam_pulang_form").hide();
        }

        function showjampulang() {
            $("#jam_pulang_form").show();

        }


        function hidejamterlambat() {
            $("#jam_terlambat_form").hide();
        }

        function showjamterlambat() {
            $("#jam_terlambat_form").show();

        }

        function hidejamkeluar() {
            $("#jam_keluar_form").hide();
        }

        function showjamkeluar() {
            $("#jam_keluar_form").show();

        }
        hidesid();
        hidejenisizin();
        hidejeniscuti();
        hidejampulang();
        hidejamkeluar();
        hidejamterlambat();



        $("#status").change(function() {
            var status = $(this).val();
            if (status == "s") {
                showsid();
            } else {
                hidesid();
            }

            if (status == "i") {
                showjenisizin();
            } else {
                hidejenisizin();
            }

            if (status == "c") {
                showjeniscuti();
            } else {
                hidejeniscuti();
            }
        });

        $("#jenis_izin").change(function() {
            var jenis_izin = $(this).val();
            if (jenis_izin == "PL") {
                showjampulang();
            } else {
                hidejampulang();

            }

            if (jenis_izin == "KL") {
                showjamkeluar();
            } else {
                hidejamkeluar();
            }

            if (jenis_izin == "TL") {
                showjamterlambat();
            } else {
                hidejamterlambat();
            }

            if (jenis_izin == "PL" || jenis_izin == "KL" || jenis_izin == "TL") {
                $("#jml_hari_frm").hide();
                $("#dari").val("{{ date('Y-m-d') }}");
                $("#sampai").val("{{ date('Y-m-d') }}");
                $("#dari").prop('disabled', true);
                $("#sampai").prop('disabled', true);
            } else {
                $("#jml_hari_frm").show();
                $("#dari").val("");
                $("#sampai").val("");
                $("#dari").prop('disabled', false);
                $("#sampai").prop('disabled', false);
            }
        });



        function loadjumlahhari() {
            var dari = $("#dari").val();
            var sampai = $("#sampai").val();
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



        // $("#dari").change(function(e) {
        //     loadjumlahhari();
        // });

        $("#sampai").change(function(e) {
            // var sampai = $(this).val();
            // var jenis_cuti = $("#jenis_cuti").val();
            // if (jenis_cuti == "C02") {
            //     Swal.fire({
            //         title: 'Oops !'
            //         , text: 'Tidak Dapat Merubah Tanggal Akhir Untuk Cuti Melahirkan'
            //         , icon: 'warning'
            //     });

            //     $("#sampai").val(sampai);
            // }
            loadjumlahhari();
        });


        function gettanggal() {
            var tanggal = $("#dari").val();
            var someDate = new Date(tanggal);
            var numberOfDaysToAdd = 89;
            var result = someDate.setDate(someDate.getDate() + numberOfDaysToAdd);
            var str = (new Date(result)).toLocaleDateString('en-CA');
            $("#sampai").val(str);
            console.log(str)
        }

        $("#dari").change(function(e) {
            var jenis_cuti = $("#jenis_cuti").val();
            if (jenis_cuti == "C02") {
                gettanggal();
            }
            loadjumlahhari();
        });

        $("#jenis_cuti").change(function() {
            var jenis_cuti = $("#jenis_cuti").val();
            if (jenis_cuti == "C02") {
                gettanggal();
                loadjumlahhari();
            } else {
                $("#sampai").val("");
            }
        });


        $("#frmPengajuanizin").submit(function() {
            var nik = $("#nik").val();
            var dari = $("#dari").val();
            var sampai = $("#sampai").val();
            var status = $("#status").val();
            var keterangan = $("#keterangan").val();
            var jenis_izin = $("#jenis_izin").val();
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
            } else if (status == "") {
                swal({
                    title: 'Oops'
                    , text: 'Status Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#status").focus();
                });
                return false;
            } else if (dari == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#dari").focus();
                });
                return false;
            } else if (sampai == "") {
                swal({
                    title: 'Oops'
                    , text: 'Sampai Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#sampai").focus();
                });
                return false;
            } else if (status == "i" && jenis_izin == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jenis Izin Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jenis_izin").focus();
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
