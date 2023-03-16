@foreach ($rencana as $d)
<tr>
    <td>{{ $d->cicilan_ke }}</td>
    <td>{{ "01-".$d->bulan."-".$d->tahun }}</td>
    <td style="text-align: right">{{ rupiah($d->jumlah) }}</td>
    <td style="text-align: right">{{ rupiah($d->bayar) }}</td>
    <td style="text-align: right">
        @php
        $sisatagihan = $d->jumlah - $d->bayar;
        @endphp
        {{ rupiah($sisatagihan) }}
    </td>
</tr>
@endforeach
