<table class="table table-bordered">

    <tr>
        <th>No. BS</th>
        <td>{{ $badstok->no_bs }}</td>
    </tr>
    <tr>
        <th>Tanggal</th>
        <td>{{ DateToIndo2($badstok->tanggal) }}</td>
    </tr>
    <tr>
        <th>Cabang</th>
        <td>{{ $badstok->kode_cabang }}</td>
    </tr>

</table>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>NO.</th>
            <th>Kode Produk</th>
            <th>Nama Produk</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $d)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $d->kode_produk }}</td>
            <td>{{ $d->nama_barang }}</td>
            <td>{{ $d->jumlah }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
