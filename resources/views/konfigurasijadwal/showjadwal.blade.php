@foreach ($jadwal as $d)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->nama_karyawan }}</td>
    <td>{{ $d->nama_jabatan }}</td>
    <td>{{ $d->nama_group }}</td>
</tr>
@endforeach
