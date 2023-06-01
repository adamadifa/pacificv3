@foreach ($hargaawal as $d)

<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->kode_produk }}
        <input type="hidden" name="kode_produk[]" value="{{ $d->kode_produk }}">
    </td>
    <td>{{ $d->nama_barang }}</td>
    <td align="right">
        <input type="text" class="form-control text-right harga_awal" autocomplete="off" name="harga_awal[]" value="{{ !empty($d->harga_awal) ?  rupiah($d->harga_awal) : '' }}" id="harga_awal">
    </td>
</tr>
@endforeach

<script>
    // $(function() {
    //     $(".harga_awal").maskMoney();
    // });

</script>
