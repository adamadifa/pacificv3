@foreach ($rencana as $d)
<tr>
    <td>{{ $d->cicilan_ke }}</td>
    <td>{{ "01-".$d->bulan."-".$d->tahun }}</td>
    <td style="text-align: right">{{ rupiah($d->jumlah) }}</td>
</tr>
@endforeach
