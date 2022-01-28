@php
$total = 0;
@endphp
@foreach ($barang as $d)
@php
$jmldus = floor($d->jumlah / $d->isipcsdus);
$sisadus = $d->jumlah % $d->isipcsdus;

if ($d->isipack == 0) {
$jmlpack = 0;
$sisapack = $sisadus;
} else {

$jmlpack = floor($sisadus / $d->isipcs);
$sisapack = $sisadus % $d->isipcs;
}

$jmlpcs = $sisapack;
$total += $d->subtotal;
@endphp
<tr>
    <td>
        <div class="vs-checkbox-con vs-checkbox-primary">
            <input type="checkbox" @if($d->cekjmlbarang > 1) disabled @endif name="promo[]" class="promo" id="promo" value="1" {{ ($d->promo==1 ? 'checked':'') }}>
            <span class="vs-checkbox">
                <span class="vs-checkbox--check">
                    <i class="vs-icon feather icon-check"></i>
                </span>
            </span>
        </div>
    </td>
    <td>
        {{ $d->kode_barang }}
        <input type="hidden" class="kode_barang" id="kode_barang" name="kode_barang[]" value="{{ $d->kode_barang }}">
    </td>
    <td>
        {{ $d->nama_barang }}
        <input type="hidden" class="isipack" id="isipack" name="isipack[]" value="{{ $d->isipack }}">
        <input type="hidden" class="isipcsdus" id="isipcsdus" name="isipcsdus[]" value="{{ $d->isipcsdus }}">
        <input type="hidden" class="isipcs" id="isipcs" name="isipcs[]" value="{{ $d->isipcs }}">
    </td>
    <td class="text-center">{{ $d->isipcsdus }}</td>
    <td class="text-center">{{ $d->isipack }}</td>
    <td class="text-center">{{ $d->isipcs }}</td>
    <td></td>
    <td>
        <input type="text" class="form-control text-center jmldus" id="jmldus" name="jmldus[]" value="{{ $jmldus }}">
    </td>
    <td>
        <input type="hidden" class="form-control text-right harga_dus_old" id="harga_dus_old" name="harga_dus_old[]" value="{{ rupiah($d->harga_dus) }}">
        <input type="text" class="form-control text-right harga_dus money" id="harga_dus" name="harga_dus[]" value="{{ rupiah($d->harga_dus) }}">
    </td>
    <td>

        <input type="{{ (!empty($d->harga_pack) ? 'text' :'hidden') }}" class="form-control text-center jmlpack" id="jmlpack" name="jmlpack[]" value="{{ $jmlpack }}">

    </td>
    <td>

        <input type="{{ (!empty($d->harga_pack) ? 'hidden' :'hidden') }}" class="form-control text-right harga_pack_old" id="harga_pack_old" name="harga_pack_old[]" value="{{ rupiah($d->harga_pack) }}">
        <input type="{{ (!empty($d->harga_pack) ? 'text' :'hidden') }}" class="form-control text-right harga_pack money" id="harga_pack" name="harga_pack[]" value="{{ rupiah($d->harga_pack) }}">

    </td>
    <td>

        <input type="text" class="form-control text-center jmlpcs" id="jmlpcs" name="jmlpcs[]" value="{{ $jmlpcs }}">
    </td>
    <td>
        <input type="hidden" class="form-control text-right harga_pcs_old " id="harga_pcs_old" name="harga_pcs_old[]" value="{{ rupiah($d->harga_pcs) }}">
        <input type="text" class="form-control text-right harga_pcs money " id="harga_pcs" name="harga_pcs[]" value="{{ rupiah($d->harga_pcs) }}">
    </td>
    <td class="text-right total" class="total">{{ rupiah($d->subtotal) }}</td>
    <td>
        <a href="#" kode_barang="{{ $d->kode_barang }}" promo="{{ $d->promo }}" class="hapus"><i class="feather icon-trash danger"></i></a>
    </td>
</tr>
@endforeach
{{-- <tr>
    <th colspan="13" style="font-size:1rem">TOTAL</td>
    <th class="text-right" style="font-size:1rem">{{ rupiah($total) }}</td>
<th></th>
</tr> --}}
<script>
    $(function() {

        function cektemp() {
            var no_fak_penj = $("#no_fak_penj").val();
            $.ajax({
                type: 'POST'
                , url: '/cekpenj'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_fak_penj: no_fak_penj
                }
                , cache: false
                , success: function(respond) {
                    $("#cektemp").val(respond);
                }
            });
        }

        function loadtotal() {
            var no_fak_penj = $("#no_fak_penj").val();
            $.ajax({
                type: 'POST'
                , url: '/loadtotalpenjualan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_fak_penj: no_fak_penj
                }
                , cache: false
                , success: function(respond) {
                    var total = parseInt(respond.replace(/\./g, ''));
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
                    var grandtotal = total - potongan - potonganistimewa - penyesuaian - voucher;
                    var bruto = total;
                    $("#grandtotal").text(convertToRupiah(grandtotal));
                    $("#total").val(convertToRupiah(grandtotal));
                    $("#bruto").val(bruto);
                    $("#subtotal").val(grandtotal);
                    cektemp();
                }
            });
        }

        function hitungdiskon() {
            var jenistransaksi = $("#jenistransaksi").val();
            var no_fak_penj = $("#no_fak_penj").val();
            var potaida = "{{ rupiah($faktur->potaida) }}";
            var potswan = "{{ rupiah($faktur->potswan) }}";
            var potstick = "{{ rupiah($faktur->potstick) }}";
            var potsb = "{{ rupiah($faktur->potsambal) }}";
            var potsp = "{{ rupiah($faktur->potsp) }}";
            $.ajax({
                type: 'POST'
                , url: '/hitungdiskonpenjualan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , jenistransaksi: jenistransaksi
                    , no_fak_penj: no_fak_penj
                }
                , cache: false
                , success: function(respond) {
                    var result = respond.split("|");
                    console.log(result);
                    if (potswan == "" || potswan === 0) {
                        $("#potswan").val(result[0]);
                    } else {
                        $("#potswan").val(potswan);
                    }
                    if (potaida == "" || potaida === 0) {
                        $("#potaida").val(result[1]);
                    } else {
                        $("#potaida").val(potaida);
                    }
                    if (potstick == "" || potstick === 0) {
                        $("#potstick").val(result[2]);
                    } else {
                        $("#potstick").val(potstick);
                    }
                    if (potsp == "" || potsp === 0) {
                        $("#potsp").val(result[3]);
                    } else {
                        $("#potsp").val(potsp);
                    }
                    if (potsb == "" || potsb === 0) {
                        $("#potsb").val(result[4]);
                    } else {
                        $("#potsb").val(potsb);
                    }
                    loadtotal();

                }
            });
        }


        function hitungdiskon2() {
            var jenistransaksi = $("#jenistransaksi").val();
            var no_fak_penj = $("#no_fak_penj").val();
            var potaida = "{{ rupiah($faktur->potaida) }}";
            var potswan = "{{ rupiah($faktur->potswan) }}";
            var potstick = "{{ rupiah($faktur->potstick) }}";
            var potsb = "{{ rupiah($faktur->potsambal) }}";
            var potsp = "{{ rupiah($faktur->potsp) }}";
            $.ajax({
                type: 'POST'
                , url: '/hitungdiskonpenjualan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , jenistransaksi: jenistransaksi
                    , no_fak_penj: no_fak_penj
                }
                , cache: false
                , success: function(respond) {
                    var result = respond.split("|");
                    console.log(result);
                    $("#potswan").val(result[0]);
                    $("#potaida").val(result[1]);
                    $("#potstick").val(result[2]);
                    $("#potsp").val(result[3]);
                    $("#potsb").val(result[4]);
                    loadtotal();
                }
            });
        }

        hitungdiskon();

        $(".tunai").hide();
        $(".kredit").hide();

        function loadtunaikredit() {
            var jenistransaksi = $("#jenistransaksi").val();
            var voucher = $("#voucher_old").val();
            if (jenistransaksi == "tunai") {
                $("#jenisbayar").val("tunai");
                $(".tunai").show();
                $(".kredit").hide();
                $("#voucher").val(convertToRupiah(voucher));
            } else if (jenistransaksi == "kredit") {
                $("#jenisbayar").val("titipan");
                $(".tunai").hide();
                $(".kredit").show();
                $("#titipan").focus();
                $("#voucher").val(0);
            }
            //hitungdiskon2();
        }
        $("#jenistransaksi").change(function() {
            loadtunaikredit();
            hitungdiskon2();
        });

        loadtunaikredit();


        $(".money").maskMoney();
        $("#potswan, #potaida, #potstick, #potsp, #potsb,#potisaida,#potisswan,#potisstick,#penyaida,#penyswan,#penystick,#voucher").on('keyup', function() {
            //alert('test');
            loadtotal();
        });
        // $('.money').mask("#.##0", {
        //     reverse: true
        // });


        function loadbarang() {
            var no_fak_penj = $("#no_fak_penj").val();
            $.ajax({
                type: 'POST'
                , url: '/penjualan/showbarang'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_fak_penj: no_fak_penj
                }
                , cache: false
                , success: function(respond) {
                    $("#loadbarang").html(respond);
                }
            });
        }
        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_barang = $(this).attr("kode_barang");
            var promo = $(this).attr("promo");
            event.preventDefault();
            swal({
                    title: `Anda Yakin Data ini Akan Dihapus ?`
                    , text: "Jika dihapus Data Ini Akan Hilang Dari Keranjang"
                    , icon: "warning"
                    , buttons: true
                    , dangerMode: true
                , })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: 'POST'
                            , url: '/penjualan/deletebarangtemp'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , kode_barang: kode_barang
                                , promo: promo
                            }
                            , cache: false
                            , success: function(respond) {
                                swal(
                                    'Deleted!'
                                    , 'Data Berhasil Dihapus'
                                    , 'success'
                                )
                                loadbarangtemp();
                                hitungdiskon2();
                            }
                        });
                    }
                });
        });

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
        var $tblrows = $("#tabelproduktemp tbody tr");
        $tblrows.each(function(index) {
            var $tblrow = $(this);

            if ($tblrow.find('.promo').is(':checked')) {
                $tblrow.css("background-color", "rgb(255 255 183 / 88%)");
            }

            $tblrow.find('.promo').on('change', function() {
                var no_fak_penj = $("#no_fak_penj").val();
                var kode_barang = $tblrow.find("[id=kode_barang]").val();
                var jmldus = $tblrow.find("[id=jmldus]").val();
                var harga_dus = $tblrow.find("[id=harga_dus]").val();
                var harga_dus_old = $tblrow.find("[id=harga_dus_old]").val();
                var jmlpack = $tblrow.find("[id=jmlpack]").val();
                var harga_pack = $tblrow.find("[id=harga_pack]").val();
                var harga_pack_old = $tblrow.find("[id=harga_pack_old]").val();
                var jmlpcs = $tblrow.find("[id=jmlpcs]").val();
                var harga_pcs = $tblrow.find("[id=harga_pcs]").val();
                var harga_pcs_old = $tblrow.find("[id=harga_pcs_old]").val();
                if ($tblrow.find('.promo').is(':checked')) {
                    $tblrow.css("background-color", "rgb(255 255 183 / 88%)");
                    if (jmldus.length === 0) {
                        var jmldus = 0;
                    } else {
                        var jmldus = parseInt(jmldus);
                    }
                    if (harga_dus.length === 0) {
                        var harga_dus = 0;
                    } else {
                        var harga_dus = parseInt(harga_dus.replace(/\./g, ''));
                    }

                    if (jmlpack.length === 0) {
                        var jmlpack = 0;
                    } else {
                        var jmlpack = parseInt(jmlpack);
                    }

                    if (harga_pack.length === 0) {
                        var harga_pack = 0;
                    } else {
                        var harga_pack = parseInt(harga_pack.replace(/\./g, ''));
                    }

                    if (jmlpcs.length === 0) {
                        var jmlpcs = 0;
                    } else {
                        var jmlpcs = parseInt(jmlpcs);
                    }

                    if (harga_pcs.length === 0) {
                        var harga_pcs = 0;
                    } else {
                        var harga_pcs = parseInt(harga_pcs.replace(/\./g, ''));
                    }

                    $tblrow.find('.harga_dus').val(0);
                    $tblrow.find('.harga_dus').attr("readonly", "true");
                    $tblrow.find('.harga_pack').val(0);
                    $tblrow.find('.harga_pack').attr("readonly", "true");
                    $tblrow.find('.harga_pcs').val(0);
                    $tblrow.find('.harga_pcs').attr("readonly", "true");
                    var total = (jmldus * 0) + (jmlpack * 0) + (jmlpcs * 0);
                    $.ajax({
                        type: 'POST'
                        , url: '/penjualan/updatedetail'
                        , data: {
                            _token: "{{ csrf_token() }}"
                            , no_fak_penj: no_fak_penj
                            , kode_barang: kode_barang
                            , jmldus: jmldus
                            , jmlpack: jmlpack
                            , jmlpcs: jmlpcs
                            , harga_dus: 0
                            , harga_pack: 0
                            , harga_pcs: 0
                            , total: total
                            , promo: 1
                            , check: 'true'
                        }
                        , cache: false
                        , success: function(respond) {
                            console.log(respond);
                            hitungdiskon2();
                        }
                    });
                    if (!isNaN(total)) {
                        $tblrow.find('.total').text(convertToRupiah(total));
                    }
                } else {
                    $tblrow.css("background-color", "");

                    function updateharga() {
                        var no_fak_penj = $("#no_fak_penj").val();
                        var harga_dus = $tblrow.find("[id=harga_dus]").val();
                        var harga_pack = $tblrow.find("[id=harga_pack]").val();
                        var harga_pcs = $tblrow.find("[id=harga_pcs]").val();
                        var jmldus = $tblrow.find("[id=jmldus]").val();
                        var jmlpack = $tblrow.find("[id=jmlpack]").val();
                        var jmlpcs = $tblrow.find("[id=jmlpcs]").val();
                        $tblrow.find('.harga_dus').removeAttr("readonly");
                        $tblrow.find('.harga_pack').removeAttr("readonly");
                        $tblrow.find('.harga_pcs').removeAttr("readonly");

                        if (jmldus.length === 0) {
                            var jmldus = 0;
                        } else {
                            var jmldus = parseInt(jmldus);
                        }
                        if (harga_dus.length === 0) {
                            var harga_dus = 0;
                        } else {
                            var harga_dus = parseInt(harga_dus.replace(/\./g, ''));
                        }

                        if (jmlpack.length === 0) {
                            var jmlpack = 0;
                        } else {
                            var jmlpack = parseInt(jmlpack);
                        }

                        if (harga_pack.length === 0) {
                            var harga_pack = 0;
                        } else {
                            var harga_pack = parseInt(harga_pack.replace(/\./g, ''));
                        }

                        if (jmlpcs.length === 0) {
                            var jmlpcs = 0;
                        } else {
                            var jmlpcs = parseInt(jmlpcs);
                        }

                        if (harga_pcs.length === 0) {
                            var harga_pcs = 0;
                        } else {
                            var harga_pcs = parseInt(harga_pcs.replace(/\./g, ''));
                        }

                        var total = (jmldus * harga_dus) + (jmlpack * harga_pack) + (jmlpcs * harga_pcs);
                        $.ajax({
                            type: 'POST'
                            , url: '/penjualan/updatedetail'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , no_fak_penj: no_fak_penj
                                , kode_barang: kode_barang
                                , jmldus: jmldus
                                , jmlpack: jmlpack
                                , jmlpcs: jmlpcs
                                , harga_dus: harga_dus
                                , harga_pack: harga_pack
                                , harga_pcs: harga_pcs
                                , total: total
                                , promo: 0
                                , check: 'false'
                            }
                            , cache: false
                            , success: function(respond) {
                                console.log(respond);
                            }
                        });
                        if (!isNaN(total)) {
                            $tblrow.find('.total').text(convertToRupiah(total));
                        }
                    }
                    $.ajax({
                        type: 'POST'
                        , url: '/gethargabarang'
                        , data: {
                            _token: "{{ csrf_token() }}"
                            , kode_barang: kode_barang
                        }
                        , cache: false
                        , success: function(respond) {
                            console.log(respond);
                            var data = respond.split("|");
                            $tblrow.find('.harga_dus').val(data[0]);
                            $tblrow.find('.harga_pack').val(data[1]);
                            $tblrow.find('.harga_pcs').val(data[2]);
                            updateharga();
                            hitungdiskon2();
                        }
                    });
                }

            });


            $tblrow.find('.jmldus,.jmlpack,.jmlpcs,.harga_dus,.harga_pack,.harga_pcs').on('input', function() {
                var no_fak_penj = $("#no_fak_penj").val();

                var kode_barang = $tblrow.find("[id=kode_barang]").val();
                var isipack = $tblrow.find("[id=isipack]").val();
                var isipcs = $tblrow.find("[id=isipcs]").val();
                var isipcsdus = $tblrow.find("[id=isipcsdus]").val();
                var jmldus = $tblrow.find("[id=jmldus]").val();
                var harga_dus = $tblrow.find("[id=harga_dus]").val();
                var jmlpack = $tblrow.find("[id=jmlpack]").val();
                var harga_pack = $tblrow.find("[id=harga_pack]").val();
                var jmlpcs = $tblrow.find("[id=jmlpcs]").val();
                var harga_pcs = $tblrow.find("[id=harga_pcs]").val();
                if ($tblrow.find("[id=promo]").is(':checked')) {
                    var promo = 1;
                } else {
                    var promo = 'NULL';
                }
                //alert(promo);
                if (isipack.length === 0) {
                    var isipack = 0;
                } else {
                    var isipack = parseInt(isipack);
                }
                if (isipcs.length === 0) {
                    var isipcs = 0;
                } else {
                    var isipcs = parseInt(isipcs);
                }
                if (isipcsdus.length === 0) {
                    var isipcsdus = 0;
                } else {
                    var isipcsdus = parseInt(isipcsdus);
                }
                if (jmldus.length === 0) {
                    var jmldus = 0;
                } else {
                    var jmldus = parseInt(jmldus);
                }
                if (harga_dus.length === 0) {
                    var harga_dus = 0;
                } else {
                    var harga_dus = parseInt(harga_dus.replace(/\./g, ''));
                }

                if (jmlpack.length === 0) {
                    var jmlpack = 0;
                } else {
                    var jmlpack = parseInt(jmlpack);
                }

                if (harga_pack.length === 0) {
                    var harga_pack = 0;
                } else {
                    var harga_pack = parseInt(harga_pack.replace(/\./g, ''));
                }

                if (jmlpcs.length === 0) {
                    var jmlpcs = 0;
                } else {
                    var jmlpcs = parseInt(jmlpcs);
                }

                if (harga_pcs.length === 0) {
                    var harga_pcs = 0;
                } else {
                    var harga_pcs = parseInt(harga_pcs.replace(/\./g, ''));
                }


                if (harga_pack === 0) {
                    if (jmlpcs >= isipcsdus) {
                        swal("Oops", "Jml Pcs Melebihi Batas Maksimal, Masukan Ke Satuan Dus/Ball", "warning");
                        $tblrow.find('.jmlpcs').val(0);
                        var total = (jmldus * harga_dus) + (jmlpack * harga_pack) + (0 * harga_pcs);
                        $.ajax({
                            type: 'POST'
                            , url: '/penjualan/updatedetail'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , no_fak_penj: no_fak_penj
                                , kode_barang: kode_barang
                                , jmldus: jmldus
                                , jmlpack: jmlpack
                                , jmlpcs: 0
                                , harga_dus: harga_dus
                                , harga_pack: harga_pack
                                , harga_pcs: harga_pcs
                                , total: total
                                , promo: promo
                            }
                            , cache: false
                            , success: function(respond) {
                                console.log(respond);
                                hitungdiskon2();
                            }
                        });
                    } else {
                        var total = (jmldus * harga_dus) + (jmlpack * harga_pack) + (jmlpcs * harga_pcs);
                        $.ajax({
                            type: 'POST'
                            , url: '/penjualan/updatedetail'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , no_fak_penj: no_fak_penj
                                , kode_barang: kode_barang
                                , jmldus: jmldus
                                , jmlpack: jmlpack
                                , jmlpcs: jmlpcs
                                , harga_dus: harga_dus
                                , harga_pack: harga_pack
                                , harga_pcs: harga_pcs
                                , total: total
                                , promo: promo
                            }
                            , cache: false
                            , success: function(respond) {
                                console.log(respond);
                                hitungdiskon2();
                            }
                        });
                    }
                } else {
                    if (jmlpack >= isipack) {
                        swal("Oops", "Jml Pack Melebihi Batas Maksimal, Masukan Ke Satuan Dus/Ball", "warning");
                        $tblrow.find('.jmlpack').val(0);
                        var total = (jmldus * harga_dus) + (0 * harga_pack) + (jmlpcs * harga_pcs);
                        $.ajax({
                            type: 'POST'
                            , url: '/penjualan/updatedetail'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , no_fak_penj: no_fak_penj
                                , kode_barang: kode_barang
                                , jmldus: jmldus
                                , jmlpack: 0
                                , jmlpcs: jmlpcs
                                , harga_dus: harga_dus
                                , harga_pack: harga_pack
                                , harga_pcs: harga_pcs
                                , total: total
                                , promo: promo
                            }
                            , cache: false
                            , success: function(respond) {
                                console.log(respond);
                                hitungdiskon2();
                            }
                        });

                    } else if (jmlpcs >= isipcs) {
                        swal("Oops", "Jml Pcs Melebihi Batas Maksimal, Masukan Ke Satuan Pack", "warning");
                        $tblrow.find('.jmlpcs').val(0);
                        var total = (jmldus * harga_dus) + (jmlpack * harga_pack) + (0 * harga_pcs);
                        $.ajax({
                            type: 'POST'
                            , url: '/penjualan/updatedetail'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , no_fak_penj: no_fak_penj
                                , kode_barang: kode_barang
                                , jmldus: jmldus
                                , jmlpack: jmlpack
                                , jmlpcs: 0
                                , harga_dus: harga_dus
                                , harga_pack: harga_pack
                                , harga_pcs: harga_pcs
                                , total: total
                                , promo: promo
                            }
                            , cache: false
                            , success: function(respond) {
                                console.log(respond);
                                hitungdiskon2();
                            }
                        });
                    } else {
                        var total = (jmldus * harga_dus) + (jmlpack * harga_pack) + (jmlpcs * harga_pcs);
                        $.ajax({
                            type: 'POST'
                            , url: '/penjualan/updatedetail'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , no_fak_penj: no_fak_penj
                                , kode_barang: kode_barang
                                , jmldus: jmldus
                                , jmlpack: jmlpack
                                , jmlpcs: jmlpcs
                                , harga_dus: harga_dus
                                , harga_pack: harga_pack
                                , harga_pcs: harga_pcs
                                , total: total
                                , promo: promo
                            }
                            , cache: false
                            , success: function(respond) {
                                console.log(respond);
                                hitungdiskon2();
                            }
                        });
                    }
                }
                if (!isNaN(total)) {
                    $tblrow.find('.total').text(convertToRupiah(total));
                }


            });

        });


    });

</script>
