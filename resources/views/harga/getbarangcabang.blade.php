{{-- <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="home-tab-fill" data-toggle="tab" href="#home-fill" role="tab" aria-controls="home-fill" aria-selected="true">Harga Lama</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="profile-tab-fill" data-toggle="tab" href="#profile-fill" role="tab" aria-controls="profile-fill" aria-selected="false">Harga Baru (2023)</a>
    </li>
</ul>
<div class="tab-content pt-1">
    <div class="tab-pane active" id="home-fill" role="tabpanel" aria-labelledby="home-tab-fill">

    </div>
    <div class="tab-pane" id="profile-fill" role="tabpanel" aria-labelledby="profile-tab-fill">
        <table class="table table-hover-animation" style="width:100% !important">
            <thead class="thead-dark">
                <tr>
                    <th>Kode Barang</th>
                    <th>Kode Produk</th>
                    <th>Nama Barang</th>
                    <th>Harga / Dus</th>
                    <th>Harga / Pack</th>
                    <th>Harga / Pcs</th>
                    <th>Kategori</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($barangnew as $d)
                <tr>
                    <td>{{ $d->kode_barang }}</td>
<td>{{ $d->kode_produk }}</td>
<td>{{ $d->nama_barang }}</td>
<td class="text-right">{{ rupiah($d->harga_dus) }}</td>
<td class="text-right">{{ rupiah($d->harga_pack) }}</td>
<td class="text-right">{{ rupiah($d->harga_pcs) }}</td>
<td class="text-right">{{ $d->kategori_harga }}</td>
<td><a href="#" class="btn btn-sm btn-primary pilihbarang" kode_barang="{{ $d->kode_barang }}" nama_barang="{{ $d->nama_barang }}" isipcsdus="{{ $d->isipcsdus }}" isipcs="{{ $d->isipcs }}" harga_dus="{{ rupiah($d->harga_dus) }}" harga_pack="{{ rupiah($d->harga_pack) }}" harga_pcs="{{ rupiah($d->harga_pcs) }}">Pilih</a></td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div> --}}
<table class="table table-hover-animation" id="mybarang" style="width:100% !important">
    <thead class="thead-dark">
        <tr>
            <th>Kode Barang</th>
            <th>Kode Produk</th>
            <th>Nama Barang</th>
            <th>Harga / Dus</th>
            <th>Harga / Pack</th>
            <th>Harga / Pcs</th>
            <th>Kategori</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($barang as $d)
        <tr>
            <td>{{ $d->kode_barang }}</td>
            <td>{{ $d->kode_produk }}</td>
            <td>{{ $d->nama_barang }}</td>
            <td class="text-right">{{ rupiah($d->harga_dus) }}</td>
            <td class="text-right">{{ rupiah($d->harga_pack) }}</td>
            <td class="text-right">{{ rupiah($d->harga_pcs) }}</td>
            <td class="text-right">{{ $d->kategori_harga }}</td>
            <td><a href="#" class="btn btn-sm btn-primary pilihbarang" kode_barang="{{ $d->kode_barang }}" nama_barang="{{ $d->nama_barang }}" isipcsdus="{{ $d->isipcsdus }}" isipcs="{{ $d->isipcs }}" harga_dus="{{ rupiah($d->harga_dus) }}" harga_pack="{{ rupiah($d->harga_pack) }}" harga_pcs="{{ rupiah($d->harga_pcs) }}">Pilih</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $("#mybarang").DataTable({
            order: [
                [1, 'asc']
            ]
        , });



        $('#mybarang tbody').on('click', '.pilihbarang', function(e) {

            e.preventDefault();
            var kode_barang = $(this).attr("kode_barang");
            var nama_barang = $(this).attr("nama_barang");
            var harga_dus = $(this).attr("harga_dus");
            var harga_pack = $(this).attr("harga_pack");
            var harga_pcs = $(this).attr("harga_pcs");
            var isipcsdus = $(this).attr("isipcsdus");
            var isipcs = $(this).attr("isipcs");
            var nama_pelanggan = $("#nama_pelanggan").val();
            if ($('#promo').is(":checked")) {
                var harga_dus = 0;
                var harga_pack = 0;
                var harga_pcs = 0;
            }

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

            $("#mdlbarang").modal("hide");
        });
    });

</script>
