<form action="/lpc/store" id="frmLpc" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group">

                <select name="kode_cabang" id="kode_cabang" class="form-control">
                    <option value="">Cabang</option>
                    @foreach ($cabang as $d)
                    <option value="{{$d->kode_cabang}}">{{$d->nama_cabang}}</option>
                    @endforeach
                </select>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">

                <select name="bulan" id="bulaninput" class="form-control">
                    <option value="">Bulan</option>
                    <?php
                            $bl = date("m");
                            for ($i = 1; $i < count($bln); $i++) {
                            ?>
                    <option <?php if ($bl == $i) {
                                        echo "selected";
                                    } ?> value="<?php echo $i; ?>"><?php echo $bln[$i]; ?></option>
                    <?php
                            }
                            ?>
                </select>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="tahun" id="tahuninput" class="form-control">
                    <option value="">Tahun</option>
                    <?php
                            $tahun = date("Y");
                            $tahunmulai = 2021;
                            for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                            ?>
                    <option <?php if ($tahun == $thn) {
                                        echo "selected";
                                    } ?> value="<?php echo $thn; ?>"><?php echo $thn; ?>
                    </option>
                    <?php
                            }
                            ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal LPC" field="tgl_lpc" datepicker icon="feather icon-calendar" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="00:00" field="jam_lpc" icon="feather icon-clock" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="file" name="foto" class="form-control">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit" id="simpanlpc"><i class="feather icon-send"></i> Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {

        $('#jam_lpc').mask('00:00', {
            'translation': {
                A: {
                    pattern: /[0-9]/
                }
            }
        });

        function loadlpc() {
            var tahun = $("#tahun").val();
            var bulan = $("#bulan").val();
            $.ajax({
                type: 'POST'
                , url: '/lpc/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , bulan: bulan
                    , tahun: tahun
                }
                , cache: false
                , success: function(respond) {
                    $("#loadlpc").html(respond);
                }
            });
        }

        $("#frmLpc").submit(function(e) {
            var kode_cabang = $("#kode_cabang").val();
            var bulan = $("#bulaninput").val();
            var tahun = $("#tahuninput").val();
            var tgl_lpc = $("#tgl_lpc").val();
            var jam_lpc = $("#jam_lpc").val();
            if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kode Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });
                return false;
            } else if (bulan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bulan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#bulaninput").focus();
                });
                return false;
            } else if (tahun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tahun Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tahuninput").focus();
                });
                return false;
            } else if (tgl_lpc == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_lpc").focus();
                });
                return false;
            } else if (jam_lpc == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jam Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jam_lpc").focus();
                });
                return false;
            }
        });
        // $("#simpanlpc").click(function(e) {
        //     e.preventDefault();
        //     var kode_cabang = $("#kode_cabang").val();
        //     var bulan = $("#bulaninput").val();
        //     var tahun = $("#tahuninput").val();
        //     var tgl_lpc = $("#tgl_lpc").val();
        //     var jam_lpc = $("#jam_lpc").val();
        //     if (kode_cabang == "") {
        //         swal({
        //             title: 'Oops'
        //             , text: 'Kode Cabang Harus Diisi !'
        //             , icon: 'warning'
        //             , showConfirmButton: false
        //         }).then(function() {
        //             $("#kode_cabang").focus();
        //         });
        //     } else if (bulan == "") {
        //         swal({
        //             title: 'Oops'
        //             , text: 'Bulan Harus Diisi !'
        //             , icon: 'warning'
        //             , showConfirmButton: false
        //         }).then(function() {
        //             $("#bulaninput").focus();
        //         });
        //     } else if (tahun == "") {
        //         swal({
        //             title: 'Oops'
        //             , text: 'Tahun Harus Diisi !'
        //             , icon: 'warning'
        //             , showConfirmButton: false
        //         }).then(function() {
        //             $("#tahuninput").focus();
        //         });
        //     } else if (tgl_lpc == "") {
        //         swal({
        //             title: 'Oops'
        //             , text: 'Tanggal Harus Diisi !'
        //             , icon: 'warning'
        //             , showConfirmButton: false
        //         }).then(function() {
        //             $("#tgl_lpc").focus();
        //         });
        //     } else if (jam_lpc == "") {
        //         swal({
        //             title: 'Oops'
        //             , text: 'Jam Harus Diisi !'
        //             , icon: 'warning'
        //             , showConfirmButton: false
        //         }).then(function() {
        //             $("#jam_lpc").focus();
        //         });
        //     } else {
        //         $.ajax({
        //             type: 'POST'
        //             , url: '/lpc/store'
        //             , data: {
        //                 _token: "{{ csrf_token() }}"
        //                 , kode_cabang: kode_cabang
        //                 , bulan: bulan
        //                 , tahun: tahun
        //                 , tgl_lpc: tgl_lpc
        //                 , jam_lpc: jam_lpc
        //             }
        //             , cache: false
        //             , success: function(respond) {
        //                 if (respond == 1) {
        //                     swal("Oops", "Data Sudah Ada", "warning");
        //                 } else if (respond == 0) {
        //                     swal("Berhasil ", "Data Berhasil Disimpan", "success");
        //                 } else {
        //                     swal("Gagal", "Data Gagal Disimpan", "danger");
        //                 }

        //                 loadlpc();
        //                 $("#mdlinputlpc").modal("hide");
        //             }
        //         });
        //     }
        // });


    });

</script>
