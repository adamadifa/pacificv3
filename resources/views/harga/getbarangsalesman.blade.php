@foreach ($barang as $d)
<tr>
    <td>{{ $d->kode_barang }}</td>
    <td>{{ $d->nama_barang }}</td>
    <td class="text-right">{{ rupiah($d->harga_dus) }}</td>
    <td><a href="#" class="btn btn-sm btn-primary pilihbarang" kode_barang="{{ $d->kode_barang }}" nama_barang="{{ $d->nama_barang }}" isipcsdus="{{ $d->isipcsdus }}" isipcs="{{ $d->isipcs }}" isipack="{{ $d->isipack }}" harga_dus="{{ rupiah($d->harga_dus) }}" harga_pack="{{ rupiah($d->harga_pack) }}" harga_pcs="{{ rupiah($d->harga_pcs) }}">Pilih</a></td>
</tr>
@endforeach

<script>
    $(function() {
        $(".pilihbarang").click(function(e) {
            e.preventDefault();
            var kode_barang = $(this).attr("kode_barang");
            var nama_barang = $(this).attr("nama_barang");
            var harga_dus = $(this).attr("harga_dus");
            var harga_pack = $(this).attr("harga_pack");
            var harga_pcs = $(this).attr("harga_pcs");
            var isipcsdus = $(this).attr("isipcsdus");
            var isipcs = $(this).attr("isipcs");
            var isipack = $(this).attr("isipack");
            var nama_pelanggan = $("#nama_pelanggan").val();
            if (nama_pelanggan.includes('KPBN')) {
                $("#harga_dus").prop('readonly', false);
                $("#harga_pack").prop('readonly', false);
                $("#harga_pcs").prop('readonly', false);
            } else {
                $("#harga_dus").prop('readonly', true);
                $("#harga_pack").prop('readonly', true);
                $("#harga_pcs").prop('readonly', true);
            }

            if ($('#promo').is(":checked")) {
                var harga_dus = 0;
                var harga_pack = 0;
                var harga_pcs = 0;
            }
            $("#kode_barang").val(kode_barang);
            $("#nama_barang").val(nama_barang);
            $("#harga_dus").val(harga_dus);
            $("#harga_pack").val(harga_pack);
            $("#harga_pcs").val(harga_pcs);

            $("#harga_dus_old").val(harga_dus);
            $("#harga_pack_old").val(harga_pack);
            $("#harga_pcs_old").val(harga_pcs);

            $("#isipcsdus").val(isipcsdus);
            $("#isipcs").val(isipcs);
            if (isipack == 0) {
                //$("#harga_pack").prop("readonly", true);
                $("#jml_pack").prop("readonly", true);
            } else {
                //$("#harga_pack").prop("readonly", false);
                $("#jml_pack").prop("readonly", false);
            }
            $("#mdlbarang").modal("hide");
        });
    });

</script>
