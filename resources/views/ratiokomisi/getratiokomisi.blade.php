@foreach ($ratiokomisi as $d)
<tr>
    <td>{{$d->id_driver_helper}}</td>
    <td>{{$d->nama_driver_helper}}</td>
    <td>{{$d->kategori}}</td>
    <td>
        @php
        if (empty($d->ratioaktif)) {
        if (empty($d->ratioterakhir)) {
        $ratio = $d->ratio_default;
        } else {
        $ratio = $d->ratioterakhir;
        }
        } else {
        $ratio = $d->ratioaktif;
        }
        @endphp
        <input type="text" data-id="<?php echo $d->id_driver_helper ?>" class="form-control ratio" style="text-align: right;" value="<?php echo $ratio; ?>">
    </td>
</tr>
@endforeach
<script>
    $(function() {
        $(".ratio").keyup(function() {
            var tgl_berlaku = $("#tgl_berlaku").val();
            var ratio = $(this).val();
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            var id = $(this).attr("data-id");
            //alert(id);
            $.ajax({
                type: 'POST'
                , url: '/ratiokomisi/store'
                , data: {
                    _token: "{{csrf_token()}}"
                    , id: id
                    , ratio: ratio
                    , bulan: bulan
                    , tahun: tahun
                }
                , cache: false
                , success: function(respond) {

                }
            });
        });

    });

</script>
