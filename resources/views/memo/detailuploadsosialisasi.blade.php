<table class="table table-bordered">
    <thead>
        <tr>
            <th>No.</th>
            <th>Cabang</th>
            <th>Link</th>
            <th>User</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sosialisasi as $d)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $d->kode_cabang }}</td>
            <td><a href="{{ $d->link }}"><i class="feather icon-link success"></i></a></td>
            <td>{{ $d->name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
