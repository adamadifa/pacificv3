<table class="table table-bordered">
    <tr>
        <td>No. Bukti</td>
        <td>{{ $pemasukan->nobukti_pemasukan }}</td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>{{ DateToIndo2($pemasukan->tgl_pemasukan) }}</td>
    </tr>
    <tr>
        <td>Asal Barang</td>
        <td>{{ $pemasukan->departemen }}</td>
    </tr>

</table>
<table class="table table-hover-animation">
    <thead class="thead-dark">
        <tr>
            <th colspan="8">Data Pemasukan</th>
        </tr>
        <tr>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Keterangan</th>
            <th>Qty Unit</th>
            <th>Qty Berat</th>
            <th>Qty Lebih</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $d)
        <tr>
            <td>{{ $d->kode_barang }}</td>
            <td>{{ $d->nama_barang }}</td>
            <td>{{ $d->keterangan }}</td>
            <td class="text-center">{{ desimal($d->qty_unit) }}</td>
            <td class="text-right">{{ desimal($d->qty_berat) }}</td>
            <td class="text-right">{{ desimal($d->qty_lebih) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
