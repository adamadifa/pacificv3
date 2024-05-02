@foreach ($detail as $d)
    @php
        $qtysaldoawal = $d->qtysaldoawal;
        $qtypemasukan = $d->qtypemasukan;
        $qtypengeluaran = $d->qtypengeluaran;
        $hasilqty = $qtysaldoawal + $qtypemasukan - $qtypengeluaran;
        $qtyrata = $d->qtysaldoawal + $d->qtypemasukan;
        if (!empty($qtyrata)) {
            $qtyrata = $d->qtysaldoawal + $d->qtypemasukan;
        } else {
            $qtyrata = 1;
        }

        if (empty($d->hargasaldoawal) and $d->hargasaldoawal == 0) {
            $hasilharga = $d->hargapemasukan;
        } elseif (empty($d->hargapemasukan) and $d->hargapemasukan == 0) {
            $hasilharga = $d->hargasaldoawal;
        } else {
            $hasilharga = ($d->totalsa * 1 + $d->totalpemasukan * 1) / $qtyrata;
        }

    @endphp
    @if (!empty($hasilqty))
        <tr>
            <td>{{ $no }}</td>
            <td>
                <input type="hidden" name="kode_barang[]" value="{{ $d->kode_barang }}">
                {{ $d->kode_barang }}
            </td>
            <td>{{ $d->nama_barang }}</td>
            <td class="text-right">
                <input type="hidden" name="qty[]" value="{{ !empty($hasilqty) ? ROUND($hasilqty, 2) : 0 }}">
                {{ desimal($hasilqty) }}
            </td>
            <td class="text-right">
                <input type="hidden" name="harga[]" value="{{ !empty($hasilharga) ? ROUND($hasilharga, 2) : 0 }}">
                {{ desimal($hasilharga) }}
            </td>
            <td class="text-right">{{ desimal($hasilqty * $hasilharga) }}</td>
        </tr>
        @php
            $no++;
        @endphp
    @endif
@endforeach
