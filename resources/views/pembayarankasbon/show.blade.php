<table class="table table-striped">
    <thead class="thead-dark">
        <tr>
            <th>No.</th>
            <th>No. Kasbon</th>
            <th>Nik</th>
            <th>Nama Karyawan</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @php
        $total = 0;
        @endphp
        @foreach ($historibayar as $d)
        @php
        $total+= $d->jumlah;
        @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $d->no_kasbon }}</td>
            <td>{{ $d->nik }}</td>
            <td>{{ $d->nama_karyawan }}</td>
            <td style="text-align: right">{{ rupiah($d->jumlah) }}</td>
        </tr>
        @endforeach
    </tbody>
    <thead class="thead-dark">
        <th colspan="4">TOTAL</th>
        <th style="text-align: right">{{ rupiah($total) }}</th>
    </thead>
</table>
