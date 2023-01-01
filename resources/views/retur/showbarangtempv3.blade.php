@php
$total = 0;
@endphp
@foreach ($detailtemp as $d)
@php
$isipcsdus = $d->isipcsdus;
$isipack = $d->isipack;
$isipcs = $d->isipcs;
$jumlah = $d->jumlah;
$jmldus = floor($jumlah / $isipcsdus);
if ($jumlah != 0) {
$sisadus = $jumlah % $isipcsdus;
} else {
$sisadus = 0;
}
if ($isipack == 0) {
$jmlpack = 0;
$sisapack = $sisadus;
} else {
$jmlpack = floor($sisadus / $isipcs);
$sisapack = $sisadus % $isipcs;
}

$jmlpcs = $sisapack;
$total += $d->subtotal;
@endphp
<tr>
    <td colspan="7" style="font-weight: bold">{{ $d->nama_barang }}</td>
    <td style="text-align: right">
        {{-- <a href="#" class="info edit" kode_barang="{{ $d->kode_barang }}"><i class="feather icon-edit"></i></a> --}}
        <a href="#" class="danger hapus" kode_barang="{{ $d->kode_barang }}"><i class=" feather icon-trash"></i></a>
    </td>
</tr>
@if (!empty($jmldus))
<tr>
    <td colspan="7">{{ $jmldus }} Dus x {{ rupiah($d->harga_dus) }}</td>
    <td style="font-weight: bold; text-align:right">{{ rupiah($jmldus * $d->harga_dus) }}</td>
</tr>
@endif
@if (!empty($jmlpack))
<tr @if ($d->promo ==1)
    class="bg-warning"
    @endif>
    <td colspan="7">{{ $jmlpack }} Pack x {{ rupiah($d->harga_pack) }}</td>
    <td style="font-weight: bold; text-align:right">{{ rupiah($jmlpack * $d->harga_pack) }}</td>
</tr>
@endif

@if (!empty($jmlpcs))
<tr>
    <td colspan="7">{{ $jmlpcs }} Pcs x {{ rupiah($d->harga_pcs) }}</td>
    <td style="font-weight: bold; text-align:right">{{ rupiah($jmlpcs * $d->harga_pcs) }}</td>
</tr>
@endif
@endforeach
<tr style="font-weight: bold">
    <td colspan="7">TOTAL</td>
    <td class="text-right">
        {{ rupiah($total) }}
        <input type="hidden" id="totaltemp" name="totaltemp" value="{{ $total }}">
    </td>
</tr>
<script>
    $(function() {
        function loadbarangtemp() {
            var kode_pelanggan = $("#kode_pelanggan").val();
            $.ajax({
                type: 'POST'
                , url: '/retur/showbarangtempv2'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_pelanggan: kode_pelanggan
                , }
                , cache: false
                , success: function(respond) {
                    $("#loadbarangtemp").html(respond);
                    loadtotal();
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
                            }
                        });
                    }
                });
        });
    });

</script>
