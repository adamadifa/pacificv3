<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>NO</th>
            <th>CABANG</th>
            <th>1 S/D 15</th>
            <th>16 S/D 31</th>
            <th>32 S/D 45</th>
            <th>46 S/D 2B</th>
            <th>2B S/D 3B</th>
            <th>3B S/D 6B</th>
            <th>6B+</th>
        </tr>
    </thead>
    <tbody>
        @php
        $no=1;
        $totalduaminggu = 0;
        $totalsatubulan = 0;
        $totalsatubulan15 = 0;
        $totalduabulan = 0;
        $totaltigabulan = 0;
        $totalenambulan = 0;
        $totallebihenambulan = 0;
        @endphp
        @foreach ($aup as $d)
        @php
        $totalduaminggu += $d->duaminggu;
        $totalsatubulan +=$d->satubulan;
        $totalsatubulan15 +=$d->satubulan15;
        $totalduabulan +=$d->duabulan;
        $totaltigabulan +=$d->tigabulan;
        $totalenambulan +=$d->enambulan;
        $totallebihenambulan +=$d->lebihenambulan;
        @endphp
        <tr>
            <td>{{ $no; }}</td>
            <td class="text-primary font-weight-bold">{{ $d->kode_cabang }}</td>
            <td align="right" class=" font-weight-bold">{{ number_format($d->duaminggu,'0','','.') }}</td>
            <td align="right" class=" font-weight-bold">{{ number_format($d->satubulan,'0','','.') }}</td>
            <td align="right" class=" font-weight-bold">{{ number_format($d->satubulan15,'0','','.') }}</td>
            <td align="right" class=" font-weight-bold">{{ number_format($d->duabulan,'0','','.') }}</td>
            <td align="right" class=" font-weight-bold">{{ number_format($d->tigabulan,'0','','.') }}</td>
            <td align="right" class=" font-weight-bold">{{ number_format($d->enambulan,'0','','.') }}</td>
            <td align="right" class=" font-weight-bold">{{ number_format($d->lebihenambulan,'0','','.') }}</td>
        </tr>
        @php
        $no++;
        @endphp
        @endforeach
    </tbody>
    <tr>
        <td colspan="2" class="font-weight-bold">TOTAL</td>
        <td align="right" class="text-right font-weight-bold">{{ number_format($totalduaminggu,'0','','.') }}</td>
        <td align="right" class="text-right font-weight-bold">{{ number_format($totalsatubulan,'0','','.') }}</td>
        <td align="right" class="text-right font-weight-bold">{{ number_format($totalsatubulan15,'0','','.') }}</td>
        <td align="right" class="text-right font-weight-bold">{{ number_format($totalduabulan,'0','','.') }}</td>
        <td align="right" class="text-right font-weight-bold">{{ number_format($totaltigabulan,'0','','.') }}</td>
        <td align="right" class="text-right font-weight-bold">{{ number_format($totalenambulan,'0','','.') }}</td>
        <td align="right" class="text-right font-weight-bold">{{ number_format($totallebihenambulan,'0','','.') }}</td>
    </tr>
</table>
