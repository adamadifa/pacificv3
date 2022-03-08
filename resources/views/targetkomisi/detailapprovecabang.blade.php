<table class="table table-hover-animation">
    <thead>
        <tr>
            <th>Kode Cabang</th>
            <th>Status</td>

        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $d)
        <tr>
            <td>{{$d->kode_cabang}}</td>
            <td>
                @if ($d->kp > 0)
                <i class="fa fa-history warning"></i>
                @else
                <i class="fa fa-check success"></i>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
