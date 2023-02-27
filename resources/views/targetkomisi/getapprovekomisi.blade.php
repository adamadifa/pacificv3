@foreach ($approvekomisi as $d)
<tr>
    <td>{{ $d->nama_cabang }}</td>
    <td>{!! $d->mm == null ? '<span class="badge bg-warning">Waiting</span>' : '<i class="fa fa-check success"></i>' !!}</td>
    <td>{!! $d->gm == null ? '<span class="badge bg-warning">Waiting</span>' : '<i class="fa fa-check success"></i>' !!}</td>
    <td>{!! $d->dirut == null ? '<span class="badge bg-warning">Waiting</span>' : '<i class="fa fa-check success"></i>' !!}</td>
    <td>
        <form action="/laporankomisi/cetak" target="_blank" method="POST">
            @csrf
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <input type="hidden" name="kode_cabang" value="{{ $d->kode_cabang }}">
            <input type="hidden" name="aturankomisi" value="2">
            <a href="#" class="cetakkomisi"><i class="feather icon-external-link info"></i></a>
        </form>
    </td>
</tr>
@endforeach

<script>
    $(function() {
        $(".cetakkomisi").click(function(e) {
            e.preventDefault();
            var form = $(this).closest("form");
            form.submit();
        });
    });

</script>
