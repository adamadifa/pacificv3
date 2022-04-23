<table class="table table-bordered">
    <tr>
        <td>No. BPBJ</td>
        <td>{{ $bpbj->no_mutasi_produksi }}</td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>{{ DateToIndo2($bpbj->tgl_mutasi_produksi) }}</td>
    </tr>
</table>
<table class="table table-hover-animation">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Shift</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $d)
        <tr>
            <td>{{ $d->kode_produk }}</td>
            <td>{{ $d->nama_barang }}</td>
            <td>{{ $d->shift }}</td>
            <td class="text-right">{{ rupiah($d->jumlah) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
