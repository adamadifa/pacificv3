@php
$total = 0;
@endphp
@foreach ($detailtemp as $d)
@php
$isipcsdus = $d->isipcsdus;
$isipack = $d->isipack;
$isipcs = $d->isipcs;
$jumlah = $d->jumlah;
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
$total += $d->subtotal;
@endphp
<tr style="background-color:{{ $d->promo == 1 ? 'yellow' : '' }}">
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->kode_barang }}</td>
    <td>{{ $d->nama_barang }}</td>
    <td class="text-center">{{ !empty($jumlah_dus) ? rupiah($jumlah_dus) : '' }}</td>
    <td class="text-right">{{ rupiah($d->harga_dus) }}</td>
    <td class="text-center">{{ !empty($jumlah_pack) ? rupiah($jumlah_pack) : '' }}</td>
    <td class="text-right">{{ rupiah($d->harga_pack)  }}</td>
    <td class="text-center">{{ !empty($jumlah_pcs) ? rupiah($jumlah_pcs) : '' }}</td>
    <td class="text-right">{{ rupiah($d->harga_pcs) }}</td>
    <td class="text-right">{{ !empty($d->subtotal) ? rupiah($d->subtotal) : '' }}</td>
    <td>
        <a href="#" class="info edit" kode_barang="{{ $d->kode_barang }}" promo="{{ $d->promo }}"><i class="feather icon-edit"></i></a>
        <a href="#" class="danger hapus" kode_barang="{{ $d->kode_barang }}" promo="{{ $d->promo }}"><i class=" feather icon-trash"></i></a>
    </td>
</tr>
@endforeach
<tr style="font-weight: bold">
    <td colspan="9">TOTAL</td>
    <td class="text-right">
        {{ rupiah($total) }}
        <input type="hidden" id="totaltemp" name="totaltemp" value="{{ $total }}">
    </td>
</tr>

<script>
    $(function() {

        function nonaktifbutton() {
            $("#btnsimpan").prop('disabled', true);
            $("#btnsimpan").html('<i class="fa fa-spinner mr-1"></i><i>Loading...</i>');
        }

        function aktifbutton() {
            $("#btnsimpan").prop('disabled', false);
            $("#btnsimpan").html('<i class="feather icon-send mr-1"></i> Simpan');
        }

        function showtemp() {
            nonaktifbutton();
            $.ajax({
                type: 'GET'
                , url: '/penjualan/showbarangtempv2'
                , cache: false
                , success: function(respond) {
                    aktifbutton();
                    $("#loadbarangtemp").html(respond);
                    hitungdiskon();
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
                        nonaktifbutton();
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
                                aktifbutton();
                                swal(
                                    'Deleted!'
                                    , 'Data Berhasil Dihapus'
                                    , 'success'
                                )
                                showtemp();
                            }
                        });
                    }
                });
        });

        //Edit Barang
        $(".edit").click(function(e) {
            var kode_barang = $(this).attr("kode_barang");
            var promo = $(this).attr("promo");
            e.preventDefault();
            $.ajax({
                type: 'POST'
                , url: '/penjualan/editbarangtemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_barang: kode_barang
                    , promo: promo
                }
                , cache: false
                , success: function(respond) {
                    $("#loadeditbarang").html(respond);
                }
            });
            $('#mdleditbarang').modal({
                backdrop: 'static'
                , keyboard: false
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

        function hitungdiskon() {
            var tgltransaksi = $("#tgltransaksi").val();
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
                , url: '/hitungdiskon'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , jenistransaksi: jenistransaksi
                    , tgltransaksi: tgltransaksi
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

        function cektemp() {
            aktifbutton();
            $.ajax({
                type: 'GET'
                , url: '/cekpenjtemp'
                , success: function(respond) {
                    nonaktifbutton();
                    $("#cektemp").val(respond);
                }
            });
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
            cektemp();
        }


    });

</script>
