<table class="table table-bordered">
    <tr>
        <td>No. Bukti</td>
        <td>{{ $pengeluaranproduksi->nobukti_pengeluaran }}</td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>{{ DateToIndo2($pengeluaranproduksi->tgl_pengeluaran) }}</td>
    </tr>
    <tr>
        <td>Jenis Pengeluaran</td>
        <td>{{ $pengeluaranproduksi->kode_dept }}</td>
    </tr>
</table>
<table class="table table-hover-animaton">
    <thead>
        <tr>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th>Ket</th>
            <th>Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $d)
        <tr>
            <td>{{ $d->kode_barang }}</td>
            <td>{{ $d->nama_barang }}</td>
            <td>{{ $d->satuan }}</td>
            <td>{{ $d->keterangan }}</td>
            <td class="text-right">{{ desimal($d->qty) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
