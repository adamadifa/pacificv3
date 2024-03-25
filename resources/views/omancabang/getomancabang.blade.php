@php
    $no = 1;
@endphp
@foreach ($produk as $d)
    @php
        $subtotal = $d->mingguke_1 + $d->mingguke_2 + $d->mingguke_3 + $d->mingguke_4;
    @endphp
    <tr>
        <td>
            <input type="hidden" name="kode_produk{{ $loop->iteration }}" value="{{ $d->kode_produk }}">
            {{ $loop->iteration }}
        </td>
        <td>{{ $d->kode_produk }}</td>
        <td>{{ $d->nama_barang }}</td>
        <td colspan="3">
            <input type="text" id="jmlm1" name="jml{{ $loop->iteration }}m1" value="{{ $d->mingguke_1 }}"
                class="form-control jmlm1" style="text-align:right" />
        </td>
        <td colspan="3">
            <input type="text" id="jmlm2" name="jml{{ $loop->iteration }}m2" value="{{ $d->mingguke_2 }}"
                class="form-control jmlm2" style="text-align:right" />
        </td>
        <td colspan="3">
            <input type="text" id="jmlm3" name="jml{{ $loop->iteration }}m3" value="{{ $d->mingguke_3 }}"
                class="form-control jmlm3" style="text-align:right" />
        </td>
        <td colspan="3">
            <input type="text" id="jmlm4" name="jml{{ $loop->iteration }}m4" value="{{ $d->mingguke_4 }}"
                class="form-control jmlm4" style="text-align:right" />
        </td>
        <td>
            <input type="text" id="subtotal" name="subtotal{{ $loop->iteration }}" value="{{ $subtotal }}"
                class="form-control subtotal" style="text-align:right" />
        </td>
    </tr>
    @php
        $no++;
    @endphp
@endforeach
<input type="hidden" name="jumproduk" id="jumproduk" value="{{ $no - 1 }}">
