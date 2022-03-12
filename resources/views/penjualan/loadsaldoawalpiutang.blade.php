@foreach ($saldoawalpiutang as $d)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->id_karyawan }}</td>
    <td>{{ $d->nama_karyawan }}</td>
    <td class="text-right">{{ rupiah($d->saldo_piutang) }}</td>
</tr>
@endforeach
