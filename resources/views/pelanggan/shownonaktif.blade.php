<div class="row mb-2">
    <div class="col-12">
        <a href="/pelanggan/updatenonaktif" class="btn btn-danger"><i class="feather icon-slash mr-1"></i>Nonaktifkan</a>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Kode Pelanggan</th>
                    <th>Nama Pelanggan</th>
                    <th>Terakhir Transaksi</th>
                    <th>Lama</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pelanggan as $d)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d->kode_pelanggan }}</td>
                    <td>{{ $d->nama_pelanggan }}</td>
                    <td>{{ DateToIndo2($d->lasttransaksi) }}</td>
                    <td>{{ $d->lama }} Hari</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
