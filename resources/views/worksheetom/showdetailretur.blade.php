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

        //Pelunasan
        $jmldus_pelunasan = floor($d->jumlahpelunasan / $d->isipcsdus);
        $sisadus_pelunasan = $d->jumlahpelunasan % $d->isipcsdus;

        if ($d->isipack == 0) {
            $jmlpack_pelunasan = 0;
            $sisapack_pelunasan = $sisadus_pelunasan;
        } else {
            $jmlpack_pelunasan = floor($sisadus_pelunasan / $d->isipcs);
            $sisapack_pelunasan = $sisadus_pelunasan % $d->isipcs;
        }

        $jmlpcs_pelunasan = $sisapack_pelunasan;

        //Sisa
        $jmlsisa = $d->jumlah - $d->jumlahpelunasan;
        $jmldus_sisa = floor($jmlsisa / $d->isipcsdus);
        $sisadus_sisa = $jmlsisa % $d->isipcsdus;

        if ($d->isipack == 0) {
            $jmlpack_sisa = 0;
            $sisapack_sisa = $sisadus_sisa;
        } else {
            $jmlpack_sisa = floor($sisadus_sisa / $d->isipcs);
            $sisapack_sisa = $sisadus_sisa % $d->isipcs;
        }

        $jmlpcs_sisa = $sisapack_sisa;
    @endphp
    <tr>

        <td>{{ $d->kode_produk }}</td>
        <td>{{ $d->nama_barang }}</td>
        <td class="text-center">{{ !empty($jmldus) ? $jmldus : '' }}</td>
        <td class="text-center">{{ !empty($jmlpack) ? $jmlpack : '' }}</td>
        <td class="text-center">{{ !empty($jmlpcs) ? $jmlpcs : '' }}</td>

        <td class="text-center">{{ !empty($jmldus_pelunasan) ? $jmldus_pelunasan : '' }}</td>
        <td class="text-center">{{ !empty($jmlpack_pelunasan) ? $jmlpack_pelunasan : '' }}</td>
        <td class="text-center">{{ !empty($jmlpcs_pelunasan) ? $jmlpcs_pelunasan : '' }}</td>

        <td class="text-center">{{ !empty($jmldus_sisa) ? $jmldus_sisa : '' }}</td>
        <td class="text-center">{{ !empty($jmlpack_sisa) ? $jmlpack_sisa : '' }}</td>
        <td class="text-center">{{ !empty($jmlpcs_sisa) ? $jmlpcs_sisa : '' }}</td>
        <td>
            @if ($jmlsisa == 0)
                <span class="badge bg-success"><i class="feather icon-check"></i></span>
            @endif
        </td>
    </tr>
@endforeach
