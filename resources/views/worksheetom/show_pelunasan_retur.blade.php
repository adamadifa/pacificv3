@foreach ($pelunasanretur as $d)
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
    @endphp
    <tr>
        <td>{{ $d->kode_produk }}</td>
        <td>{{ $d->nama_barang }}</td>
        <td>{{ $jmldus }}</td>
        <td>{{ $jmlpack }}</td>
        <td>{{ $jmlpcs }}</td>
        <td>{{ $d->no_dpb }}</td>
        <td>
            <a href="#" kode_barang="{{ $d->kode_barang }}" class="hapus"><i
                    class="feather icon-trash danger"></i></a>
        </td>
    </tr>
@endforeach
