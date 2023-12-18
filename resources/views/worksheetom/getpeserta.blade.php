@foreach ($peserta as $d)
    <tr>
        <td>{{ $d->kode_pelanggan }}</td>
        <td>{{ $d->nama_pelanggan }}</td>
        <td>{{ $d->kode_cabang }}</td>
        <td>{{ $d->nama_karyawan }}</td>
    </tr>
@endforeach
