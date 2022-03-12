@foreach ($kaskeciltemp as $d)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->keterangan }}</td>
    <td align="right">{{ rupiah($d->jumlah) }}</td>
    <td>{{ $d->kode_akun }} {{ $d->nama_akun }}</td>
    <td>
        @php
        if ($d->status_dk == "D") {
        $inout = "OUT";
        $color = "danger";
        } else {
        $inout = "IN";
        $color = "success";
        }
        @endphp
        <span class="badge bg-{{ $color }}">{{ $inout }}</span>
    </td>
    @if (Auth::user()->kode_cabang == "PCF")
    <td>{{ $d->peruntukan }}</td>
    @endif
    <td>
        <a href="#" class="hapus"><i class="feather icon-trash danger"></i></a>
    </td>
</tr>
@endforeach
