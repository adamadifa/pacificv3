@foreach ($detail as $d)
    <input type="hidden" name="kode_produk[]" value="{{ $d->kode_produk }}">
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $d->nama_barang }}</td>
        <td>
            <input type="text" name="bufferstok[]" value="{{ $d->jmlbufferstok }}" style="text-align: right"
                class="form-control">
        </td>
        <td>
            <input type="text" name="limitstok[]" value="{{ $d->jmllimitstok }}" style="text-align: right"
                class="form-control">
        </td>
    </tr>
@endforeach
