@foreach ($jadwal as $d)
<tr>
    <td>{{ $d->nik }}</td>
    <td>{{ $d->nama_karyawan }}</td>
    <td>{{ $d->nama_jabatan }}</td>
    <td>{{ $d->nama_group }}</td>
</tr>
@endforeach
