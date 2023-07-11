<div class="row">
    <div class="col-12">
        <table class="table">
            <tr>
                <th colspan="3">Mesin 1</th>
            </tr>
            <tr>
                <th>PIN</th>
                <th>Status Scan</th>
                <th>Scan Date</th>
                <th>#</th>
            </tr>
            @foreach ($filtered_array as $d)
            <tr>
                <td>{{ $d->pin }}</td>
                <td>{{ $d->status_scan % 2 == 0 ? "IN" : "OUT"}} ({{ $d->status_scan }})</td>
                <td>{{ date("d-m-Y H:i:s",strtotime($d->scan_date)) }}</td>
                <td>
                    <div class="btn-group">
                        <a href="/presensi/{{ Crypt::encrypt($d->pin) }}/0/{{ date("Y-m-d H:i:s",strtotime($d->scan_date)) }}/updatefrommachine"> <i class="feather icon-log-in success mr-1"></i> Masuk </a>
                        <a href="/presensi/{{ Crypt::encrypt($d->pin) }}/1/{{ date("Y-m-d H:i:s",strtotime($d->scan_date)) }}/updatefrommachine"> <i class="feather icon-log-out danger mr-1"></i> Pulang </a>
                    </div>



                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <table class="table">
            <tr>
                <th colspan="3">Mesin 2</th>
            </tr>
            <tr>
                <th>PIN</th>
                <th>Status Scan</th>
                <th>Scan Date</th>
                <th>#</th>
            </tr>
            @foreach ($filtered_array_2 as $d)
            <tr>
                <td>{{ $d->pin }}</td>
                <td>{{ $d->status_scan % 2 == 0 ? "IN" : "OUT"}} ({{ $d->status_scan }})</td>
                <td>{{ date("d-m-Y H:i:s",strtotime($d->scan_date)) }}</td>
                <td>
                    <div class="btn-group">
                        <a href="/presensi/{{ Crypt::encrypt($d->pin) }}/0/{{ date("Y-m-d H:i:s",strtotime($d->scan_date)) }}/updatefrommachine"> <i class="feather icon-log-in success mr-1"></i> Masuk </a>
                        <a href="/presensi/{{ Crypt::encrypt($d->pin) }}/1/{{ date("Y-m-d H:i:s",strtotime($d->scan_date)) }}/updatefrommachine"> <i class="feather icon-log-out danger mr-1"></i> Pulang </a>
                    </div>
                </td>
            </tr>
            @endforeach

        </table>

    </div>
</div>
