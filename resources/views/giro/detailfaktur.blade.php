<table class="table table-bordered">
    <thead>
        <tr>
            <th>No. Faktur</th>
            <th>Jumlah</th>
            <th>Tgl Pencatatan</th>
            <th>Tgl Input</th>
            <th>Tgl Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detailfaktur as $d)
        <tr>
            <td>{{ $d->no_fak_penj }}</td>
            <td class="text-right">{{ rupiah($d->jumlah) }}</td>
            <td>{{ date("d-m-Y",strtotime($d->tgl_giro)) }}</td>
            <td>{{ date("d-m-Y H:i:s",strtotime($d->tgl_input)) }}</td>
            <td>{{ date("d-m-Y H:i:s",strtotime($d->tgl_aksi)) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
