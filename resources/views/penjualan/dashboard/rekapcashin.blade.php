<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>NO</th>
            <th>CABANG</th>
            <th>TUNAI KREDIT</th>
            <th>CASH IN</th>
        </tr>
    </thead>
    <tbody>
        @php
        $no=1;
        $totalpenjualan = 0;
        $totalbayar = 0;
        @endphp
        @foreach ($rekap as $d)
        @php
        $totalpenjualan += $d->netto;
        $totalbayar += $d->totalbayar;
        @endphp
        <tr>
            <td>{{ $no; }}</td>
            <td class="font-weight-bold"> {{ strtoupper($d->nama_cabang) }}</td>
            <td align="right" class="text-info font-weight-bold">{{ number_format($d->netto,'0','','.') }}</td>
            <td align="right" class="text-success font-weight-bold">{{ number_format($d->totalbayar,'0','','.') }}</td>
        </tr>
        @php
        $no++;
        @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">TOTAL</th>
            <th class="text-right text-primary font-weight-bold">{{ number_format($totalpenjualan,'0','','.') }}</th>
            <th class="text-right text-primary font-weight-bold">{{ number_format($totalbayar,'0','','.') }}</th>
        </tr>
    </tfoot>
</table>
