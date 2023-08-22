<div class="row">
    <div class="col-12">
        <div class="form-group">
            <select name="jenis_bayar" id="jenis_bayar" class="form-control">
                <option value="">Jenis Pembayaran</option>
                <option value="1">Potong Gaji</option>
                <option value="2">Potong Komisi</option>
                <option value="3">Titipan Pelanggan</option>
            </select>
        </div>
    </div>
</div>
<div class="row" id="pilihbulan">
    <div class="col-12">
        {{-- <label for="" class="form-label mb-1">Omset Bulan</label> --}}
        <div class="form-group">
            <select class="form-control" id="bulan" name="bulan">
                <option value="">Bulan</option>
                <?php
                $bulanini = date("m");
                for ($i = 1; $i < count($bulan); $i++) {
                ?>
                <option value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?></option>
                <?php
                }
                ?>
            </select>
        </div>
    </div>
</div>
<div class="row" id="pilihtahun">
    <div class="col-12">
        <div class="form-group">
            <select class="form-control" id="tahun" name="tahun">
                <option value="">Tahun</option>
                <?php
                $tahunmulai = 2020;
                for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                ?>
                <option value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
                <?php
                }
                ?>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <x-inputtext label="Jumlah Bayar" field="jumlah" icon="feather icon-file" right />
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <a href="#" class="btn btn-primary w-100" id="bayar"><i class="feather icon-send mr-1"></i> Bayar</a>
        </div>
    </div>
</div>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {
        $("#jumlah").maskMoney();

        function convertToRupiah(number) {
            if (number) {
                var rupiah = "";
                var numberrev = number
                    .toString()
                    .split("")
                    .reverse()
                    .join("");
                for (var i = 0; i < numberrev.length; i++)
                    if (i % 3 == 0) rupiah += numberrev.substr(i, 3) + ".";
                return (
                    rupiah
                    .split("", rupiah.length - 1)
                    .reverse()
                    .join("")
                );
            } else {
                return number;
            }
        }



        function loadhistoribayar() {
            var no_pinjaman_nonpjp = "{{ $no_pinjaman_nonpjp }}";
            $.ajax({
                type: 'POST'
                , url: '/piutangkaryawan/gethistoribayar'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_pinjaman_nonpjp: no_pinjaman_nonpjp
                }
                , cache: false
                , success: function(respond) {
                    $("#loadhistoribayar").html(respond);
                    loadsisatagihan();
                }
            });
        }

        function loadsisatagihan() {
            var jml_pinjaman = $("#jmlpinjaman").text();
            var totalbayar = $("#totalbayar").text();

            var jp = parseInt(jml_pinjaman.replace(/\./g, ''));
            var tb = parseInt(totalbayar.replace(/\./g, ''));

            var sisa = jp - tb;
            $("#jmlbayar").text(convertToRupiah(tb));
            $("#sisatagihan").text(convertToRupiah(sisa));

        }


        $(".tutupmdlinputbayarpinjaman").click(function(e) {
            $("#mdlinputbayarpinjaman").modal("hide");
        });


        $("#bayar").click(function(e) {
            e.preventDefault();
            var jenis_bayar = $("#jenis_bayar").val();
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            var jumlah = $("#jumlah").val();
            var jmlbayar = $("#jmlbayar").text();
            var jml_pinjaman = $("#jmlpinjaman").text();
            var jp = parseInt(jml_pinjaman.replace(/\./g, ''));
            var jb = parseInt(jmlbayar.replace(/\./g, ''));
            var sisa = jp - jb;
            var jm = parseInt(jumlah.replace(/\./g, ''));


            if (jenis_bayar == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jenis Pembayaran Tidak Boleh Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jenis_bayar").focus();
                });
            } else if (bulan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bulan Pembayaran Tidak Boleh Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#bulan").focus();
                });
            } else if (tahun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tahun Pembayaran Tidak Boleh Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tahun").focus();
                });
            } else if (jumlah == "" || jumlah == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Bayar Tidak Boleh Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah").focus();
                });
            } else if (jm > sisa) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Bayar Tidak Boleh Lebih Dari Sisa Tagihan !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah").focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/pembayaranpiutangkaryawan/store'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , no_pinjaman_nonpjp: "{{ $no_pinjaman_nonpjp }}"
                        , jenis_bayar: jenis_bayar
                        , bulan: bulan
                        , tahun: tahun
                        , jumlah: jumlah
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 0) {
                            swal({
                                title: 'Success'
                                , text: 'Data Berhasil Disimpan !'
                                , icon: 'success'
                                , showConfirmButton: false
                            }).then(function() {
                                loadhistoribayar();
                                $("#mdlinputbayarpinjaman").modal("hide");
                            });
                        }
                    }
                });
            }
        });

    });

</script>
