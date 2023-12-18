@foreach ($peserta as $d)
    @php
        $sisa = $program->jml_target - $d->jmldus;
    @endphp
    <tr>
        <td>{{ $d->kode_pelanggan }}</td>
        <td>{{ $d->nama_pelanggan }}</td>
        <td>{{ $d->kode_cabang }}</td>
        <td>{{ $d->nama_karyawan }}</td>
        <td style="text-align: center">{{ rupiah($d->jmldus) }}</td>
        <td style="text-align: center">{{ $sisa > 0 ? rupiah($sisa) : 0 }}</td>
    </tr>
@endforeach
