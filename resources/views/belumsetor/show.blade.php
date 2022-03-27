<table class="table">
    <tr>
        <td>Cabang</td>
        <td>{{ $belumsetor->kode_cabang }}</td>
    </tr>
    <tr>
        <td>Bulan</td>
        <td>{{ $bulan[$belumsetor->bulan] }}</td>
    </tr>
</table>
<table class="table table-hover-animation">
    <thead class="thead-dark">
        <tr>
            <th>ID Salesman</th>
            <th>Nama Karyawan</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $d)
        <tr>

            <td>{{ $d->id_karyawan }}</td>
            <td>{{ $d->nama_karyawan }}</td>
            <td class="text-right">{{ rupiah($d->jumlah) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
