@foreach ($hpp as $d)

<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->kode_produk }}
        <input type="hidden" name="kode_produk[]" value="{{ $d->kode_produk }}">
    </td>
    <td>{{ $d->nama_barang }}</td>
    <td align="right">
        <input type="text" class="form-control text-right harga_hpp" name="harga_hpp[]" value="{{ !empty($d->harga_hpp) ?  rupiah($d->harga_hpp) : '' }}" id="harga_hpp">
    </td>
</tr>
@endforeach

<script>


</script>
