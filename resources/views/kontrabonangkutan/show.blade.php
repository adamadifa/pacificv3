<div class="row">
    <div class="col-12">
        <table class="table">
            <tr>
                <td>No. Kontrabon</td>
                <td>{{ $kontrabon->no_kontrabon }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>{{ DateToIndo2($kontrabon->tgl_kontrabon) }}</td>
            </tr>
            <tr>
                <td>Angkutan</td>
                <td>{{ $kontrabon->keterangan }}</td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-hover-animation">
            <thead class="thead-dark">
                <tr>
                    <th>No. SJ</th>
                    <th>Tanggal SJ</th>
                    <th>No. Polisi</th>
                    <th>Tujuan</th>
                    <th>Tarif</th>
                    <th>Tepung</th>
                    <th>BS</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                $grandtotal = 0;
                @endphp
                @foreach ($detail as $d)
                @php
                $total = $d->tarif + $d->tepung + $d->bs;
                $grandtotal += $total;
                @endphp
                <tr>
                    <td>{{ $d->no_surat_jalan }}</td>
                    <td>{{ date("d-m-y",strtotime($d->tgl_input)) }}</td>
                    <td>{{ $d->nopol }}</td>
                    <td>{{ $d->tujuan }}</td>
                    <td class="text-right">{{ rupiah($d->tarif) }}</td>
                    <td class="text-right">{{ rupiah($d->tepung) }}</td>
                    <td class="text-right">{{ rupiah($d->bs) }}</td>
                    <td class="text-right">{{ rupiah($total) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="7"></td>
                    <td class="text-right" style="font-weight: bold">{{ rupiah($grandtotal) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
