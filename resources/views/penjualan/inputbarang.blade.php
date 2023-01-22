<style>
    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }

</style>
<form action="#" id="frmeditbarangtemp">
    <input type="hidden" id="harga_dus_old">
    <input type="hidden" id="harga_pack_old">
    <input type="hidden" id="harga_pcs_old">
    <input type="hidden" name="isipcsdus" id="isipcsdus">
    <input type="hidden" name="isipcs" id="isipcs">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_barang" id="kode_barang_pilih" class="form-control">
                    <option value="">Pilih Barang</option>
                    @foreach ($barang as $d)
                    <option value="{{ $d->kode_barang }}" kode_barang="{{ $d->kode_barang }}" nama_barang="{{ $d->nama_barang }}" isipcsdus="{{ $d->isipcsdus }}" isipcs="{{ $d->isipcs }}" harga_dus="{{ rupiah($d->harga_dus) }}" harga_pack="{{ rupiah($d->harga_pack) }}" harga_pcs="{{ rupiah($d->harga_pcs) }}">{{ $d->nama_barang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <x-inputtext label="Dus" field="jml_dus" icon="feather icon-file" right />
        </div>
        <div class="col-8">
            <x-inputtext label="Dus" field="harga_dus" icon="feather icon-tag" right readonly />
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <x-inputtext label="Pack" field="jml_pack" icon="feather icon-file" right />
        </div>
        <div class="col-8">
            <x-inputtext label="Harga / Pack" field="harga_pack" icon="feather icon-tag" right readonly />
        </div>
    </div>


    <div class="row">
        <div class="col-4">
            <x-inputtext label="Pcs" field="jml_pcs" icon="feather icon-file" right />
        </div>
        <div class="col-8">
            <x-inputtext label="Harga / Pcs" field="harga_pcs" icon="feather icon-tag" right readonly />
        </div>
    </div>
    <div class="row mb-2 mt-1">
        <div class="col-12">
            <div class="vs-checkbox-con vs-checkbox-primary">
                <input type="checkbox" class="promo" id="promo" name="promo" value="1">
                <span class="vs-checkbox">
                    <span class="vs-checkbox--check">
                        <i class="vs-icon feather icon-check"></i>
                    </span>
                </span>
                <span class="">Promosi</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <a class="btn btn-primary btn-block text-white" id="tambahitem"><i class="feather icon-plus text-white mr-1"></i> Tambahkan</a>
        </div>
    </div>

</form>
<script>
    $(function() {
        $("#frmeditbarangtemp").find("#harga_dus, #harga_pack, #harga_pcs, #jml_dus, #jml_pack, #jml_pcs").maskMoney();

        function showtemp() {
            var no_fak_penj = $("#no_fak_penj").val();
            $.ajax({
                type: 'POST'
                , url: '/penjualan/showbarangv2'
                , cache: false
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_fak_penj: no_fak_penj
                }
                , success: function(respond) {
                    $("#loadbarangtemp").html(respond);
                    hitungdiskon();
                }
            });
        }


        function hitungdiskon() {
            var no_fak_penj = $("#no_fak_penj").val();
            var jenistransaksi = $("#jenistransaksi").val();
            var pelanggan = $("#nama_pelanggan").val();
            var pl = pelanggan.split("|");
            var nama_pelanggan = pl[1] != undefined ? pl[1] : '';
            var kode_pelanggan = pl[0] != undefined ? pl[0] : '';
            var kode_cabang = kode_pelanggan.substr(0, 3);
            $("#btnsimpan").prop('disabled', true);
            $("#btnsimpan").html('<i class="fa fa-spinner mr-1"></i><i>Loading...</i>');
            $.ajax({
                type: 'POST'
                , url: '/hitungdiskonpenjualanv2'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_fak_penj: no_fak_penj
                    , jenistransaksi: jenistransaksi
                }
                , cache: false
                , success: function(respond) {

                    $("#btnsimpan").prop('disabled', false);
                    $("#btnsimpan").html('<i class="feather icon-send mr-1"></i> Simpan');
                    var result = respond.split("|");
                    console.log(result);
                    if (nama_pelanggan.includes("KPBN") && kode_cabang == "TSM") {
                        $("#potswan").val(0);
                        $("#potaida").val(0);
                        $("#potstick").val(0);
                        $("#potsp").val(0);
                        $("#potsb").val(0);
                    } else {
                        $("#potswan").val(result[0]);
                        $("#potaida").val(result[1]);
                        $("#potstick").val(result[2]);
                        $("#potsp").val(result[3]);
                        $("#potsb").val(result[4]);
                    }
                    loadtotal();
                }
            });
        }




        //Hitung Total
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

        function loadtotal() {
            var subtotal = $("#totaltemp").val();
            var potswan = $("#potswan").val();
            var potaida = $("#potaida").val();
            var potstick = $("#potstick").val();
            var potsp = $("#potsp").val();
            var potsb = $("#potsb").val();
            var potisaida = $("#potisaida").val();
            var potisswan = $("#potisswan").val();
            var potisstick = $("#potisstick").val();
            var penyaida = $("#penyaida").val();
            var penyswan = $("#penyswan").val();
            var penystick = $("#penystick").val();
            var voucher = $("#voucher").val();
            var cekpajak = $("#cekpajak").val();

            if (potswan.length === 0) {
                var potswan = 0;
            } else {
                var potswan = parseInt(potswan.replace(/\./g, ''));
            }

            if (potaida.length === 0) {
                var potaida = 0;
            } else {
                var potaida = parseInt(potaida.replace(/\./g, ''));
            }

            if (potstick.length === 0) {
                var potstick = 0;
            } else {
                var potstick = parseInt(potstick.replace(/\./g, ''));
            }

            if (potsp.length === 0) {
                var potsp = 0;
            } else {
                var potsp = parseInt(potsp.replace(/\./g, ''));
            }

            if (potsb.length === 0) {
                var potsb = 0;
            } else {
                var potsb = parseInt(potsb.replace(/\./g, ''));
            }

            if (potisaida.length === 0) {
                var potisaida = 0;
            } else {
                var potisaida = parseInt(potisaida.replace(/\./g, ''));
            }

            if (potisswan.length === 0) {
                var potisswan = 0;
            } else {
                var potisswan = parseInt(potisswan.replace(/\./g, ''));
            }

            if (potisstick.length === 0) {
                var potisstick = 0;
            } else {
                var potisstick = parseInt(potisstick.replace(/\./g, ''));
            }

            if (penyaida.length === 0) {
                var penyaida = 0;
            } else {
                var penyaida = parseInt(penyaida.replace(/\./g, ''));
            }

            if (penyswan.length === 0) {
                var penyswan = 0;
            } else {
                var penyswan = parseInt(penyswan.replace(/\./g, ''));
            }

            if (penystick.length === 0) {
                var penystick = 0;
            } else {
                var penystick = parseInt(penystick.replace(/\./g, ''));
            }

            if (voucher.length === 0) {
                var voucher = 0;
            } else {
                var voucher = parseInt(voucher.replace(/\./g, ''));
            }

            var potongan = potswan + potaida + potstick + potsp + potsb;
            var potonganistimewa = potisaida + potisswan + potisstick;
            var penyesuaian = penyaida + penyswan + penystick;
            var total = subtotal - potongan - potonganistimewa - penyesuaian;
            var grandtotal = total - voucher;
            if (cekpajak == 1) {
                var ppn = parseInt(total) * (11 / 100);
            } else {
                var ppn = 0;
            }
            var totalwithppn = parseInt(grandtotal) + parseInt(ppn);
            var bruto = total;
            $("#grandtotal").text(convertToRupiah(totalwithppn));
            $("#totalnonppn").val(convertToRupiah(total));
            $("#ppn").val(convertToRupiah(ppn));
            $("#total").val(convertToRupiah(totalwithppn));
            $("#bruto").val(subtotal);
            $("#subtotal").val(totalwithppn);
        }

        $("#tambahitem").click(function(e) {
            e.preventDefault();
            $("#tambahitem").prop("disabled", true);
            var no_fak_penj = $("#no_fak_penj").val();
            var kode_barang = $("#kode_barang_pilih").val();
            var jml_dus = $("#jml_dus").val();
            var jml_pack = $("#jml_pack").val();
            var jml_pcs = $("#jml_pcs").val();
            var harga_dus = $("#harga_dus").val();
            var harga_pack = $("#harga_pack").val();
            var harga_pcs = $("#harga_pcs").val();
            var isipcsdus = $("#isipcsdus").val();
            var isipcs = $("#isipcs").val();
            if ($('#promo').is(":checked")) {
                var promo = $("#promo").val();
            } else {
                var promo = "";
            }


            var jmldus = jml_dus != "" ? parseInt(jml_dus.replace(/\./g, '')) : 0;
            var jmlpack = jml_pack != "" ? parseInt(jml_pack.replace(/\./g, '')) : 0;
            var jmlpcs = jml_pcs != "" ? parseInt(jml_pcs.replace(/\./g, '')) : 0;

            var hargadus = harga_dus != "" ? parseInt(harga_dus.replace(/\./g, '')) : 0;
            var hargapack = harga_pack != "" ? parseInt(harga_pack.replace(/\./g, '')) : 0;
            var hargapcs = harga_pcs != "" ? parseInt(harga_pcs.replace(/\./g, '')) : 0;


            var jumlah = (jmldus * parseInt(isipcsdus)) + (jmlpack * (parseInt(isipcs))) + jmlpcs;
            var subtotal = (jmldus * hargadus) + (jmlpack * hargapack) + (jmlpcs * hargapcs);
            //alert(totalpcs);

            if (kode_barang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Barang Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_barang").focus();
                });
                return false;
            } else if (jumlah == "") {
                swal({
                    title: 'Oops'
                    , text: 'Qty Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jml_dus").focus();
                });
                return false;
            } else {
                //Simpan Barang Temp
                $.ajax({
                    type: 'POST'
                    , url: '/penjualan/storebarang'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , no_fak_penj: no_fak_penj
                        , kode_barang: kode_barang
                        , hargadus: hargadus
                        , hargapack: hargapack
                        , hargapcs: hargapcs
                        , jumlah: jumlah
                        , subtotal: subtotal
                        , promo: promo
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 0) {
                            swal({
                                title: 'Success'
                                , text: 'Item Berhasil Disimpan !'
                                , icon: 'success'
                                , showConfirmButton: false
                            }).then(function() {
                                $('#mdlinputbarang').modal({
                                    backdrop: 'static'
                                    , keyboard: false
                                });
                                showtemp();
                                $("#kode_barang_pilih").val("");
                                $("#nama_barang").val("");
                                $("#jml_dus").val("");
                                $("#jml_pack").val("");
                                $("#jml_pcs").val("");

                                $("#harga_dus").val("");
                                $("#harga_pack").val("");
                                $("#harga_pcs").val("");

                                $("#harga_dus_old").val("");
                                $("#harga_pack_old").val("");
                                $("#harga_pcs_old").val("");

                                //$("#jml_dus").focus();

                            });


                        } else if (respond == 1) {
                            swal({
                                title: 'Oops'
                                , text: 'Item Sudah Ada !'
                                , icon: 'warning'
                                , showConfirmButton: false
                            }).then(function() {
                                $("#kode_barang_pilih").val("");
                                $("#nama_barang").val("");
                                $("#jml_dus").val("");
                                $("#jml_pack").val("");
                                $("#jml_pcs").val("");

                                $("#harga_dus").val("");
                                $("#harga_pack").val("");
                                $("#harga_pcs").val("");

                                $("#harga_dus_old").val("");
                                $("#harga_pack_old").val("");
                                $("#harga_pcs_old").val("");

                                $("#nama_barang").focus();

                            });
                        } else {
                            swal({
                                title: 'Oops'
                                , text: respond
                                , icon: 'warning'
                                , showConfirmButton: false
                            }).then(function() {

                                $("#jml_dus").focus();

                            });
                        }
                    }
                });
            }
        });

        $("#kode_barang_pilih").change(function(e) {
            e.preventDefault();
            var kode_barang = $('option:selected', this).attr("kode_barang");
            var nama_barang = $('option:selected', this).attr("nama_barang");
            var harga_dus = $('option:selected', this).attr("harga_dus");
            var harga_pack = $('option:selected', this).attr("harga_pack");
            var harga_pcs = $('option:selected', this).attr("harga_pcs");
            var isipcsdus = $('option:selected', this).attr("isipcsdus");
            var isipcs = $('option:selected', this).attr("isipcs");
            var nama_pelanggan = $("#nama_pelanggan").val();
            if ($('#promo').is(":checked")) {
                var harga_dus = 0;
                var harga_pack = 0;
                var harga_pcs = 0;
            }
            if (nama_pelanggan.includes('KPBN')) {
                $("#harga_dus").prop('readonly', false);
                $("#harga_pack").prop('readonly', false);
                $("#harga_pcs").prop('readonly', false);
            } else {
                $("#harga_dus").prop('readonly', true);
                $("#harga_pack").prop('readonly', true);
                $("#harga_pcs").prop('readonly', true);
            }
            $("#harga_dus").val(harga_dus);
            $("#harga_pack").val(harga_pack);
            $("#harga_pcs").val(harga_pcs);

            $("#harga_dus_old").val(harga_dus);
            $("#harga_pack_old").val(harga_pack);
            $("#harga_pcs_old").val(harga_pcs);

            $("#isipcsdus").val(isipcsdus);
            $("#isipcs").val(isipcs);
            if (harga_pack == 0) {
                $("#harga_pack").prop("readonly", true);
                $("#jml_pack").prop("readonly", true);
            } else {
                if (nama_pelanggan.includes('KPBN')) {
                    $("#harga_pack").prop("readonly", false);
                    $("#jml_pack").prop("readonly", false);
                } else {
                    $("#harga_pack").prop("readonly", true);
                    $("#jml_pack").prop("readonly", false);
                }

            }
        });

        $("#promo").change(function() {
            var kode_barang = $("#kode_barang_pilih").val();
            var harga_dus = $("#harga_dus_old").val();
            var harga_pack = $("#harga_pack_old").val();
            var harga_pcs = $("#harga_pcs_old").val();
            if ($('#promo').is(":checked")) {
                if (kode_barang == "") {
                    swal({
                        title: 'Oops'
                        , text: 'Barang Harus Dipilih !'
                        , icon: 'warning'
                        , showConfirmButton: false
                    }).then(function() {
                        $("#nama_barang").focus();
                    });
                    $('#promo').prop('checked', false); // Unchecks it
                } else {
                    $("#harga_dus").val(0);
                    $("#harga_pack").val(0);
                    $("#harga_pcs").val(0);

                    $("#harga_dus").prop('readonly', true);
                    $("#harga_pack").prop('readonly', true);
                    $("#harga_pcs").prop('readonly', true);
                }
            } else {
                $("#harga_dus").val(0);
                $("#harga_pack").val(0);
                $("#harga_pcs").val(0);
                $("#jml_dus").val("");
                $("#jml_pack").val("");
                $("#jml_pcs").val("");
                $("#kode_barang_pilih").val("");
                $("#nama_barang").val("");
                $("#kode_barang").val("");
                $("#harga_dus").prop('readonly', true);
                $("#harga_pack").prop('readonly', true);
                $("#harga_pcs").prop('readonly', true);
            }
        });
    });

</script>
