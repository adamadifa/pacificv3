<table class="table">
    <tr>
        <th>PIN</th>
        <th>Status Scan</th>
        <th>Scan Date</th>
    </tr>
    @foreach ($filtered_array as $d)
    <tr>
        <td>{{ $d->pin }}</td>
        <td>{{ $d->status_scan % 2 == 0 ? "IN" : "OUT"}}</td>
        <td>{{ date("d-m-Y H:i:s",strtotime($d->scan_date)) }}</td>
    </tr>
    @endforeach

</table>
