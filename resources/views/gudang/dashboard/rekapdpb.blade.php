@php
    $hariini = date('Y-m-d');
@endphp
@foreach ($barang as $p)
    @php
        ${"isipcs_$p->kode_produk"} = $p->isipcsdus;
    @endphp
@endforeach
<div class="table-responsive">

    <table class="table table-hover-animation">
        <thead>
            <tr>
                <th>Nama Cabang</th>
                @foreach ($barang as $p)
                    <th>{{ $p->kode_produk }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="font-medium-2">
            @foreach ($barang as $p)
                @php
                    ${"g$p->kode_produk"} = 0;
                @endphp
            @endforeach
            @foreach ($rekapgudang as $g)
            @endforeach
            <tr>
                <td>Gudang Pusat</td>
                @foreach ($barang as $d)
                    @php
                        if ($rekapgudang->{"saldo_$d->kode_produk"} <= 0) {
                            ${"color$d->kode_produk"} = 'bg-danger';
                        } else {
                            ${"color$d->kode_produk"} = 'bg-success';
                        }
                    @endphp
                    <td>
                        <span class='badge {{ ${"color$d->kode_produk"} }}'>
                            {{ rupiah($rekapgudang->{"saldo_$d->kode_produk"}) }}
                        </span>
                    </td>
                @endforeach
            </tr>
            @foreach ($rekapdpb as $r)
                <tr>
                    <td><?php echo ucwords($r->nama_cabang); ?></td>

                    @foreach ($barang as $d)
                        @php
                            $kode_produk = strtolower($d->kode_produk);
                            ${"s$kode_produk"} = $r->{"mg_$kode_produk"} + ROUND($r->{"saldo_$kode_produk"} / ${"isipcs_$d->kode_produk"}, 2) + ROUND($r->{"mutasi_$kode_produk"} / ${"isipcs_$d->kode_produk"}, 2) - $r->{"ambil_$kode_produk"} + $r->{"kembali_$kode_produk"};
                            if (${"s$kode_produk"} <= 0) {
                                ${"color$kode_produk"} = 'bg-danger';
                            } else {
                                ${"color$kode_produk"} = 'bg-success';
                            }
                            if (${"s$kode_produk"} < 0) {
                                ${"s$kode_produk"} = 0;
                            }
                        @endphp
                        <td>
                            <span class='badge {{ ${"color$kode_produk"} }}'>
                                {{ rupiah(floor(${"s$kode_produk"})) }}
                            </span>
                        </td>
                    @endforeach
                </tr>
            @endforeach


        </tbody>
    </table>
</div>
