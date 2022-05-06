<table class="table">
    <tr>
        <td>No. Repack/Reject</td>
        <td>{{$mutasi->no_mutasi_gudang}}</td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>{{ DateToIndo2($mutasi->tgl_mutasi_gudang)}}</td>
    </tr>
</table>
<table class="table table-hover-animation">
    <thead>
        <tr>
            <th>No.</th>
            <th>Kode Produk</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $d)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$d->kode_produk}}</td>
            <td>{{$d->nama_barang}}</td>
            <td class="text-right">{{rupiah($d->jumlah)}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
