@foreach ($detailtemp as $d)
<tr>
    <td>{{ $d->nama_karyawan }}</td>
    <td class="text-right">{{ rupiah($d->jumlah) }}</td>
    <td>
        <a href="#" data-id="{{ $d->id }}" class="danger hapus"><i class="feather icon-trash"></i></a>
    </td>
</tr>
@endforeach
