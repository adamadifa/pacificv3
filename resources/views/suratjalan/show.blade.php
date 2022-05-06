<table class="table">
    <tr>
        <td>No. Surat Jalan</td>
        <td>{{$mutasi->no_mutasi_gudang}}</td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>{{ DateToIndo2($mutasi->tgl_mutasi_gudang)}}</td>
    </tr>
    <tr>
        <td>No. Permintaan</td>
        <td>{{$mutasi->no_permintaan_pengiriman}}</td>
    </tr>
    <tr>
        <td>Tanggal Permintaan</td>
        <td>{{DateToIndo2($mutasi->tgl_permintaan_pengiriman)}}</td>
    </tr>
    <tr>
        <td>Cabang</td>
        <td>{{$mutasi->nama_cabang}}</td>
    </tr>
    <tr>
        <td>Keterangan</td>
        <td>{{$mutasi->keterangan}}</td>
    </tr>
    <tr>
        <td>Status</td>
        <td>
            @if ($mutasi->status_sj==0)
            <span class="badge bg-danger">Belum Diterima Cabang</span>
            @elseif($mutasi->status_sj==1)
            <span class="badge bg-success">Sudah Diterima Cabang</span>
            @elseif($mutasi->status_sj ==2)
            <span class="badge bg-info">Transit Out</span>
            @endif

        </td>
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
