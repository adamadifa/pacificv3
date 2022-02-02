<table class="table table-bordered">
    <thead>
        <tr>
            <th>Kode Produk</th>
            <th>Nama Barang</th>
            <th class="text-center">Dus</th>
            <th>Harga/Dus</th>
            <th class="text-center">Pack</th>
            <th>Harga/Pack</th>
            <th class="text-center">Pcs</th>
            <th>Harga/Pcs</th>
            <th>Total</th>
        </tr>
    </thead>
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

            <td>{{ $d->kode_produk }}</td>
            <td>{{ $d->nama_barang }}</td>
            <td class="text-center">{{ $jmldus }}</td>
            <td class="text-right">{{ rupiah($d->harga_dus) }}</td>
            <td class="text-center">{{ $jmlpack }}</td>
            <td class="text-right">{{ rupiah($d->harga_pack) }}</td>
            <td class="text-center">{{ $jmlpcs }}</td>
            <td class="text-right">{{ rupiah($d->harga_pcs) }}</td>
            <td class="text-right">{{ rupiah($d->subtotal) }}</td>
        </tr>
        @endforeach
        <tr style="font-weight: bold">
            <td colspan="8">Total Retur</td>
            <td class="text-right">{{ rupiah($total) }}</td>
        </tr>
    </tbody>
</table>
