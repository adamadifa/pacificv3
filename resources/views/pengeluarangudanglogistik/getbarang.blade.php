<table class="table" id="tabelbarang">
    <thead>
        <tr>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>#</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($barang as $d)
        <tr>
            <td>{{ $d->kode_barang }}</td>
            <td>{{ $d->nama_barang }}</td>
            <td><a href="#" class="pilihbarang" kode_barang="{{ $d->kode_barang }}" nama_barang="{{ $d->nama_barang }}"><i class="feather icon-external-link success"></i></a></td>
        </tr>
        @endforeach
    </tbody>
</table>


<script>
    $(function() {


        $("#tabelbarang").DataTable();
        $('#tabelbarang').on('click', '.pilihbarang', function() {
            var kode_barang = $(this).attr('kode_barang');
            var nama_barang = $(this).attr('nama_barang');
            $("#kode_barang").val(kode_barang);
            $("#nama_barang").val(nama_barang);
            $("#mdlpilihbarang").modal("hide");
        });


    });

</script>
