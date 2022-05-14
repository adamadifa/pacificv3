<table class="table">
    <tr>
        <td>Kode Saldo Awal</td>
        <td>{{ $saldoawal->kode_saldoawal_bb }}</td>
    </tr>
    <tr>
        <td>Bulan</td>
        <td>{{ $bulan[$saldoawal->bulan] }}</td>
    </tr>
    <tr>
        <td>Tahun</td>
        <td>{{ $saldoawal->tahun }}</td>
    </tr>
</table>
<table class="table table-hover-animation">
    <thead>
        <tr>
            <th>Kode Akun - Nama Akun</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $d)
        <tr>
            <td style="font-weight: {{ !empty($d->parent) ? 'bold' : '' }}">
                {{ $d->kode_akun }} {{ $d->nama_akun }}</td>
            <td class="text-right">
                {{ !empty($d->saldoawal) ? desimal($d->saldoawal) : '' }}
            </td>
        </tr>
        @endforeach

    </tbody>
</table>
