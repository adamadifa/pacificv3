<table class="table">
    <tr>
        <td>Cabang</td>
        <td>{{ $lebihsetor->kode_cabang }}</td>
    </tr>
    <tr>
        <td>Bulan</td>
        <td>{{ $bulan[$lebihsetor->bulan] }}</td>
    </tr>
</table>
<table class="table table-hover-animation">
    <thead class="thead-dark">
        <tr>
            <th>Tanggal</th>
            <th>Bank</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $d)
        <tr>

            <td>{{ date("d-m-Y",strtotime($d->tanggal_disetorkan)) }}</td>
            <td>{{ $d->nama_bank }}</td>
            <td class="text-right">{{ rupiah($d->jumlah) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
