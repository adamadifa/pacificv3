<style>
    .form-group {
        margin-bottom: 5px !important;
    }

</style>

@php
$isipcsdus = $barang->isipcsdus;
$isipack = $barang->isipack;
$isipcs = $barang->isipcs;
$jumlah = $barang->jumlah;
$jumlah_dus = floor($jumlah / $isipcsdus);
if ($jumlah != 0) {
$sisadus = $jumlah % $isipcsdus;
} else {
$sisadus = 0;
}
if ($isipack == 0) {
$jumlah_pack = 0;
$sisapack = $sisadus;
} else {
$jumlah_pack = floor($sisadus / $isipcs);
$sisapack = $sisadus % $isipcs;
}

$jumlah_pcs = $sisapack;
@endphp
<form action="#" id="frmeditbarangtemp">
    <input type="hidden" id="harga_dus_old" value="{{ rupiah($barang->promo == 1 ? $barang->harga_dus_old : $barang->harga_dus) }}">
    <input type="hidden" id="harga_pack_old" value="{{ rupiah($barang->promo == 1 ? $barang->harga_pack_old :  $barang->harga_pack) }}">
    <input type="hidden" id="harga_pcs_old" value="{{ rupiah( $barang->promo == 1 ? $barang->harga_pcs_old : $barang->harga_pcs) }}">
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Kode Barang" field="kode_barang" value="{{ $barang->kode_barang }}" icon="feather icon-credit-card" readonly />
            <input type="hidden" id="isipcsdus" value="{{ $isipcsdus }}">
            <input type="hidden" id="isipcs" value="{{ $isipcs }}">
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Nama Barang" field="nama_barang" value="{{ $barang->nama_barang }}" icon="feather icon-file" readonly />
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <x-inputtext label="Dus" field="jml_dus" value="{{ rupiah($jumlah_dus) }}" icon="feather icon-file" right />
        </div>
        <div class="col-8">
            <x-inputtext label="Dus" field="harga_dus" value="{{ rupiah($barang->harga_dus) }}" icon="feather icon-tag" right />
        </div>
    </div>
    @if ($barang->harga_pack != 0)
    <div class="row">
        <div class="col-4">
            <x-inputtext label="Pack" field="jml_pack" value="{{ rupiah($jumlah_pack) }}" icon="feather icon-file" right />
        </div>
        <div class="col-8">
            <x-inputtext label="Harga / Pack" field="harga_pack" value="{{ rupiah($barang->harga_pack) }}" icon="feather icon-tag" right />
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-4">
            <x-inputtext label="Pack" field="jml_pack" value="{{ rupiah($jumlah_pack) }}" icon="feather icon-file" right readonly />
        </div>
        <div class="col-8">
            <x-inputtext label="Harga / Pack" field="harga_pack" value="{{ rupiah($barang->harga_pack) }}" icon="feather icon-tag" right readonly />
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-4">
            <x-inputtext label="Pcs" field="jml_pcs" value="{{ rupiah($jumlah_pcs) }}" icon="feather icon-file" right />
        </div>
        <div class="col-8">
            <x-inputtext label="Harga / Pcs" field="harga_pcs" value="{{ rupiah($barang->harga_pcs) }}" icon="feather icon-tag" right />
        </div>
    </div>
    <div class="row mb-1">
        <div class="col-12">
            <div class="vs-checkbox-con vs-checkbox-primary">
                <input type="checkbox" class="promo" {{ $barang->promo == 1 ? 'checked' : '' }} id="promo" name="promo" value="1">
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
            <button class="btn btn-primary btn-block" id="updatebarangtemp"><i class="feather icon-send mr-1"></i> Simpan</button>
        </div>
    </div>

</form>
<script>
    $(function() {
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
        $("#updatebarangtemp").click(function(e) {
            e.preventDefault();
            var no_fak_penj = $("#no_fak_penj").val();
            var kode_barang = $("#frmeditbarangtemp").find("#kode_barang").val();
            var jml_dus = $("#frmeditbarangtemp").find("#jml_dus").val();
            var jml_pack = $("#frmeditbarangtemp").find("#jml_pack").val();
            var jml_pcs = $("#frmeditbarangtemp").find("#jml_pcs").val();
            var harga_dus = $("#frmeditbarangtemp").find("#harga_dus").val();
            var harga_pack = $("#frmeditbarangtemp").find("#harga_pack").val();
            var harga_pcs = $("#frmeditbarangtemp").find("#harga_pcs").val();
            var isipcsdus = $("#frmeditbarangtemp").find("#isipcsdus").val();
            var isipcs = $("#frmeditbarangtemp").find("#isipcs").val();

            if ($("#frmeditbarangtemp").find("#promo").is(":checked")) {
                var promo = $("#frmeditbarangtemp").find("#promo").val();
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

            if (jumlah == "") {
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
                    , url: '/penjualan/updatebarang'
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
                                , text: 'Item Berhasil Diupdate !'
                                , icon: 'success'
                                , showConfirmButton: false
                            }).then(function() {
                                $("#kode_barang").val("");
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
                                $("#mdleditbarang").modal("hide");
                                showtemp();
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

        $("#frmeditbarangtemp").find("#promo").change(function() {
            var kode_barang = $("#frmeditbarangtemp").find("#kode_barang").val();
            var harga_dus = $("#frmeditbarangtemp").find("#harga_dus_old").val();
            var harga_pack = $("#frmeditbarangtemp").find("#harga_pack_old").val();
            var harga_pcs = $("#frmeditbarangtemp").find("#harga_pcs_old").val();
            if ($("#frmeditbarangtemp").find("#promo").is(":checked")) {
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
                    $("#frmeditbarangtemp").find("#harga_dus").val(0);
                    $("#frmeditbarangtemp").find("#harga_pack").val(0);
                    $("#frmeditbarangtemp").find("#harga_pcs").val(0);

                    $("#frmeditbarangtemp").find("#harga_dus").prop('readonly', true);
                    $("#frmeditbarangtemp").find("#harga_pack").prop('readonly', true);
                    $("#frmeditbarangtemp").find("#harga_pcs").prop('readonly', true);
                }
            } else {
                $("#frmeditbarangtemp").find("#harga_dus").val(harga_dus);
                $("#frmeditbarangtemp").find("#harga_pack").val(harga_pack);
                $("#frmeditbarangtemp").find("#harga_pcs").val(harga_pcs);

                $("#frmeditbarangtemp").find("#harga_dus").prop('readonly', false);
                $("#frmeditbarangtemp").find("#harga_pack").prop('readonly', false);
                $("#frmeditbarangtemp").find("#harga_pcs").prop('readonly', false);
            }
        });

        $("#frmeditbarangtemp").find("#harga_dus, #harga_pack, #harga_pcs, #jml_dus, #jml_pack, #jml_pcs").maskMoney();

        //Hitung Diskon

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
    });

</script>
