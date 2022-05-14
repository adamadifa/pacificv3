@foreach ($detail as $d)
<tr>
    <td style="font-weight: {{ !empty($d->parent) ? 'bold' : '' }}">
        <input type="hidden" name="kode_akun[]" value="{{ $d->kode_akun }}">
        {{ $d->kode_akun }} {{ $d->nama_akun }}</td>
    <td class="text-right">
        <input type="hidden" class="form-control text-right jumlah" name="jumlah[]" value="{{ $d->saldoakhir }}">
        {{ !empty($d->saldoakhir) ? desimal($d->saldoakhir) : '' }}
    </td>
</tr>
@endforeach
