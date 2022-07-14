@php
$total=0;
@endphp
@foreach ($split as $d)
@php
$total += $d->jumlah
@endphp
<tr>
    <td>{{ $d->kode_akun }} {{ $d->nama_akun }}</td>
    <td>{{ $d->keterangan }}</td>
    <td style="text-align: right">{{ rupiah($d->jumlah) }}</td>
    <td>{{ $d->peruntukan }}</td>
    <td>
        <a href="#" class="hapus" data-id="{{ $d->id }}"><i class="feather icon-trash danger"></i></a>
    </td>
</tr>
@endforeach
<tr>
    <th colspan="2">TOTAL</th>
    <th style="text-align: right">{{ rupiah($total) }}</th>
    <th colspan="2"></th>
</tr>

<script>
    $(function() {
        function loadsplit() {
            var no_bukti = $("#id_kaskecil").val();
            $("#loadsplit").load('/kaskecil/' + no_bukti + '/showsplit');
        }
        $(".hapus").click(function(e) {
            e.preventDefault();
            let id = $(this).attr("data-id");
            $.ajax({
                type: 'POST'
                , url: '/kaskecil/deletesplit'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , id: id
                }
                , cache: false
                , success: function(respond) {
                    loadsplit();
                }
            });
        });
    });

</script>
