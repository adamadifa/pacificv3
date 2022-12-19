<table class="table table-bordered">
    <tbody>
        @php
        $totalpf = 0;
        $totalgb = 0;
        $total = 0;
        @endphp
        @foreach ($detail as $d)
        @php
        $jmldus = floor($d->jumlah / $d->isipcsdus);
        $sisadus = $d->jumlah % $d->isipcsdus;

        if ($d->isipack == 0) {
        $jmlpack = 0;
        $sisapack = $sisadus;
        } else {

        $jmlpack = floor($sisadus / $d->isipcs);
        $sisapack = $sisadus % $d->isipcs;
        }

        $jmlpcs = $sisapack;


        $total += $d->subtotal;

        @endphp
        <tr>
            <td colspan="2">({{ $d->kode_produk }}) {{ $d->nama_barang }}</td>
        </tr>
        @if (!empty($jmldus))
        <tr>
            <td>{{ $jmldus }} Dus x {{ rupiah($d->harga_dus) }}</td>
            <td style="font-weight: bold; text-align:right">{{ rupiah($jmldus * $d->harga_dus) }}</td>
        </tr>
        @endif
        @if (!empty($jmlpack))
        <tr>
            <td>{{ $jmlpack }} Pack x {{ rupiah($d->harga_pack) }}</td>
            <td style="font-weight: bold; text-align:right">{{ rupiah($jmlpack * $d->harga_pack) }}</td>
        </tr>
        @endif
        @if (!empty($jmlpcs))
        <tr>
            <td>{{ $jmlpcs }} Pcs x {{ rupiah($d->harga_pcs) }}</td>
            <td style="font-weight: bold; text-align:right">{{ rupiah($jmlpcs * $d->harga_pcs) }}</td>
        </tr>
        @endif
        @endforeach
        <tr style="font-weight: bold">
            <td>Total Retur</td>
            <td class="text-right">{{ rupiah($total) }}</td>
        </tr>
    </tbody>
</table>
