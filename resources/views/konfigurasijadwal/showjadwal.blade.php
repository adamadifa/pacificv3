@foreach ($jadwal as $key => $d)
@php
$grup = @$jadwal[$key + 1]->grup;
@endphp
<tr>
    <td>{{ $d->nik }}</td>
    <td>{{ $d->nama_karyawan }}</td>
    <td>{{ $d->nama_jabatan }}</td>
    <td>{{ $d->nama_group }}</td>
</tr>
@if ($grup != $d->grup)
<tr style="background-color: rgb(156, 240, 156)">
    <td colspan="4"></td>
</tr>
@endif
@endforeach
