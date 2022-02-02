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
        <a href="#" kode_barang="{{ $d->kode_barang }}" class="hapus"><i class="feather icon-trash danger"></i></a>
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
            var kode_pelanggan = $("#kode_pelanggan").val();
            $.ajax({
                type: 'POST'
                , url: '/cekreturtemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_pelanggan: kode_pelanggan
                }
                , success: function(respond) {
                    $("#cektemp").val(respond);
                }
            });
        }

        function loadtotal() {
            var kode_pelanggan = $("#kode_pelanggan").val();
            $.ajax({
                type: 'POST'
                , url: '/loadtotalreturtemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_pelanggan: kode_pelanggan
                }
                , success: function(respond) {
                    var total = parseInt(respond.replace(/\./g, ''));
                    $("#grandtotal").text(convertToRupiah(total));
                    // $("#total").val(convertToRupiah(grandtotal));
                    // $("#bruto").val(bruto);
                    $("#subtotal").val(total);
                    cektemp();
                }
            });
        }






        $(".money").maskMoney();



        function loadbarangtemp() {
            var kode_pelanggan = $("#kode_pelanggan").val();
            $.ajax({
                type: 'POST'
                , url: '/retur/showbarangtemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_pelanggan: kode_pelanggan
                , }
                , cache: false
                , success: function(respond) {
                    $("#loadbarangtemp").html(respond);
                    cektemp();
                }
            });
        }



        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_barang = $(this).attr("kode_barang");
            var kode_pelanggan = $("#kode_pelanggan").val();
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
                            , url: '/retur/deletebarangtemp'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , kode_barang: kode_barang
                                , kode_pelanggan: kode_pelanggan
                            }
                            , cache: false
                            , success: function(respond) {
                                swal(
                                    'Deleted!'
                                    , 'Data Berhasil Dihapus'
                                    , 'success'
                                )
                                loadbarangtemp();
                                loadtotal();
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
            $tblrow.find('.jmldus,.jmlpack,.jmlpcs,.harga_dus,.harga_pack,.harga_pcs').on('keyup', function() {
                //alert('test');
                var kode_pelanggan = $("#kode_pelanggan").val();
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
                            , url: '/retur/updatedetailtemp'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , kode_barang: kode_barang
                                , jmldus: jmldus
                                , jmlpack: jmlpack
                                , jmlpcs: 0
                                , harga_dus: harga_dus
                                , harga_pack: harga_pack
                                , harga_pcs: harga_pcs
                                , total: total
                                , kode_pelanggan: kode_pelanggan
                            }
                            , cache: false
                            , success: function(respond) {
                                console.log(respond);
                                loadtotal();
                            }
                        });
                    } else {
                        var total = (jmldus * harga_dus) + (jmlpack * harga_pack) + (jmlpcs * harga_pcs);
                        $.ajax({
                            type: 'POST'
                            , url: '/retur/updatedetailtemp'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , kode_barang: kode_barang
                                , jmldus: jmldus
                                , jmlpack: jmlpack
                                , jmlpcs: jmlpcs
                                , harga_dus: harga_dus
                                , harga_pack: harga_pack
                                , harga_pcs: harga_pcs
                                , total: total
                                , kode_pelanggan: kode_pelanggan
                            }
                            , cache: false
                            , success: function(respond) {
                                console.log(respond);
                                loadtotal();
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
                            , url: '/retur/updatedetailtemp'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , kode_barang: kode_barang
                                , jmldus: jmldus
                                , jmlpack: 0
                                , jmlpcs: jmlpcs
                                , harga_dus: harga_dus
                                , harga_pack: harga_pack
                                , harga_pcs: harga_pcs
                                , total: total
                                , kode_pelanggan: kode_pelanggan
                            }
                            , cache: false
                            , success: function(respond) {
                                console.log(respond);
                                loadtotal();
                            }
                        });

                    } else if (jmlpcs >= isipcs) {
                        swal("Oops", "Jml Pcs Melebihi Batas Maksimal, Masukan Ke Satuan Pack", "warning");
                        $tblrow.find('.jmlpcs').val(0);
                        var total = (jmldus * harga_dus) + (jmlpack * harga_pack) + (0 * harga_pcs);
                        $.ajax({
                            type: 'POST'
                            , url: '/retur/updatedetailtemp'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , kode_barang: kode_barang
                                , jmldus: jmldus
                                , jmlpack: jmlpack
                                , jmlpcs: 0
                                , harga_dus: harga_dus
                                , harga_pack: harga_pack
                                , harga_pcs: harga_pcs
                                , total: total
                                , kode_pelanggan: kode_pelanggan
                            }
                            , cache: false
                            , success: function(respond) {
                                console.log(respond);
                                loadtotal();
                            }
                        });
                    } else {
                        var total = (jmldus * harga_dus) + (jmlpack * harga_pack) + (jmlpcs * harga_pcs);
                        $.ajax({
                            type: 'POST'
                            , url: '/retur/updatedetailtemp'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , kode_barang: kode_barang
                                , jmldus: jmldus
                                , jmlpack: jmlpack
                                , jmlpcs: jmlpcs
                                , harga_dus: harga_dus
                                , harga_pack: harga_pack
                                , harga_pcs: harga_pcs
                                , total: total
                                , kode_pelanggan: kode_pelanggan
                            }
                            , cache: false
                            , success: function(respond) {
                                console.log(respond);
                                loadtotal();
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
